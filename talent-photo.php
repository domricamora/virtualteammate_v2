<?php
/**
 * Public talent-photo serve.
 *
 * Mirrors the portal's auth-gated media endpoint but is open to anonymous
 * traffic and only serves PHOTOS for VT users (never resumes or videos).
 * Used by the marketing-site "Meet the Team" section so we can show real
 * synced profile photos without requiring login.
 *
 * Safe to expose because (a) the photos themselves were originally
 * publicly-shared headshots sourced from HubSpot and (b) we filter by
 * role to ensure only VT users' photos are served.
 */

declare(strict_types=1);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/**
 * Fallback path — served whenever we can't return a real photo. Lets the
 * marketing site show a graceful avatar instead of a broken-image icon.
 */
function tp_serve_placeholder(): void
{
    $placeholder = __DIR__ . '/images/photos/placeholder-avatar.svg';
    if (!is_file($placeholder)) { http_response_code(404); exit; }
    header('Content-Type: image/svg+xml');
    header('Content-Length: ' . filesize($placeholder));
    header('Cache-Control: public, max-age=86400');
    readfile($placeholder);
    exit;
}

if ($id < 1) { tp_serve_placeholder(); }

$dbPath = __DIR__ . '/data/portal.sqlite';
if (!file_exists($dbPath)) { tp_serve_placeholder(); }

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id AND active = 1 LIMIT 1");
    $stmt->execute([':id' => $id]);
    $role = (string) $stmt->fetchColumn();
} catch (Throwable $_) {
    http_response_code(500); exit;
}

if (!in_array($role, ['vt_hired', 'vt_onpool'], true)) {
    tp_serve_placeholder();
}

// Photos now live in the web-accessible /vtmedia/ tree; fall back to the legacy
// data/media location for any VT not yet re-synced/migrated.
$file = null;
foreach (['vtmedia', 'data/media'] as $rel) {
    $base = realpath(__DIR__ . '/' . $rel);
    if ($base === false) { continue; }
    $matches = glob($base . DIRECTORY_SEPARATOR . 'vt' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'photo.*');
    if (!empty($matches)) {
        $real = realpath($matches[0]);
        if ($real !== false && str_starts_with($real, $base)) { $file = $real; break; }
    }
}
if ($file === null) { tp_serve_placeholder(); }

$ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($file));
header('Cache-Control: public, max-age=86400');
readfile($file);
