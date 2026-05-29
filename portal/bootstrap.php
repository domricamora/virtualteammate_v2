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
    $pdo = new PDO('sqlite:' . PORTAL_DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
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
