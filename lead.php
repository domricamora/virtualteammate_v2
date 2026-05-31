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
 * Flush a clean JSON success to the visitor, close the connection, THEN email
 * the team — so the form feels instant even if the mail relay is slow. The
 * recipient comes from the portal's app_settings (lead_notify_email). Native
 * mail() so we stay independent of the portal bootstrap. Best-effort: any mail
 * failure is swallowed (the lead is already saved).
 */
function lead_respond_ok_then_notify(PDO $pdo, array $lead): void
{
    while (ob_get_level() > 0) { ob_end_clean(); }
    $json = '{"ok":true}';
    http_response_code(200);
    header('Content-Type: application/json; charset=UTF-8');
    header('Content-Length: ' . strlen($json));
    header('Connection: close');
    echo $json;
    if (function_exists('fastcgi_finish_request')) { fastcgi_finish_request(); }
    else { @ob_flush(); @flush(); }

    try {
        $to = 'nricamora@virtualteammate.com';
        try {
            $st = $pdo->query("SELECT value FROM app_settings WHERE key = 'lead_notify_email'");
            $v  = $st ? trim((string) $st->fetchColumn()) : '';
            if ($v !== '') { $to = $v; }
        } catch (Throwable $_) {}
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) { return; }

        $who     = $lead['name'] !== '' ? $lead['name'] : $lead['email'];
        $subject = 'New lead: ' . $who;
        $text    = "New website lead\n\n"
                 . "Name: {$lead['name']}\nEmail: {$lead['email']}\nPhone: {$lead['phone']}\n"
                 . "Company: {$lead['company']}\nSource: {$lead['source']}\nForm: {$lead['form']}\n"
                 . ($lead['message'] !== '' ? "\nMessage:\n{$lead['message']}\n" : '');
        lead_send_mail($to, $subject, lead_email_html($lead), $text);
    } catch (Throwable $_) {}
    exit;
}

/** Self-contained multipart text+HTML mail (mirrors the portal mailer). */
function lead_send_mail(string $to, string $subject, string $html, string $text): bool
{
    $from     = 'support@virtualteammate.com';
    $subject  = preg_replace('/\s+/', ' ', trim($subject));
    $boundary = 'vtlead_' . bin2hex(random_bytes(8));
    $headers  = implode("\r\n", [
        'From: Virtual Teammate Leads <' . $from . '>',
        'Reply-To: ' . $from,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'X-Mailer: VT Lead Capture',
    ]);
    $payload = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n{$text}\r\n"
             . "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n{$html}\r\n"
             . "--{$boundary}--";
    try { return @mail($to, $subject, $payload, $headers, '-f' . $from); }
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

// Reply to the visitor immediately, then email the team in the background so a
// slow mail relay never delays the form. The lead is already saved above.
lead_respond_ok_then_notify($pdo, [
    'name'    => $name,    'email' => $email, 'phone' => $phone,
    'company' => $company, 'source' => $source, 'form' => $form,
    'message' => $message, 'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
]);
