<?php
/**
 * Member-gated media serve for the public Virtual Teammates modal.
 *
 * Streams a VT's résumé or intro video (from the web-denied data/media tree) to
 * ANY logged-in portal user (vtportal session) — so clients browsing the bench
 * can preview a teammate's CV regardless of whether they're already engaged with
 * that VT. Photos are public (talent-photo.php); résumé/video stay login-gated.
 * Supports HTTP range requests so <video> can seek.
 */
declare(strict_types=1);

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$kind = preg_replace('#[^a-z]#', '', strtolower((string) ($_GET['k'] ?? '')));
if ($id < 1 || !in_array($kind, ['resume', 'video'], true)) { http_response_code(400); exit; }

$dbPath = __DIR__ . '/data/portal.sqlite';
if (!is_file($dbPath)) { http_response_code(404); exit; }

// Access is granted by EITHER a valid CSM "special link" token for this VT, OR
// a logged-in portal session (any active user). The token path needs no login.
$token   = isset($_GET['t']) ? trim((string) $_GET['t']) : '';
$tokenOk = false;
if ($token !== '' && preg_match('/^[a-f0-9]{16,64}$/i', $token)) {
    try {
        $tp = new PDO('sqlite:' . $dbPath);
        $tp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ts = $tp->prepare('SELECT 1 FROM vt_special_links WHERE token = :t AND vt_user_id = :v AND revoked = 0 AND expires_at > :now LIMIT 1');
        $ts->execute([':t' => $token, ':v' => $id, ':now' => time()]);
        $tokenOk = (bool) $ts->fetchColumn();
    } catch (Throwable $_) { $tokenOk = false; }
}

if (!$tokenOk) {
    // Require a logged-in portal session (any active user).
    if (!empty($_COOKIE['vtportal'])) {
        @ini_set('session.use_strict_mode', '1');
        @ini_set('session.cookie_httponly', '1');
        @ini_set('session.use_only_cookies', '1');
        @ini_set('session.cookie_samesite', 'Lax');
        // Read the SAME session store the portal writes to (bootstrap uses
        // data/sessions when writable). Without this, a logged-in client's
        // session is invisible here and every résumé/video 403s.
        $vtSessDir = __DIR__ . '/data/sessions';
        if (!is_dir($vtSessDir)) { @mkdir($vtSessDir, 0700, true); }
        if (is_dir($vtSessDir) && is_writable($vtSessDir)) { @session_save_path($vtSessDir); }
        if (session_status() === PHP_SESSION_NONE) { session_name('vtportal'); @session_start(); }
    }
    $uid = (int) ($_SESSION['uid'] ?? 0);
    if ($uid < 1) { http_response_code(403); exit; }
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $chk = $pdo->prepare('SELECT 1 FROM users WHERE id = :u AND active = 1');
        $chk->execute([':u' => $uid]);
        if (!$chk->fetchColumn()) { http_response_code(403); exit; }
    } catch (Throwable $_) { http_response_code(500); exit; }
}

$base = realpath(__DIR__ . '/data/media');
if ($base === false) { http_response_code(404); exit; }
$matches = glob($base . DIRECTORY_SEPARATOR . 'vt' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $kind . '.*');
if (empty($matches)) { http_response_code(404); exit; }
$file = realpath($matches[0]);
if ($file === false || !str_starts_with($file, $base)) { http_response_code(403); exit; }

$ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = [
    'pdf'=>'application/pdf','doc'=>'application/msword',
    'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'mp4'=>'video/mp4','mov'=>'video/quicktime','m4v'=>'video/x-m4v','webm'=>'video/webm',
][$ext] ?? 'application/octet-stream';

$size = filesize($file);
while (ob_get_level() > 0) { ob_end_clean(); }

header('Content-Type: ' . $mime);
header('Accept-Ranges: bytes');
header('Cache-Control: private, max-age=3600');
// ?dl=1 forces a download (attachment); otherwise serve inline for the viewer.
$dispo = !empty($_GET['dl']) ? 'attachment' : 'inline';
header('Content-Disposition: ' . $dispo . '; filename="' . preg_replace('#[^A-Za-z0-9._-]#', '_', basename($file)) . '"');

$range = $_SERVER['HTTP_RANGE'] ?? '';
if ($range !== '' && preg_match('/bytes=(\d+)-(\d*)/', $range, $m)) {
    $start = (int) $m[1];
    $end   = ($m[2] !== '') ? (int) $m[2] : $size - 1;
    if ($end >= $size) { $end = $size - 1; }
    if ($start > $end || $start < 0) { http_response_code(416); header('Content-Range: bytes */' . $size); exit; }
    $length = $end - $start + 1;
    http_response_code(206);
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
    header('Content-Length: ' . $length);
    $fp = fopen($file, 'rb');
    if ($fp === false) { exit; }
    fseek($fp, $start);
    $remaining = $length;
    while ($remaining > 0 && !feof($fp)) {
        $chunk = fread($fp, min(8192, $remaining));
        if ($chunk === false) { break; }
        echo $chunk;
        $remaining -= strlen($chunk);
        flush();
    }
    fclose($fp);
    exit;
}

header('Content-Length: ' . $size);
readfile($file);
