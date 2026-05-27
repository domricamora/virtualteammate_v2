<?php
/**
 * Lightweight traffic beacon receiver.
 *
 * The marketing pages fire a non-blocking beacon here AFTER load (see the
 * tracker in includes/footer.php), so logging never slows page render.
 *
 * Logs one row per pageview into the portal SQLite DB (data/portal.sqlite):
 * path, client IP, geolocation (country / region / city) and user agent.
 * Geo is resolved via ip-api.com and cached per-IP so repeat visitors don't
 * trigger another lookup. If the portal DB doesn't exist (e.g. the portal
 * isn't installed in this environment) the beacon silently no-ops.
 *
 * Always returns 204 No Content — the browser ignores the body.
 */

declare(strict_types=1);

// Respond instantly; do the work, then 204. Keep it cheap and never fatal.
ignore_user_abort(true);

const TRACK_DB = __DIR__ . '/data/portal.sqlite';

function track_done(): void
{
    if (!headers_sent()) {
        http_response_code(204);
    }
    exit;
}

/** Best-effort client IP, honoring common proxy / CDN headers. */
function track_client_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }
        // X-Forwarded-For can be a comma list; the first entry is the origin client.
        $candidate = trim(explode(',', (string) $_SERVER[$key])[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            return $candidate;
        }
    }
    return '';
}

/** Resolve geo for an IP, using the geo_cache table. Returns [country, region, city]. */
function track_geo(PDO $pdo, string $ip): array
{
    if ($ip === '') {
        return ['', '', ''];
    }

    // Private / loopback ranges never resolve — tag them as Local.
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return ['Local', '', ''];
    }

    // Cache hit?
    $stmt = $pdo->prepare('SELECT country, region, city FROM geo_cache WHERE ip = :ip LIMIT 1');
    $stmt->execute([':ip' => $ip]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return [(string) $row['country'], (string) $row['region'], (string) $row['city']];
    }

    // Miss — look it up (HTTP is fine here, ip-api free tier is HTTP-only).
    $country = $region = $city = '';
    $lat = $lon = null;
    $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,country,regionName,city,lat,lon';
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 3,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_USERAGENT      => 'VT-Traffic/1.0',
    ]);
    $raw  = curl_exec($ch);
    $errn = curl_errno($ch);
    curl_close($ch);

    if ($errn === 0 && is_string($raw)) {
        $data = json_decode($raw, true);
        if (is_array($data) && ($data['status'] ?? '') === 'success') {
            $country = (string) ($data['country'] ?? '');
            $region  = (string) ($data['regionName'] ?? '');
            $city    = (string) ($data['city'] ?? '');
            $lat     = isset($data['lat']) ? (float) $data['lat'] : null;
            $lon     = isset($data['lon']) ? (float) $data['lon'] : null;
        }
    }

    // Cache the result (even an empty one, to avoid hammering the API on failures).
    try {
        $pdo->prepare(
            'INSERT INTO geo_cache (ip, country, region, city, lat, lon)
             VALUES (:ip, :c, :r, :ci, :lat, :lon)
             ON CONFLICT(ip) DO UPDATE SET country=excluded.country, region=excluded.region,
                                           city=excluded.city, lat=excluded.lat, lon=excluded.lon,
                                           resolved_at=CURRENT_TIMESTAMP'
        )->execute([':ip' => $ip, ':c' => $country, ':r' => $region, ':ci' => $city, ':lat' => $lat, ':lon' => $lon]);
    } catch (Throwable $_) {
        // ignore cache write failures
    }

    return [$country, $region, $city];
}

try {
    if (!file_exists(TRACK_DB)) {
        track_done();
    }

    $pdo = new PDO('sqlite:' . TRACK_DB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA busy_timeout = 2000');

    // Defensive: ensure the tables exist even if the server DB predates them.
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS traffic (
            id INTEGER PRIMARY KEY AUTOINCREMENT, path TEXT NOT NULL DEFAULT "", ip TEXT NOT NULL DEFAULT "",
            country TEXT NOT NULL DEFAULT "", region TEXT NOT NULL DEFAULT "", city TEXT NOT NULL DEFAULT "",
            user_agent TEXT NOT NULL DEFAULT "", referrer TEXT NOT NULL DEFAULT "",
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );'
    );
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS geo_cache (
            ip TEXT PRIMARY KEY, country TEXT NOT NULL DEFAULT "", region TEXT NOT NULL DEFAULT "",
            city TEXT NOT NULL DEFAULT "", lat REAL, lon REAL, resolved_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );'
    );

    // Inputs (sendBeacon posts JSON, fetch sends query params — accept both).
    $path = (string) ($_POST['p'] ?? $_GET['p'] ?? '');
    $ref  = (string) ($_POST['r'] ?? $_GET['r'] ?? ($_SERVER['HTTP_REFERER'] ?? ''));
    if ($path === '' && $ref !== '') {
        $path = parse_url($ref, PHP_URL_PATH) ?: '/';
    }
    $path = substr($path, 0, 300);
    $ref  = substr($ref, 0, 500);
    $ua   = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 400);
    $ip   = track_client_ip();

    [$country, $region, $city] = track_geo($pdo, $ip);

    $pdo->prepare(
        'INSERT INTO traffic (path, ip, country, region, city, user_agent, referrer)
         VALUES (:path, :ip, :country, :region, :city, :ua, :ref)'
    )->execute([
        ':path' => $path, ':ip' => $ip, ':country' => $country, ':region' => $region,
        ':city' => $city, ':ua' => $ua, ':ref' => $ref,
    ]);
} catch (Throwable $_) {
    // Tracking must never surface an error to the visitor.
}

track_done();
