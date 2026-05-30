<?php
/**
 * Portal bootstrap — loaded by every portal entry point.
 *
 * Provides: strict error mode, secure PHP session, PDO SQLite (`db()`), auth
 * helpers (current_user / require_login / require_role), CSRF helpers, render
 * helpers (e / render / redirect / flash), audit_log writer.
 *
 * No HTML output. Entry-point files (index.php, install.php) handle that.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (PHP_SAPI !== 'cli') {
    ini_set('session.use_strict_mode',  '1');
    ini_set('session.cookie_httponly',  '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite',  'Lax');
    if (session_status() === PHP_SESSION_NONE) {
        session_name('vtportal');
        session_start();
    }

    // Portal pages are always dynamic + session-bound — never cache the HTML.
    // Static assets (portal.css / portal.js) are cache-busted via ?v={mtime}
    // on their <link>/<script> tags in views/layout.php.
    if (!headers_sent()) {
        header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

const PORTAL_DB_PATH = __DIR__ . '/../data/portal.sqlite';
const PORTAL_SCHEMA  = __DIR__ . '/schema.sql';
const PORTAL_ROLES   = ['super_admin', 'client', 'csm', 'vt_hired', 'vt_onpool'];

/** Lazy PDO singleton. Aborts loudly if DB missing — installer needs to run. */
function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    if (!file_exists(PORTAL_DB_PATH)) {
        http_response_code(503);
        $installUrl = 'install.php';
        die('Portal database not initialized. Run the installer first: '
            . '<a href="' . htmlspecialchars($installUrl) . '">' . htmlspecialchars($installUrl) . '</a>');
    }
    // ATTR_TIMEOUT makes the driver wait (not throw) when the DB is briefly
    // locked while opening; busy_timeout below covers per-statement waits.
    $pdo = new PDO('sqlite:' . PORTAL_DB_PATH, null, null, [PDO::ATTR_TIMEOUT => 15]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
    // Concurrency hardening — the HubSpot sync writes a checkpoint after every
    // media item while the UI poller reads the same DB. WAL lets readers and a
    // writer coexist; busy_timeout makes a contended write WAIT up to 15s for
    // the lock instead of throwing "database is locked". synchronous=NORMAL is
    // the safe, fast pairing for WAL.
    $pdo->exec('PRAGMA busy_timeout = 15000');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA synchronous = NORMAL');
    $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}

/** HTML-escape helper. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build a portal URL: `index.php?p=action&...`. Relative — works at any depth. */
function portal_url(string $page, array $params = []): string
{
    $params = array_merge(['p' => $page], $params);
    return 'index.php?' . http_build_query($params);
}

/** Absolute-path URL back to the marketing site root. */
function site_url(string $path = ''): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/portal/index.php';
    $base   = preg_replace('#/portal/.*$#', '/', $script) ?: '/';
    return $base . ltrim($path, '/');
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

/** Returns the current authed user row, or null. */
function current_user(): ?array
{
    static $cached = null;
    if (empty($_SESSION['uid'])) {
        return null;
    }
    if ($cached && (int) $cached['id'] === (int) $_SESSION['uid']) {
        return $cached;
    }
    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id AND active = 1');
    $stmt->execute([':id' => (int) $_SESSION['uid']]);
    $user = $stmt->fetch();
    if (!$user) {
        $_SESSION = [];
        if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        return null;
    }
    return $cached = $user;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        $back = $_SERVER['REQUEST_URI'] ?? '';
        redirect(portal_url('login', $back ? ['next' => $back] : []));
    }
    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        render('error', ['title' => 'Access denied', 'message' => 'You do not have access to this page.']);
        exit;
    }
    return $user;
}

function is_super_admin(): bool
{
    $u = current_user();
    return $u && $u['role'] === 'super_admin';
}

/* ───────────────────────── CSRF ─────────────────────────── */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $supplied = $_POST['_csrf'] ?? '';
    if (!is_string($supplied) || !hash_equals(csrf_token(), $supplied)) {
        http_response_code(400);
        render('error', ['title' => 'Bad request', 'message' => 'CSRF token mismatch. Reload the page and try again.']);
        exit;
    }
}

/* ───────────────────────── Flash messages ─────────────────────────── */

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_pull(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return is_array($f) ? $f : [];
}

/* ───────────────────────── Render / view ─────────────────────────── */

/**
 * Render a view file from views/. If $vars['_naked'] is true, render
 * standalone (no portal layout chrome — used by login / install / error pages
 * shown to unauthenticated users).
 */
function render(string $view, array $vars = []): void
{
    $viewPath = __DIR__ . '/views/' . $view . '.php';
    if (!is_file($viewPath)) {
        http_response_code(500);
        echo 'View not found: ' . e($view);
        return;
    }
    extract($vars, EXTR_SKIP);
    $flashes = flash_pull();
    if (!empty($vars['_naked'])) {
        require $viewPath;
        return;
    }
    require __DIR__ . '/views/layout.php';
}

/* ───────────────────────── Audit log ─────────────────────────── */

function audit_log(string $action, string $entityType = '', ?int $entityId = null, string $details = ''): void
{
    try {
        $actor = current_user();
        $stmt  = db()->prepare(
            'INSERT INTO audit_log (actor_user_id, action, entity_type, entity_id, details, ip)
             VALUES (:actor, :action, :etype, :eid, :details, :ip)'
        );
        $stmt->execute([
            ':actor'   => $actor['id'] ?? null,
            ':action'  => $action,
            ':etype'   => $entityType,
            ':eid'     => $entityId,
            ':details' => $details,
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    } catch (Throwable $_) {
        // Auditing must never crash the request.
    }
}

/* ───────────────────────── Notifications ─────────────────────────── */

/**
 * Create one notification + optionally email it.
 *
 * All notification triggers (task assigned/updated/completed, meeting
 * invite, new message, hubspot sync done, …) funnel through here so the
 * per-user `users.notify_by_email` opt-in is honored everywhere from a
 * single place.
 *
 * - $userId   recipient user id
 * - $kind     short category ('task' | 'meeting' | 'message' | 'sync' | 'info')
 *             used to pick the bell-icon glyph + colour in the views
 * - $title    short headline (≤120 chars)
 * - $body     plain-text body (no markup; emails strip HTML)
 * - $link     portal-relative link (e.g. `index.php?p=tasks.edit&id=42`)
 *             gets shown as the "Open" button + included verbatim in email
 */
function notify(int $userId, string $kind, string $title, string $body = '', string $link = ''): void
{
    if ($userId <= 0) { return; }
    try {
        $pdo = db();
        $pdo->prepare(
            "INSERT INTO notifications (user_id, kind, title, body, link)
             VALUES (:u, :k, :t, :b, :l)"
        )->execute([
            ':u' => $userId, ':k' => $kind,
            ':t' => mb_substr($title, 0, 200),
            ':b' => mb_substr($body,  0, 1000),
            ':l' => $link,
        ]);

        // Per-user opt-in email. Best-effort: mail() failures don't bubble.
        $row = $pdo->prepare('SELECT email, notify_by_email FROM users WHERE id = :u AND active = 1');
        $row->execute([':u' => $userId]);
        $r = $row->fetch();
        if ($r && (int) ($r['notify_by_email'] ?? 0) === 1 && !empty($r['email'])) {
            notify_send_email((string) $r['email'], $title, $body, $link);
        }
    } catch (Throwable $_) {
        // Notifying must never crash the request.
    }
}

/** Absolute https?:// base for the current host (falls back to the prod domain). */
function portal_site_base(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'virtualteammate.com';
    return $scheme . '://' . $host;
}

/**
 * Wrap content in the branded VT email shell: gradient header with logo +
 * white card + footer (left note + virtualteammate.com link). Matches the
 * marketing site / portal hero gradient. Used by both portal notifications
 * and the super-admin email composer.
 *
 * - $heading    H2 headline (escaped here — pass raw text)
 * - $bodyHtml   body markup (already-safe HTML — caller escapes)
 * - $ctaHtml    optional button block HTML
 * - $footerNote small grey footer text HTML (left column); '' hides it
 * - $eyebrow    uppercase tag in the header's top-right
 */
function portal_email_shell(string $heading, string $bodyHtml, string $ctaHtml = '', string $footerNote = '', string $eyebrow = 'Portal notification'): string
{
    $clean    = static fn(string $s): string => htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
    $siteBase = portal_site_base();
    $logoSrc  = $siteBase . '/images/logo.webp';
    $headHtml = $clean($heading);

    return '<!doctype html><html><head><meta charset="UTF-8"><title>' . $headHtml . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f4f3f8;font-family:\'Manrope\',Helvetica,Arial,sans-serif;color:#1a1535;-webkit-font-smoothing:antialiased;">'
        . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f3f8;padding:36px 16px;">'
        . '<tr><td align="center">'
        . '<table width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 16px 40px rgba(57,25,186,.14);">'
        // Branded gradient header (matches the marketing site + portal hero gradient).
        . '<tr><td style="background:linear-gradient(135deg,#3919BA 0%,#7c3aed 55%,#F6B845 100%);padding:26px 30px;text-align:left;">'
        . '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>'
        . '<td valign="middle"><img src="' . $clean($logoSrc) . '" alt="Virtual Teammate" width="120" style="display:block;border:0;height:auto;max-width:120px;"></td>'
        . ($eyebrow !== '' ? '<td valign="middle" align="right" style="color:rgba(255,255,255,.92);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:1.6px;">' . $clean($eyebrow) . '</td>' : '')
        . '</tr></table>'
        . '</td></tr>'
        // Body
        . '<tr><td style="padding:30px 30px 10px;">'
        . '<h2 style="margin:0 0 12px;font-family:\'Manrope\',Helvetica,Arial,sans-serif;font-size:19px;font-weight:800;color:#1a1535;line-height:1.3;letter-spacing:-.2px;">' . $headHtml . '</h2>'
        . ($bodyHtml !== '' ? '<div style="font-family:\'Manrope\',Helvetica,Arial,sans-serif;font-size:14px;line-height:1.6;color:#444163;">' . $bodyHtml . '</div>' : '')
        . $ctaHtml
        . '</td></tr>'
        // Footer
        . '<tr><td style="padding:18px 30px 26px;border-top:1px solid #eee9f5;background:#fafafd;">'
        . '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>'
        . '<td style="font-family:\'Manrope\',Helvetica,Arial,sans-serif;color:#6b6588;font-size:11.5px;line-height:1.6;">'
        . ($footerNote !== '' ? $footerNote : '&nbsp;')
        . '</td>'
        . '<td align="right" valign="top" style="font-family:\'Manrope\',Helvetica,Arial,sans-serif;font-size:10.5px;color:#9b97b4;letter-spacing:.4px;">'
        . '<a href="' . $clean($siteBase) . '" style="color:#9b97b4;text-decoration:none;">virtualteammate.com</a>'
        . '</td>'
        . '</tr></table>'
        . '</td></tr>'
        . '</table>'
        . '</td></tr></table>'
        . '</body></html>';
}

/**
 * Low-level multipart (plain + HTML) sender via native PHP mail(). From
 * support@virtualteammate.com. Returns mail()'s result — true if the message
 * was handed to a transport, false otherwise (e.g. localhost with no MTA).
 * Never throws.
 */
function portal_send_mail(string $to, string $subject, string $html, string $text, string $fromName = 'Virtual Teammate'): bool
{
    $from     = 'support@virtualteammate.com';
    $subject  = preg_replace('/\s+/', ' ', trim($subject));
    $boundary = 'vtp_' . bin2hex(random_bytes(8));
    $headers  = implode("\r\n", [
        'From: ' . $fromName . ' <' . $from . '>',
        'Reply-To: ' . $from,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'X-Mailer: VT Portal',
    ]);
    $payload  = "--{$boundary}\r\n"
              . "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
              . $text . "\r\n"
              . "--{$boundary}\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
              . $html . "\r\n"
              . "--{$boundary}--";

    try { return @mail($to, $subject, $payload, $headers, '-f' . $from); }
    catch (Throwable $_) { return false; }
}

/**
 * Branded HTML + plain-text email for a portal notification, with a primary
 * CTA button to the original link. Sent via native PHP mail(). Returns whether
 * mail() accepted it (false on hosts without a relay, e.g. localhost; works on
 * the production host).
 */
function notify_send_email(string $to, string $title, string $body, string $link = ''): bool
{
    $absLink = '';
    if ($link !== '') {
        $absLink = portal_site_base() . '/portal/' . ltrim($link, '/');
    }
    $clean = static fn(string $s): string => htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');

    $bodyHtml = $body !== '' ? nl2br($clean($body)) : '';
    $btnHtml  = $absLink !== ''
        ? '<table cellpadding="0" cellspacing="0" border="0" align="left" style="margin:22px 0 6px;">'
        . '<tr><td align="center" bgcolor="#3919BA" style="border-radius:10px;background:linear-gradient(135deg,#3919BA 0%,#7c3aed 100%);">'
        . '<a href="' . $clean($absLink) . '" style="display:inline-block;padding:14px 26px;font-family:Manrope,Arial,sans-serif;font-size:14px;font-weight:800;letter-spacing:.2px;color:#ffffff;text-decoration:none;border-radius:10px;">Open in portal &rarr;</a>'
        . '</td></tr></table><div style="clear:both;"></div>'
        : '';

    $portalUrl = portal_site_base() . '/portal/';
    $footer    = 'You\'re receiving this because email notifications are turned on for your portal account.<br>'
        . 'You can turn them off any time from the <a href="' . $clean($portalUrl) . '?p=notifications" style="color:#3919BA;text-decoration:none;font-weight:700;">Notifications</a> page.';

    $html = portal_email_shell($title, $bodyHtml, $btnHtml, $footer, 'Portal notification');
    $text = trim($title) . ($body !== '' ? "\n\n" . $body : '') . ($absLink !== '' ? "\n\nOpen: " . $absLink : '');

    return portal_send_mail($to, $title, $html, $text);
}

/* ───────────────────────── Misc helpers ─────────────────────────── */

/** Generate a strong human-friendly password (no ambiguous chars). */
function generate_password(int $len = 20): string
{
    $alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $max      = strlen($alphabet) - 1;
    $out      = '';
    for ($i = 0; $i < $len; $i++) {
        $out .= $alphabet[random_int(0, $max)];
    }
    return $out;
}

function role_label(string $role): string
{
    return [
        'super_admin' => 'Super Admin',
        'client'      => 'Client',
        'csm'         => 'CSM',
        'vt_hired'    => 'VT (Hired)',
        'vt_onpool'   => 'VT (On-Pool)',
    ][$role] ?? $role;
}

function role_badge(string $role): string
{
    $cls = [
        'super_admin' => 'role-super',
        'client'      => 'role-client',
        'csm'         => 'role-csm',
        'vt_hired'    => 'role-vt-hired',
        'vt_onpool'   => 'role-vt-onpool',
    ][$role] ?? 'role-default';
    return '<span class="role-badge ' . $cls . '">' . e(role_label($role)) . '</span>';
}

function user_display_name(array $u): string
{
    $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $name !== '' ? $name : ($u['email'] ?? 'Unknown');
}

/** Format an ISO date/time for display in user's local zone (no JS dep). */
function fmt_dt(?string $iso, string $fmt = 'Y-m-d H:i'): string
{
    if (!$iso) return '';
    try {
        return (new DateTime($iso))->format($fmt);
    } catch (Throwable $_) {
        return $iso;
    }
}

/* ───────────────────────── app_settings key/value ───────────────────────── */

/**
 * Shared in-memory cache for app_settings rows. Loaded once per request,
 * mutated by set_setting() so subsequent get_setting() calls in the same
 * request see fresh values (critical for the HubSpot state-machine dispatcher
 * that writes state then immediately reads it back).
 */
function &_settings_cache(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT key, value FROM app_settings') as $row) {
            $cache[(string) $row['key']] = (string) $row['value'];
        }
    }
    return $cache;
}

function get_setting(string $key, string $default = ''): string
{
    $cache = &_settings_cache();
    return $cache[$key] ?? $default;
}

function set_setting(string $key, string $value): void
{
    db()->prepare(
        'INSERT INTO app_settings (key, value, updated_at) VALUES (:k, :v, CURRENT_TIMESTAMP)
         ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = CURRENT_TIMESTAMP'
    )->execute([':k' => $key, ':v' => $value]);
    $cache = &_settings_cache();
    $cache[$key] = $value;
}
