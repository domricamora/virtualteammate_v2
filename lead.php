<?php
/**
 * Site-wide lead / contact capture endpoint.
 *
 * Every marketing form (homepage CTA, ROI reach-out, Virtual Teammates funnel,
 * etc.) posts here. Stores the lead in the portal DB (`leads` table, created on
 * demand) and emails the team a branded notification via the portal mailer.
 * Recipient is configurable in the portal Email page (app_settings
 * `lead_notify_email`, default nricamora@virtualteammate.com).
 *
 * Public + anonymous: no CSRF, guarded by a honeypot + validation. Always JSON.
 */
declare(strict_types=1);

// Buffer everything so a stray notice/whitespace from the bootstrap include
// can never corrupt the JSON body (which would make fetch().json() throw and
// the form appear to "do nothing"). All responses go through lead_respond().
ob_start();

function lead_respond(array $payload, int $code = 200): void
{
    while (ob_get_level() > 0) { ob_end_clean(); }
    http_response_code($code);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload);
    exit;
}

function lead_fail(string $msg, int $code = 400): void
{
    lead_respond(['ok' => false, 'error' => $msg], $code);
}

/**
 * Best-effort team notification email. Called AFTER the JSON response is sent,
 * so it can never delay or corrupt the form response. Recipient comes from the
 * portal's app_settings (lead_notify_email). Skipped on localhost (no relay).
 */
function lead_notify_team(PDO $pdo, array $lead): void
{
    try {
        // Skip only when there is no relay to send through: on localhost the
        // native mail() fallback can't deliver, but an authenticated SMTP relay
        // (portal/smtp.local.php) works from anywhere — so honor it everywhere.
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        $isLocal = $host === '' || str_contains($host, 'localhost') || str_starts_with($host, '127.0.0.1');
        if ($isLocal && lead_smtp_config() === null) { return; }

        $to = 'nricamora@virtualteammate.com';
        try {
            $st = $pdo->query("SELECT value FROM app_settings WHERE key = 'lead_notify_email'");
            $v  = $st ? trim((string) $st->fetchColumn()) : '';
            if ($v !== '') { $to = $v; }
        } catch (Throwable $_) {}
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) { return; }

        $who  = $lead['name'] !== '' ? $lead['name'] : $lead['email'];
        $text = "New website lead\n\n"
              . "Name: {$lead['name']}\nEmail: {$lead['email']}\nPhone: {$lead['phone']}\n"
              . "Company: {$lead['company']}\nSource: {$lead['source']}\nForm: {$lead['form']}\n"
              . ($lead['message'] !== '' ? "\nMessage:\n{$lead['message']}\n" : '');
        lead_send_mail($to, 'New lead: ' . $who, lead_email_html($lead), $text);
    } catch (Throwable $_) {}
}

/**
 * Outbound SMTP config, loaded once from portal/smtp.local.php (same file the
 * portal mailer uses). Returns the config array when host/user/pass are all
 * present, else null to fall back to native mail(). Never throws.
 */
function lead_smtp_config(): ?array
{
    static $cfg = false;                       // false = not loaded, null = none
    if ($cfg !== false) { return $cfg; }
    $cfg  = null;
    $file = __DIR__ . '/portal/smtp.local.php';
    if (is_file($file)) {
        try {
            $c = include $file;
            if (is_array($c) && !empty($c['host']) && !empty($c['user']) && !empty($c['pass'])) {
                $cfg = $c + ['port' => 587, 'from' => $c['user'], 'from_name' => 'Virtual Teammate'];
            }
        } catch (Throwable $_) { $cfg = null; }
    }
    return $cfg;
}

/**
 * Minimal STARTTLS + AUTH LOGIN SMTP relay (mirrors portal smtp_send). Returns
 * true only if the server accepted the message (250 at end-of-DATA). Never throws.
 */
function lead_smtp_send(array $cfg, string $from, string $to, string $message): bool
{
    $host = (string) ($cfg['host'] ?? '');
    $port = (int)    ($cfg['port'] ?? 587);
    $user = (string) ($cfg['user'] ?? '');
    $pass = (string) ($cfg['pass'] ?? '');

    $fp = @fsockopen($host, $port, $errno, $errstr, 15);
    if (!$fp) { error_log("lead_smtp_send: connect failed {$host}:{$port} — {$errstr}"); return false; }
    stream_set_timeout($fp, 15);

    $read = static function () use ($fp): int {
        $code = 0;
        do {
            $line = fgets($fp, 515);
            if ($line === false) { return 0; }
            $code = (int) substr($line, 0, 3);
        } while (isset($line[3]) && $line[3] === '-');
        return $code;
    };
    $cmd = static function (string $c) use ($fp): void { fwrite($fp, $c . "\r\n"); };

    $domain = ($d = strrchr($from, '@')) !== false ? substr($d, 1) : 'localhost';
    $ok = true;
    try {
        if ($read() !== 220) { throw new RuntimeException('no greeting'); }
        $cmd('EHLO ' . $domain);
        if ($read() !== 250) { throw new RuntimeException('EHLO rejected'); }
        $cmd('STARTTLS');
        if ($read() !== 220) { throw new RuntimeException('STARTTLS rejected'); }
        if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('TLS handshake failed');
        }
        $cmd('EHLO ' . $domain);
        if ($read() !== 250) { throw new RuntimeException('EHLO (TLS) rejected'); }
        $cmd('AUTH LOGIN');
        if ($read() !== 334) { throw new RuntimeException('AUTH LOGIN unsupported'); }
        $cmd(base64_encode($user));
        if ($read() !== 334) { throw new RuntimeException('username stage failed'); }
        $cmd(base64_encode($pass));
        if ($read() !== 235) { throw new RuntimeException('authentication failed'); }
        $cmd('MAIL FROM:<' . $from . '>');
        if ($read() !== 250) { throw new RuntimeException('MAIL FROM rejected'); }
        $cmd('RCPT TO:<' . $to . '>');
        $rc = $read();
        if ($rc !== 250 && $rc !== 251) { throw new RuntimeException('RCPT TO rejected'); }
        $cmd('DATA');
        if ($read() !== 354) { throw new RuntimeException('DATA rejected'); }
        fwrite($fp, preg_replace('/^\./m', '..', $message) . "\r\n.\r\n");
        if ($read() !== 250) { throw new RuntimeException('message not accepted'); }
        $cmd('QUIT');
    } catch (Throwable $e) {
        error_log('lead_smtp_send: ' . $e->getMessage());
        $ok = false;
    }
    @fclose($fp);
    return $ok;
}

/**
 * Self-contained multipart text+HTML mail (mirrors the portal mailer). Relays
 * through the authenticated SMTP server in portal/smtp.local.php when present;
 * otherwise falls back to native mail() (works only where the host has an MTA).
 */
function lead_send_mail(string $to, string $subject, string $html, string $text): bool
{
    $cfg      = lead_smtp_config();
    $from     = $cfg !== null ? (string) $cfg['from'] : 'support@virtualteammate.com';
    $fromName = $cfg !== null ? (string) ($cfg['from_name'] ?? 'Virtual Teammate') : 'Virtual Teammate';
    $subject  = preg_replace('/\s+/', ' ', trim($subject));
    $boundary = 'vtlead_' . bin2hex(random_bytes(8));
    $bodyPart = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n{$text}\r\n"
              . "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n{$html}\r\n"
              . "--{$boundary}--";

    // ── Authenticated SMTP relay (preferred when configured) ──
    if ($cfg !== null) {
        $domain  = ($d = strrchr($from, '@')) !== false ? substr($d, 1) : 'virtualteammate.com';
        $message = 'From: ' . $fromName . ' <' . $from . ">\r\n"
                 . 'To: ' . $to . "\r\n"
                 . 'Reply-To: ' . $from . "\r\n"
                 . 'Subject: ' . $subject . "\r\n"
                 . 'Date: ' . gmdate('D, d M Y H:i:s') . " +0000\r\n"
                 . 'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $domain . ">\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . 'Content-Type: multipart/alternative; boundary="' . $boundary . "\"\r\n"
                 . "X-Mailer: VT Lead Capture\r\n\r\n"
                 . $bodyPart;
        return lead_smtp_send($cfg, $from, $to, $message);
    }

    // ── Fallback: native mail() ──
    $headers = implode("\r\n", [
        'From: ' . $fromName . ' <' . $from . '>',
        'Reply-To: ' . $from,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'X-Mailer: VT Lead Capture',
    ]);
    try { return @mail($to, $subject, $bodyPart, $headers, '-f' . $from); }
    catch (Throwable $_) { return false; }
}

/** Branded HTML body for the team lead-notification email. */
function lead_email_html(array $lead): string
{
    $e    = static fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    $base = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
          . '://' . ($_SERVER['HTTP_HOST'] ?? 'virtualteammate.com');
    $rows = '';
    foreach ([['Name','name'],['Email','email'],['Phone','phone'],['Company','company'],['Source','source'],['Form','form'],['IP','ip']] as $r) {
        $val = trim((string) ($lead[$r[1]] ?? ''));
        if ($val === '') { continue; }
        $rows .= '<tr><td style="padding:6px 14px 6px 0;color:#8a8aa0;font-size:13px;white-space:nowrap;">' . $e($r[0]) . '</td>'
               . '<td style="padding:6px 0;color:#15123a;font-size:14px;font-weight:600;">' . $e($val) . '</td></tr>';
    }
    $msg      = trim((string) ($lead['message'] ?? ''));
    $msgBlock = $msg !== '' ? '<p style="margin:18px 0 0;font-size:13.5px;color:#333;line-height:1.55;"><strong>Message:</strong><br>' . nl2br($e($msg)) . '</p>' : '';
    $who      = $lead['name'] !== '' ? $lead['name'] : $lead['email'];
    return '<!doctype html><html><body style="margin:0;background:#f4f4f8;padding:24px;font-family:Manrope,Arial,Helvetica,sans-serif;">'
        . '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:560px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(20,15,55,.12);">'
        . '<tr><td style="background:linear-gradient(135deg,#3919BA 0%,#7c3aed 100%);padding:22px 28px;">'
        . '<div style="color:rgba(255,255,255,.72);font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;">New Lead</div>'
        . '<div style="color:#fff;font-size:20px;font-weight:800;margin-top:4px;">' . $e($who) . '</div></td></tr>'
        . '<tr><td style="padding:24px 28px;">'
        . '<p style="margin:0 0 14px;font-size:14px;color:#333;">A new lead just came in from the website:</p>'
        . '<table cellpadding="0" cellspacing="0" border="0">' . $rows . '</table>' . $msgBlock
        . '<table cellpadding="0" cellspacing="0" border="0" align="left" style="margin:22px 0 4px;"><tr>'
        . '<td bgcolor="#3919BA" style="border-radius:10px;background:linear-gradient(135deg,#3919BA,#7c3aed);">'
        . '<a href="' . $e($base) . '/portal/?p=leads" style="display:inline-block;padding:13px 24px;font-family:Manrope,Arial,sans-serif;font-size:14px;font-weight:800;color:#fff;text-decoration:none;border-radius:10px;">View in portal &rarr;</a>'
        . '</td></tr></table><div style="clear:both;"></div></td></tr>'
        . '<tr><td style="padding:14px 28px 22px;border-top:1px solid #eee;color:#9a9ab0;font-size:11.5px;">Sent automatically because a lead form was submitted on virtualteammate.com.</td></tr>'
        . '</table></body></html>';
}

/**
 * Push the lead to HubSpot as a contact (upsert by email) using the portal's
 * private-app token (app_settings.hs_token). Called AFTER the JSON response so it
 * never delays the form. Best-effort: every failure is swallowed. Skipped on
 * localhost and when no token is configured. Dedupes by email on HubSpot's side.
 */
function lead_push_hubspot(PDO $pdo, array $lead): void
{
    try {
        if (!function_exists('curl_init')) { return; }
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '' || str_contains($host, 'localhost') || str_starts_with($host, '127.0.0.1')) { return; }

        $token = '';
        try {
            $st = $pdo->query("SELECT value FROM app_settings WHERE key = 'hs_token'");
            $token = trim((string) ($st ? $st->fetchColumn() : ''));
        } catch (Throwable $_) {}
        if ($token === '') { return; }

        $email = trim((string) ($lead['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { return; }

        // Only standard contact properties (present in every HubSpot portal) so an
        // unknown-property error can never reject the whole upsert.
        $props = ['email' => $email, 'lifecyclestage' => 'lead'];
        foreach (['firstname' => 'first', 'lastname' => 'last', 'phone' => 'phone', 'company' => 'company'] as $hsKey => $leadKey) {
            $v = trim((string) ($lead[$leadKey] ?? ''));
            if ($v !== '') { $props[$hsKey] = $v; }
        }
        $body = json_encode(['properties' => $props]);

        // Upsert: update by email; if the contact doesn't exist (404), create it.
        $status = lead_hubspot_call('PATCH',
            'https://api.hubapi.com/crm/v3/objects/contacts/' . rawurlencode($email) . '?idProperty=email',
            $token, $body);
        if ($status === 404) {
            lead_hubspot_call('POST', 'https://api.hubapi.com/crm/v3/objects/contacts', $token, $body);
        }
    } catch (Throwable $_) {}
}

/** Minimal HubSpot API call. Returns the HTTP status (0 on transport error). */
function lead_hubspot_call(string $method, string $url, string $token, string $body): int
{
    $ch = curl_init($url);
    $opts = [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
    ];
    // Ship-with-the-app CA bundle so SSL verification works on hosts whose default
    // bundle is missing/stale (the portal already relies on this file).
    $ca = __DIR__ . '/portal/cacert.pem';
    if (is_file($ca)) { $opts[CURLOPT_CAINFO] = $ca; }
    curl_setopt_array($ch, $opts);
    curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $status;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { lead_fail('Method not allowed.', 405); }

// Honeypot — bots fill this hidden field; pretend success so they don't retry.
// NOTE: named "vt_hp" (not a company/email/url-like token) so Chrome & password
// managers can't classify it and never autofill it for real visitors — an
// autofilled honeypot would silently drop a genuine lead. The legacy
// "company_site" name is still honored for any cached/older page.
if (trim((string) ($_POST['vt_hp'] ?? '')) !== '' || trim((string) ($_POST['company_site'] ?? '')) !== '') { lead_respond(['ok' => true]); }

/* ── Collect fields generically ── */
$control = ['vt_hp' => 1, 'company_site' => 1, '_csrf' => 1];
$fields  = [];
foreach ($_POST as $k => $v) {
    if (isset($control[$k]) || !is_string($v)) { continue; }
    $v = trim($v);
    if ($v !== '') { $fields[$k] = mb_substr($v, 0, 2000); }
}

$pick = static function (array $f, array $keys): string {
    foreach ($keys as $k) { if (!empty($f[$k])) { return $f[$k]; } }
    return '';
};

$first = $pick($fields, ['first_name', 'firstname']);
$last  = $pick($fields, ['last_name', 'lastname']);
$name  = trim($first . ' ' . $last) ?: $pick($fields, ['name', 'full_name']);
$email = $pick($fields, ['email']);
$phone = $pick($fields, ['phone', 'tel']);
$company = $pick($fields, ['company', 'practice', 'clinic', 'organization', 'role', 'practice_name']);
$message = $pick($fields, ['message', 'notes', 'comments']);
$source  = $pick($fields, ['source']) ?: 'website';
$form    = $pick($fields, ['form']) ?: $source;
$vtId    = (int) ($fields['vt_id'] ?? 0);
$vtName  = $pick($fields, ['vt_interest']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { lead_fail('Please enter a valid email address.'); }

// Build a complete, human-readable dump of everything submitted (nothing lost).
$labels = [
    'first_name' => 'First name', 'last_name' => 'Last name', 'name' => 'Name',
    'email' => 'Email', 'phone' => 'Phone', 'company' => 'Company', 'practice' => 'Practice',
    'clinic' => 'Clinic', 'organization' => 'Organization', 'role' => 'Role', 'intent' => 'Intent',
    'source' => 'Source', 'source_other' => 'Source (other)', 'message' => 'Message',
    'vt_interest' => 'Interested in', 'vt_id' => 'VT id', 'form' => 'Form',
];
$detailLines = [];
foreach ($fields as $k => $v) {
    $detailLines[$k] = ($labels[$k] ?? ucwords(str_replace('_', ' ', $k))) . ': ' . $v;
}
$details = implode("\n", $detailLines);

/* ── Persist to the leads table (direct SQLite — NO mail, no bootstrap, so the
 *    response is instant). Lead notifications by email are intentionally off. ── */
$dbPath = __DIR__ . '/data/portal.sqlite';
if (!is_file($dbPath)) { lead_fail('Lead capture is temporarily unavailable.', 503); }

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL DEFAULT '', email TEXT NOT NULL DEFAULT '',
            phone TEXT NOT NULL DEFAULT '', company TEXT NOT NULL DEFAULT '',
            message TEXT NOT NULL DEFAULT '', source TEXT NOT NULL DEFAULT '',
            form TEXT NOT NULL DEFAULT '', vt_id INTEGER NOT NULL DEFAULT 0,
            vt_interest TEXT NOT NULL DEFAULT '', details TEXT NOT NULL DEFAULT '',
            ip TEXT NOT NULL DEFAULT '', created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )"
    );
    $pdo->prepare(
        "INSERT INTO leads (name,email,phone,company,message,source,form,vt_id,vt_interest,details,ip)
         VALUES (:n,:e,:p,:c,:m,:s,:f,:vid,:vn,:d,:ip)"
    )->execute([
        ':n' => mb_substr($name, 0, 160), ':e' => mb_substr($email, 0, 160),
        ':p' => mb_substr($phone, 0, 40), ':c' => mb_substr($company, 0, 160),
        ':m' => $message, ':s' => mb_substr($source, 0, 60), ':f' => mb_substr($form, 0, 60),
        ':vid' => $vtId, ':vn' => mb_substr($vtName, 0, 160), ':d' => $details,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);
} catch (Throwable $_) {
    lead_fail('Could not save your request — please try again.', 500);
}

// Success. Respond EXACTLY like lead_respond — NO manual Content-Length, so
// Apache's gzip sets its own length (a manual one previously corrupted the
// gzipped body and broke the form). Then email the team, AFTER the client has
// the response, so a slow relay can never delay or corrupt it.
while (ob_get_level() > 0) { ob_end_clean(); }
http_response_code(200);
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['ok' => true]);

$leadForMail = [
    'name' => $name, 'email' => $email, 'phone' => $phone, 'company' => $company,
    'source' => $source, 'form' => $form, 'message' => $message,
    'first' => $first, 'last' => $last,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
];
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();          // client gets the response now; rest in bg
    lead_notify_team($pdo, $leadForMail);
    lead_push_hubspot($pdo, $leadForMail);
} else {
    // No FPM: body is sent at script end. Capture+discard any stray output so it
    // can never append to (corrupt) the JSON response.
    ob_start();
    lead_notify_team($pdo, $leadForMail);
    lead_push_hubspot($pdo, $leadForMail);
    ob_end_clean();
}
exit;
