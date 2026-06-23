<?php
/**
 * Minimal, dependency-free Web Push (RFC 8030 / 8188 / 8291 + VAPID RFC 8292).
 *
 * Pure PHP on top of ext-openssl + hash_hkdf — no Composer, nothing to install
 * on the host. Generates a VAPID keypair once (stored in app_settings),
 * aes128gcm-encrypts the payload to a subscription, signs an ES256 VAPID JWT,
 * and POSTs to the push service.
 *
 * Public API:
 *   webpush_public_key()                 -> base64url applicationServerKey for the client
 *   webpush_send($sub, $payload, $opts)  -> ['ok'=>bool,'status'=>int,'error'=>?string]
 *       $sub = ['endpoint'=>..., 'p256dh'=>base64url, 'auth'=>base64url]
 *
 * Requires bootstrap.php loaded first (uses get_setting/set_setting + cacert).
 */
declare(strict_types=1);

/** Create the subscriptions table on demand (so no install re-run is needed). */
function push_ensure_table(PDO $db): void
{
    $db->exec(
        'CREATE TABLE IF NOT EXISTS push_subscriptions (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id      INTEGER NOT NULL,
            endpoint     TEXT    NOT NULL UNIQUE,
            p256dh       TEXT    NOT NULL,
            auth         TEXT    NOT NULL,
            user_agent   TEXT,
            created_at   TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_used_at TEXT,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )'
    );
    $db->exec('CREATE INDEX IF NOT EXISTS idx_push_subs_user ON push_subscriptions(user_id)');
}

/* ───────────── base64url ───────────── */
function wp_b64u_encode(string $bin): string
{
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}
function wp_b64u_decode(string $s): string
{
    $s = strtr($s, '-_', '+/');
    $pad = strlen($s) % 4;
    if ($pad) { $s .= str_repeat('=', 4 - $pad); }
    return (string) base64_decode($s, true);
}

/* ───────────── openssl helpers (Windows needs an explicit openssl.cnf) ───────────── */
function wp_openssl_configs(): array
{
    $list = [];
    $env = getenv('OPENSSL_CONF');
    if ($env) { $list[] = $env; }
    $list[] = dirname(PHP_BINARY) . '/extras/ssl/openssl.cnf';
    $list[] = dirname(PHP_BINARY) . '/extras/openssl/openssl.cnf';
    $list[] = '/usr/lib/ssl/openssl.cnf';
    $list[] = '/etc/ssl/openssl.cnf';
    $list[] = '/etc/pki/tls/openssl.cnf';
    return array_values(array_filter($list, 'is_file'));
}

/** Generate a fresh P-256 keypair, retrying with a discovered config on Windows. */
function wp_ec_new()
{
    $args = ['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1'];
    $k = @openssl_pkey_new($args);
    if ($k) { return $k; }
    foreach (wp_openssl_configs() as $cnf) {
        $k = @openssl_pkey_new($args + ['config' => $cnf]);
        if ($k) { return $k; }
    }
    return false;
}

/** Export a private key to PEM, with the same config fallback. */
function wp_pkey_export($key): ?string
{
    $out = '';
    if (@openssl_pkey_export($key, $out)) { return $out; }
    foreach (wp_openssl_configs() as $cnf) {
        if (@openssl_pkey_export($key, $out, null, ['config' => $cnf])) { return $out; }
    }
    return null;
}

/** Raw 65-byte uncompressed point (0x04 || X || Y) from an EC key's details. */
function wp_point_from_details(array $d): string
{
    $x = str_pad($d['ec']['x'], 32, "\x00", STR_PAD_LEFT);
    $y = str_pad($d['ec']['y'], 32, "\x00", STR_PAD_LEFT);
    return "\x04" . $x . $y;
}

/** Wrap a raw P-256 public point into a PEM SubjectPublicKeyInfo (no config needed). */
function wp_public_pem_from_point(string $point): string
{
    // Fixed ASN.1 prefix for an id-ecPublicKey / prime256v1 SPKI, then the point.
    $der = "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01"
         . "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42\x00" . $point;
    return "-----BEGIN PUBLIC KEY-----\r\n"
         . chunk_split(base64_encode($der), 64, "\r\n")
         . "-----END PUBLIC KEY-----\r\n";
}

/* ───────────── VAPID keypair (generated once, cached in app_settings) ───────────── */
function webpush_vapid(): ?array
{
    $priv = get_setting('vapid_private', '');
    $pub  = get_setting('vapid_public', '');
    $sub  = get_setting('vapid_subject', '');

    if ($priv === '' || $pub === '') {
        $k = wp_ec_new();
        if (!$k) { return null; }
        $pem = wp_pkey_export($k);
        $det = openssl_pkey_get_details($k);
        if (!$pem || !$det) { return null; }
        $priv = $pem;
        $pub  = wp_b64u_encode(wp_point_from_details($det));
        set_setting('vapid_private', $priv);
        set_setting('vapid_public', $pub);
    }
    if ($sub === '') {
        $sub = 'mailto:nricamora@virtualteammate.com';
        set_setting('vapid_subject', $sub);
    }
    return ['private' => $priv, 'public' => $pub, 'subject' => $sub];
}

/** base64url applicationServerKey for PushManager.subscribe(). */
function webpush_public_key(): ?string
{
    $v = webpush_vapid();
    return $v['public'] ?? null;
}

/* ───────────── ECDSA DER → raw R||S (JWS ES256 wants 64 raw bytes) ───────────── */
function wp_der_to_raw_sig(string $der): string
{
    $off = 0;
    if (($der[$off++] ?? '') !== "\x30") { return ''; }   // SEQUENCE
    // length (skip; may be long-form for <128 it's short)
    $len = ord($der[$off++]);
    if ($len & 0x80) { $off += ($len & 0x7f); }
    $read = static function (string $der, int &$off): string {
        if (($der[$off++] ?? '') !== "\x02") { return ''; } // INTEGER
        $l = ord($der[$off++]);
        $v = substr($der, $off, $l);
        $off += $l;
        $v = ltrim($v, "\x00");                              // strip sign byte
        return str_pad($v, 32, "\x00", STR_PAD_LEFT);        // left-pad to 32
    };
    $r = $read($der, $off);
    $s = $read($der, $off);
    return $r . $s;
}

/** Build + sign a VAPID JWT for the push service origin $aud. */
function wp_vapid_jwt(string $aud, array $v): ?string
{
    $header  = ['typ' => 'JWT', 'alg' => 'ES256'];
    $payload = ['aud' => $aud, 'exp' => time() + 12 * 3600, 'sub' => $v['subject']];
    $signing = wp_b64u_encode((string) json_encode($header)) . '.'
             . wp_b64u_encode((string) json_encode($payload));

    $pk = openssl_pkey_get_private($v['private']);
    if (!$pk) { return null; }
    $der = '';
    if (!openssl_sign($signing, $der, $pk, OPENSSL_ALGO_SHA256)) { return null; }
    $raw = wp_der_to_raw_sig($der);
    if (strlen($raw) !== 64) { return null; }
    return $signing . '.' . wp_b64u_encode($raw);
}

/* ───────────── aes128gcm payload encryption (RFC 8291 + 8188) ───────────── */
function wp_encrypt(string $payload, string $ua_public, string $auth): ?array
{
    $as = wp_ec_new();                                  // ephemeral keypair
    if (!$as) { return null; }
    $asDet = openssl_pkey_get_details($as);
    if (!$asDet) { return null; }
    $as_public = wp_point_from_details($asDet);

    $uaKey = openssl_pkey_get_public(wp_public_pem_from_point($ua_public));
    if (!$uaKey) { return null; }
    $secret = openssl_pkey_derive($uaKey, $as);         // 32-byte ECDH shared secret
    if (!$secret) { return null; }

    // RFC 8291 §3.4: derive the input keying material.
    $keyInfo = "WebPush: info\x00" . $ua_public . $as_public;
    $ikm = hash_hkdf('sha256', $secret, 32, $keyInfo, $auth);

    // RFC 8188 content encryption keys, salted per record.
    $salt  = random_bytes(16);
    $cek   = hash_hkdf('sha256', $ikm, 16, "Content-Encoding: aes128gcm\x00", $salt);
    $nonce = hash_hkdf('sha256', $ikm, 12, "Content-Encoding: nonce\x00", $salt);

    // Single record: plaintext || 0x02 padding-delimiter.
    $tag = '';
    $ct  = openssl_encrypt($payload . "\x02", 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, '', 16);
    if ($ct === false) { return null; }

    // aes128gcm header: salt(16) | rs(4) | idlen(1) | keyid(=as_public,65)
    $rs     = 4096;
    $header = $salt . pack('N', $rs) . chr(strlen($as_public)) . $as_public;

    return ['body' => $header . $ct . $tag];
}

/* ───────────── send ───────────── */
function webpush_send(array $sub, string $payload, array $opts = []): array
{
    $endpoint = (string) ($sub['endpoint'] ?? '');
    if ($endpoint === '' || empty($sub['p256dh']) || empty($sub['auth'])) {
        return ['ok' => false, 'status' => 0, 'error' => 'invalid subscription'];
    }
    $v = webpush_vapid();
    if (!$v) { return ['ok' => false, 'status' => 0, 'error' => 'vapid unavailable (openssl?)']; }

    $parts = parse_url($endpoint);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
        return ['ok' => false, 'status' => 0, 'error' => 'bad endpoint'];
    }
    $aud = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');

    $enc = wp_encrypt($payload, wp_b64u_decode((string) $sub['p256dh']), wp_b64u_decode((string) $sub['auth']));
    if (!$enc) { return ['ok' => false, 'status' => 0, 'error' => 'encrypt failed']; }

    $jwt = wp_vapid_jwt($aud, $v);
    if (!$jwt) { return ['ok' => false, 'status' => 0, 'error' => 'jwt failed']; }

    $headers = [
        'Authorization: vapid t=' . $jwt . ', k=' . $v['public'],
        'Content-Encoding: aes128gcm',
        'Content-Type: application/octet-stream',
        'TTL: ' . (int) ($opts['ttl'] ?? 2419200),
        'Urgency: ' . (string) ($opts['urgency'] ?? 'normal'),
    ];

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $enc['body'],
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CAINFO         => __DIR__ . '/cacert.pem',
    ]);
    $resp   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err    = curl_error($ch);
    curl_close($ch);

    if ($status >= 200 && $status < 300) {
        return ['ok' => true, 'status' => $status, 'error' => null];
    }
    return ['ok' => false, 'status' => $status, 'error' => $err ?: (is_string($resp) ? substr($resp, 0, 200) : 'http ' . $status)];
}
