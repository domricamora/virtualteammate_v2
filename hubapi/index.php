<?php
/**
 * HubSpot webhook receiver — https://virtualteammate.com/hubapi
 *
 * HubSpot POSTs a JSON array of events (contact/deal property changes, new
 * contacts, etc.). We store them in the portal DB (`hubspot_events`, created on
 * demand) so the super-admin Funnel page can show recent activity, then 200 fast.
 *
 * Best-effort signature check: if app_settings.hs_webhook_secret is set we verify
 * HubSpot's v3 signature; otherwise we accept (the data is monitoring-only and the
 * receiver takes no destructive action). Always returns quickly.
 */
declare(strict_types=1);

http_response_code(200);
header('Content-Type: application/json; charset=UTF-8');

// HubSpot only sends POST. GET = a health check (e.g. the user opening the URL).
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    echo json_encode(['ok' => true, 'endpoint' => 'hubspot-webhook']);
    exit;
}

$raw = file_get_contents('php://input') ?: '';
$dbPath = __DIR__ . '/../data/portal.sqlite';

try {
    if (!is_file($dbPath)) { echo json_encode(['ok' => true]); exit; }
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional v3 signature validation (only if a secret is configured).
    try {
        $secret = '';
        $st = $pdo->query("SELECT value FROM app_settings WHERE key = 'hs_webhook_secret'");
        $secret = trim((string) ($st ? $st->fetchColumn() : ''));
        if ($secret !== '') {
            $sig = $_SERVER['HTTP_X_HUBSPOT_SIGNATURE_V3'] ?? '';
            $ts  = $_SERVER['HTTP_X_HUBSPOT_REQUEST_TIMESTAMP'] ?? '';
            $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $uri = $proto . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
            $base = 'POST' . $uri . $raw . $ts;
            $expected = base64_encode(hash_hmac('sha256', $base, $secret, true));
            if (!hash_equals($expected, (string) $sig)) { echo json_encode(['ok' => true]); exit; }
        }
    } catch (Throwable $_) {}

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS hubspot_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_id TEXT NOT NULL DEFAULT '',
            subscription_type TEXT NOT NULL DEFAULT '',
            object_id TEXT NOT NULL DEFAULT '',
            property_name TEXT NOT NULL DEFAULT '',
            property_value TEXT NOT NULL DEFAULT '',
            change_source TEXT NOT NULL DEFAULT '',
            occurred_at TEXT NOT NULL DEFAULT '',
            raw TEXT NOT NULL DEFAULT '',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )"
    );
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_hsev_created ON hubspot_events(created_at)");

    $events = json_decode($raw, true);
    if (is_array($events)) {
        // HubSpot may send a single object or an array of events.
        if (isset($events['subscriptionType']) || isset($events['eventId'])) { $events = [$events]; }
        $ins = $pdo->prepare(
            "INSERT INTO hubspot_events
                (event_id, subscription_type, object_id, property_name, property_value, change_source, occurred_at, raw)
             VALUES (:eid,:st,:oid,:pn,:pv,:cs,:oa,:raw)"
        );
        foreach (array_slice($events, 0, 100) as $e) {
            if (!is_array($e)) { continue; }
            $occ = isset($e['occurredAt']) ? gmdate('c', (int) ($e['occurredAt'] / 1000)) : '';
            $ins->execute([
                ':eid' => (string) ($e['eventId'] ?? ''),
                ':st'  => (string) ($e['subscriptionType'] ?? ''),
                ':oid' => (string) ($e['objectId'] ?? ''),
                ':pn'  => (string) ($e['propertyName'] ?? ''),
                ':pv'  => mb_substr((string) ($e['propertyValue'] ?? ''), 0, 255),
                ':cs'  => (string) ($e['changeSource'] ?? ''),
                ':oa'  => $occ,
                ':raw' => mb_substr($raw === '' ? json_encode($e) : json_encode($e), 0, 4000),
            ]);
        }
        // Keep the table bounded.
        $pdo->exec("DELETE FROM hubspot_events WHERE id < (SELECT MAX(id) - 5000 FROM hubspot_events)");
    }
} catch (Throwable $_) {
    // Never error back to HubSpot — it would retry and disable the subscription.
}

echo json_encode(['ok' => true]);
