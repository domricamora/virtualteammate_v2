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

header('Content-Type: application/json; charset=UTF-8');

function lead_fail(string $msg, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { lead_fail('Method not allowed.', 405); }

// Honeypot — bots fill this hidden field; pretend success so they don't retry.
if (trim((string) ($_POST['company_site'] ?? '')) !== '') { echo json_encode(['ok' => true]); exit; }

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

/* ── Persist + notify via the portal stack ── */
$bootstrap = __DIR__ . '/portal/bootstrap.php';
if (!is_file($bootstrap)) { lead_fail('Lead capture is temporarily unavailable.', 503); }
require $bootstrap;

try {
    $pdo = db();
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

$who = $name !== '' ? $name : $email;

/* ── Branded email to the configured recipient ── */
try {
    if (function_exists('portal_email_shell') && function_exists('portal_send_mail')) {
        $recipient = function_exists('get_setting') ? trim(get_setting('lead_notify_email', '')) : '';
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) { $recipient = 'nricamora@virtualteammate.com'; }

        $clean = static fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
        $bodyHtml = '<table cellpadding="0" cellspacing="0" border="0" style="font-family:Manrope,Arial,sans-serif;font-size:14px;color:#1a1535;">';
        foreach ($fields as $k => $v) {
            $lbl = $labels[$k] ?? ucwords(str_replace('_', ' ', $k));
            $bodyHtml .= '<tr><td style="padding:4px 14px 4px 0;color:#6b6588;font-weight:700;vertical-align:top;white-space:nowrap;">' . $clean($lbl) . '</td>'
                      .  '<td style="padding:4px 0;">' . nl2br($clean($v)) . '</td></tr>';
        }
        $bodyHtml .= '</table>';
        $footer = 'Captured from the website (' . $clean($form) . ') — reply directly to ' . $clean($email) . '.';
        $html = portal_email_shell('New lead from the website', $bodyHtml, '', $footer, 'New lead');
        $text = "New lead from the website (" . $form . ")\n\n" . $details;
        portal_send_mail($recipient, 'New lead: ' . $who, $html, $text);
    }
} catch (Throwable $_) { /* email failure must never fail the capture */ }

/* ── In-app notification to active super admins (also emails them) ── */
try {
    if (function_exists('notify')) {
        $body = $who . ' · ' . $email . ($vtName !== '' ? ' · interested in ' . $vtName : '') . ' · ' . $form;
        foreach (db()->query("SELECT id FROM users WHERE role = 'super_admin' AND active = 1") as $row) {
            notify((int) $row['id'], 'info', 'New lead: ' . $who, $body, '');
        }
    }
} catch (Throwable $_) { /* non-fatal */ }

echo json_encode(['ok' => true]);
