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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { lead_fail('Method not allowed.', 405); }

// Honeypot — bots fill this hidden field; pretend success so they don't retry.
if (trim((string) ($_POST['company_site'] ?? '')) !== '') { lead_respond(['ok' => true]); }

/* ── Collect fields generically ── */
$control = ['company_site' => 1, '_csrf' => 1];
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

lead_respond(['ok' => true]);
