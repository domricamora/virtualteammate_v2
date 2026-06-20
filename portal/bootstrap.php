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
// Show errors only on localhost/CLI (dev). On a public host, leaking stack
// traces, file paths and SQL is an information-disclosure risk — log instead.
$vtIsLocalHost = PHP_SAPI === 'cli'
    || in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
ini_set('display_errors', $vtIsLocalHost ? '1' : '0');
ini_set('log_errors', '1');

// Force HTTPS (super-admin toggleable; shares data/force_ssl.off with the
// marketing site). No-ops on CLI and already-secure requests.
require __DIR__ . '/../includes/force-ssl.php';

if (PHP_SAPI !== 'cli') {
    ini_set('session.use_strict_mode',  '1');
    ini_set('session.cookie_httponly',  '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite',  'Lax');
    // Persist sessions in a guaranteed-writable app dir. Some shared hosts have
    // an unwritable default session.save_path, which silently breaks login: the
    // CSRF token set when the login page renders never survives to the POST, so
    // every login fails with a "CSRF token mismatch". Only override when our dir
    // is actually writable, otherwise leave the host default untouched.
    $vtSessDir = __DIR__ . '/../data/sessions';
    if (!is_dir($vtSessDir)) { @mkdir($vtSessDir, 0700, true); }
    if (is_dir($vtSessDir) && is_writable($vtSessDir)) { @session_save_path($vtSessDir); }
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
// Chat lives in its OWN SQLite file so high-frequency message writes/polls
// never contend with the primary DB (which a HubSpot sync may be writing).
const CHAT_DB_PATH   = __DIR__ . '/../data/chat.sqlite';
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

/**
 * Dedicated PDO for the chat database (data/chat.sqlite). Isolated from the
 * main portal DB so messaging's frequent writes + polling can't lock it, and
 * vice-versa. WAL + busy_timeout for safe concurrent read/write. The table is
 * created on first use, and any legacy rows in the main DB's `messages` table
 * are migrated once (ids preserved, so re-running is a no-op).
 */
function chatdb(): PDO
{
    static $pdo = null;
    if ($pdo !== null) { return $pdo; }
    $pdo = new PDO('sqlite:' . CHAT_DB_PATH, null, null, [PDO::ATTR_TIMEOUT => 15]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
    $pdo->exec('PRAGMA busy_timeout = 15000');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA synchronous = NORMAL');
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS messages (
            id                INTEGER PRIMARY KEY AUTOINCREMENT,
            conversation_key  TEXT    NOT NULL,
            sender_user_id    INTEGER NOT NULL,
            receiver_user_id  INTEGER NOT NULL,
            body              TEXT    NOT NULL,
            read_at           TEXT,
            created_at        TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP
        )"
    );
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_chat_conv ON messages(conversation_key, id)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_chat_recv ON messages(receiver_user_id, read_at)');

    // One-time migration of legacy messages from the main DB (only while empty).
    try {
        if ((int) $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn() === 0) {
            $legacy = db()->query(
                'SELECT id, conversation_key, sender_user_id, receiver_user_id, body, read_at, created_at FROM messages'
            )->fetchAll();
            if ($legacy) {
                $ins = $pdo->prepare(
                    'INSERT OR IGNORE INTO messages
                        (id, conversation_key, sender_user_id, receiver_user_id, body, read_at, created_at)
                     VALUES (:id,:k,:s,:r,:b,:ra,:ca)'
                );
                foreach ($legacy as $m) {
                    $ins->execute([
                        ':id' => $m['id'], ':k' => $m['conversation_key'],
                        ':s'  => $m['sender_user_id'], ':r' => $m['receiver_user_id'],
                        ':b'  => $m['body'], ':ra' => $m['read_at'], ':ca' => $m['created_at'],
                    ]);
                }
            }
        }
    } catch (Throwable $_) { /* legacy messages table may not exist — ignore */ }

    return $pdo;
}

/** HTML-escape helper. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Idempotently create the vt_requests table (client → "request an additional
 * VT" workflow) and migrate older copies to add the per-side soft-delete
 * columns. Lives here (not the router) so both the portal handlers AND the
 * public talent directory's request endpoint can ensure the schema.
 */
function vt_requests_ensure(PDO $pdo): void
{
    static $done = false;
    if ($done) { return; }
    $done = true;
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS vt_requests (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id      INTEGER NOT NULL,
            vt_user_id     INTEGER NOT NULL,
            requested_by   INTEGER NOT NULL,
            status         TEXT    NOT NULL DEFAULT 'pending',
            csm_note       TEXT    NOT NULL DEFAULT '',
            decided_by     INTEGER NOT NULL DEFAULT 0,
            client_deleted INTEGER NOT NULL DEFAULT 0,
            csm_deleted    INTEGER NOT NULL DEFAULT 0,
            created_at     TEXT    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            decided_at     TEXT
        )"
    );
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_vt_requests_client ON vt_requests(client_id, status)");

    // Migrate older tables: per-side soft-delete columns so a client and a CSM
    // can each remove a request from their own view independently.
    $cols = [];
    foreach ($pdo->query("PRAGMA table_info(vt_requests)") as $r) { $cols[$r['name']] = true; }
    if (!isset($cols['client_deleted'])) { $pdo->exec("ALTER TABLE vt_requests ADD COLUMN client_deleted INTEGER NOT NULL DEFAULT 0"); }
    if (!isset($cols['csm_deleted']))    { $pdo->exec("ALTER TABLE vt_requests ADD COLUMN csm_deleted INTEGER NOT NULL DEFAULT 0"); }
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

/**
 * Resolve a stored media/photo URL to a src usable from a portal page (which
 * lives under /portal/). Web-accessible /vtmedia/ paths are root-relative, so
 * they need the site base prepended; portal endpoints and absolute URLs pass
 * through unchanged.
 */
function media_src(?string $url): string
{
    $url = trim((string) $url);
    if ($url === '') { return ''; }
    if (preg_match('#^https?://#i', $url)) { return $url; }      // external
    if (str_starts_with($url, 'vtmedia/')) { return site_url($url); } // public photo file
    return $url; // index.php?p=media | p=avatar — already relative to /portal/
}

/**
 * Small round avatar thumbnail for list tables. Renders the photo (resolved via
 * media_src) with an initials-circle fallback if the image is missing/empty.
 */
function tbl_thumb(?string $photoUrl, string $name): string
{
    $u   = trim((string) $photoUrl);
    $ini = strtoupper(mb_substr(trim($name) !== '' ? trim($name) : '?', 0, 1));
    // For synced VT photos use the lightweight 150x150 thumbnail; otherwise fall
    // back to the stored photo/avatar. Missing thumb → onerror swaps to initials.
    $src = '';
    if (preg_match('#^vtmedia/vt/(\d+)/photo\.([a-z0-9]+)$#i', $u, $m)) {
        $src = site_url('vtmedia/vt_thumbs/' . $m[1] . '.webp');
    } elseif ($u !== '') {
        $src = media_src($u);
    }
    if ($src !== '') {
        return '<img class="tbl-thumb" src="' . e($src) . '" alt="" loading="lazy" '
             . 'onerror="this.outerHTML=&quot;<span class=\'tbl-thumb tbl-thumb-ph\'>' . e($ini) . '</span>&quot;;">';
    }
    return '<span class="tbl-thumb tbl-thumb-ph">' . e($ini) . '</span>';
}

/**
 * Resolved web URL of a VT's lightweight 150x150 thumbnail, or '' when the
 * stored photo isn't a synced vtmedia file (so callers can fall back to
 * initials). Use this for card grids / avatars instead of the full-size photo
 * or a PHP-served endpoint — vtmedia/vt_thumbs/<id>.<ext> is a static file.
 */
function media_thumb_src(?string $photoUrl): string
{
    $u = trim((string) $photoUrl);
    if (preg_match('#^vtmedia/vt/(\d+)/photo\.([a-z0-9]+)$#i', $u, $m)) {
        return site_url('vtmedia/vt_thumbs/' . $m[1] . '.webp');
    }
    return '';
}

/* ───────────────────────── Media cleanup ───────────────────────── */

/** Recursively delete a directory's contents + the dir itself. Returns files removed. No-op if missing. */
function rrmdir(string $dir): int
{
    if (!is_dir($dir)) { return 0; }
    $n = 0;
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            if ($f->isDir()) { @rmdir($f->getPathname()); }
            elseif (@unlink($f->getPathname())) { $n++; }
        }
    } catch (Throwable $_) {}
    @rmdir($dir);
    return $n;
}

/**
 * Delete one VT/user's media from BOTH the public photo tree (vtmedia/) and the
 * gated resume/video tree (data/media/). Returns the number of files removed.
 */
function delete_user_media(int $userId): int
{
    if ($userId < 1) { return 0; }
    $n = rrmdir(__DIR__ . '/../vtmedia/vt/' . $userId)
       + rrmdir(__DIR__ . '/../data/media/vt/' . $userId);
    foreach (glob(__DIR__ . '/../vtmedia/vt_thumbs/' . $userId . '.*') ?: [] as $t) {
        if (@unlink($t)) { $n++; }
    }
    return $n;
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
    $clean = static fn(string $s): string => htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
    // Text-only branding (no logo image) — renders identically in every client
    // with no remote-image fetch to fail. $publicBase is just the footer link.
    $publicBase = 'https://virtualteammate.com';
    $headHtml   = $clean($heading);

    return '<!doctype html><html><head><meta charset="UTF-8"><title>' . $headHtml . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f4f3f8;font-family:\'Manrope\',Helvetica,Arial,sans-serif;color:#1a1535;-webkit-font-smoothing:antialiased;">'
        . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f3f8;padding:36px 16px;">'
        . '<tr><td align="center">'
        . '<table width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 16px 40px rgba(57,25,186,.14);">'
        // Branded gradient header (matches the marketing site + portal hero gradient).
        . '<tr><td style="background:linear-gradient(135deg,#3919BA 0%,#7c3aed 55%,#F6B845 100%);padding:26px 30px;text-align:left;">'
        . '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>'
        . '<td valign="middle"><span style="font-family:\'Manrope\',Helvetica,Arial,sans-serif;font-size:21px;font-weight:800;letter-spacing:-.3px;color:#ffffff;line-height:1;">Virtual Teammate</span></td>'
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
        . '<a href="' . $clean($publicBase) . '" style="color:#9b97b4;text-decoration:none;">virtualteammate.com</a>'
        . '</td>'
        . '</tr></table>'
        . '</td></tr>'
        . '</table>'
        . '</td></tr></table>'
        . '</body></html>';
}

/**
 * Outbound SMTP configuration for Google Workspace, loaded once from the
 * gitignored `portal/smtp.local.php` (see smtp.local.php.example). Returns the
 * config array when host/user/pass are all present, or null to fall back to
 * native mail(). Never throws.
 */
function smtp_config(): ?array
{
    static $cfg = false;             // false = not yet loaded, null = none
    if ($cfg !== false) { return $cfg; }

    $cfg  = null;
    $file = __DIR__ . '/smtp.local.php';
    if (is_file($file)) {
        try {
            $c = include $file;
            if (is_array($c) && !empty($c['host']) && !empty($c['user']) && !empty($c['pass'])) {
                $cfg = $c + [
                    'port'      => 587,
                    'from'      => $c['user'],
                    'from_name' => 'Virtual Teammate',
                ];
            }
        } catch (Throwable $_) { $cfg = null; }
    }
    return $cfg;
}

/**
 * Minimal SMTP client: STARTTLS + AUTH LOGIN — enough to relay one message
 * through Google Workspace (smtp.gmail.com:587). Dependency-free (no Composer
 * / PHPMailer). Returns true only if the server accepted the message (250 at
 * end-of-DATA). Logs the failing step via error_log(); never throws.
 */
function smtp_send(array $cfg, string $from, string $to, string $message): bool
{
    $host = (string) ($cfg['host'] ?? 'smtp.gmail.com');
    $port = (int)    ($cfg['port'] ?? 587);
    $user = (string) ($cfg['user'] ?? '');
    $pass = (string) ($cfg['pass'] ?? '');

    $fp = @fsockopen($host, $port, $errno, $errstr, 15);
    if (!$fp) {
        error_log("smtp_send: connect failed to {$host}:{$port} — {$errstr} ({$errno})");
        return false;
    }
    stream_set_timeout($fp, 15);

    // Read a (possibly multiline) reply; return its leading 3-digit code, 0 on EOF.
    $read = static function () use ($fp): int {
        $code = 0;
        do {
            $line = fgets($fp, 515);
            if ($line === false) { return 0; }
            $code = (int) substr($line, 0, 3);
        } while (isset($line[3]) && $line[3] === '-');   // "250-" continues, "250 " ends
        return $code;
    };
    $cmd = static function (string $c) use ($fp): void { fwrite($fp, $c . "\r\n"); };

    $domain   = ($d = strrchr($from, '@')) !== false ? substr($d, 1) : 'localhost';
    $ok       = true;
    try {
        if ($read() !== 220) { throw new RuntimeException('no greeting'); }
        $cmd('EHLO ' . $domain);
        if ($read() !== 250) { throw new RuntimeException('EHLO rejected'); }

        // Gmail requires TLS before AUTH on 587.
        $cmd('STARTTLS');
        if ($read() !== 220) { throw new RuntimeException('STARTTLS rejected'); }
        if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('TLS handshake failed (check server CA bundle)');
        }
        $cmd('EHLO ' . $domain);                          // re-EHLO over the encrypted channel
        if ($read() !== 250) { throw new RuntimeException('EHLO (TLS) rejected'); }

        $cmd('AUTH LOGIN');
        if ($read() !== 334) { throw new RuntimeException('AUTH LOGIN unsupported'); }
        $cmd(base64_encode($user));
        if ($read() !== 334) { throw new RuntimeException('username stage failed'); }
        $cmd(base64_encode($pass));
        if ($read() !== 235) { throw new RuntimeException('authentication failed (check App Password)'); }

        $cmd('MAIL FROM:<' . $from . '>');
        if ($read() !== 250) { throw new RuntimeException('MAIL FROM rejected'); }
        $cmd('RCPT TO:<' . $to . '>');
        $rc = $read();
        if ($rc !== 250 && $rc !== 251) { throw new RuntimeException('RCPT TO rejected'); }

        $cmd('DATA');
        if ($read() !== 354) { throw new RuntimeException('DATA rejected'); }
        fwrite($fp, preg_replace('/^\./m', '..', $message) . "\r\n.\r\n");  // dot-stuff per RFC 5321
        if ($read() !== 250) { throw new RuntimeException('message not accepted'); }

        $cmd('QUIT');
    } catch (Throwable $e) {
        error_log('smtp_send: ' . $e->getMessage());
        $ok = false;
    }
    @fclose($fp);
    return $ok;
}

/**
 * Low-level multipart (plain + HTML) sender. Relays through Google Workspace
 * SMTP when portal/smtp.local.php is configured; otherwise falls back to native
 * PHP mail(). From support@virtualteammate.com (overridable in smtp.local.php).
 * Returns true if the message was accepted by the transport. Never throws.
 */
function portal_send_mail(string $to, string $subject, string $html, string $text, string $fromName = 'Virtual Teammate'): bool
{
    $cfg      = smtp_config();
    $fromAddr = $cfg !== null ? (string) $cfg['from']      : 'support@virtualteammate.com';
    $fromName = $cfg !== null ? (string) $cfg['from_name'] : $fromName;
    $subject  = preg_replace('/\s+/', ' ', trim($subject));
    $boundary = 'vtp_' . bin2hex(random_bytes(8));

    // RFC 2047 encode anything with non-ASCII so subjects/names survive transit.
    $encWord    = static fn(string $s): string =>
        preg_match('/[^\x20-\x7e]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s;

    $bodyPart = "--{$boundary}\r\n"
              . "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
              . $text . "\r\n"
              . "--{$boundary}\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
              . $html . "\r\n"
              . "--{$boundary}--\r\n";

    // ── Google Workspace SMTP (preferred when configured) ──
    if ($cfg !== null) {
        $domain  = ($d = strrchr($fromAddr, '@')) !== false ? substr($d, 1) : 'virtualteammate.com';
        $message = 'From: ' . $encWord($fromName) . ' <' . $fromAddr . ">\r\n"
                 . 'To: ' . $to . "\r\n"
                 . 'Reply-To: ' . $fromAddr . "\r\n"
                 . 'Subject: ' . $encWord($subject) . "\r\n"
                 . 'Date: ' . gmdate('D, d M Y H:i:s') . " +0000\r\n"
                 . 'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $domain . ">\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . 'Content-Type: multipart/alternative; boundary="' . $boundary . "\"\r\n"
                 . "X-Mailer: VT Portal\r\n"
                 . "\r\n"
                 . $bodyPart;
        return smtp_send($cfg, $fromAddr, $to, $message);
    }

    // ── Fallback: native mail() (no relay on localhost; works if host MTA set) ──
    $headers = implode("\r\n", [
        'From: ' . $fromName . ' <' . $fromAddr . '>',
        'Reply-To: ' . $fromAddr,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'X-Mailer: VT Portal',
    ]);
    try { return @mail($to, $subject, $bodyPart, $headers, '-f' . $fromAddr); }
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

function user_display_name(?array $u): string
{
    if (!$u) { return 'Guest'; }
    $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $name !== '' ? $name : ($u['email'] ?? 'Unknown');
}

/** Format an ISO date/time string with a PHP date() format (no zone shift).
 *  Used for naive wall-clock values (e.g. meeting scheduled_at) that must
 *  display exactly as entered. For UTC instant columns use local_dt(). */
function fmt_dt(?string $iso, string $fmt = 'Y-m-d g:i a'): string
{
    if (!$iso) return '';
    try {
        return (new DateTime($iso))->format($fmt);
    } catch (Throwable $_) {
        return $iso;
    }
}

/**
 * Render a UTC instant (any CURRENT_TIMESTAMP column — created_at, updated_at,
 * last_login_at, started_at, finished_at, …) as a <time> element that
 * assets/portal.js rewrites to the viewer's *browser* local time.
 *
 * The stored value is never mutated: it is read as UTC and emitted both as a
 * machine-readable UTC datetime and a server-rendered UTC fallback (shown when
 * JS is off). $fmt is a PHP date() format; portal.js understands the same
 * tokens (Y m n d j H G i s g h a A M) for the localized output.
 *
 * Returns ready-to-print HTML — do NOT wrap in e().
 */
function local_dt(?string $iso, string $fmt = 'Y-m-d g:i a'): string
{
    if ($iso === null || $iso === '') { return ''; }
    try {
        $dt = new DateTime($iso, new DateTimeZone('UTC'));
    } catch (Throwable $_) {
        return e($iso);
    }
    return '<time class="js-localtime" datetime="' . $dt->format('Y-m-d\TH:i:s\Z') . '"'
         . ' data-fmt="' . e($fmt) . '">' . e($dt->format($fmt)) . '</time>';
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
