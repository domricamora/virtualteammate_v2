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
if ($id < 1) { http_response_code(400); exit; }

$dbPath = __DIR__ . '/data/portal.sqlite';
if (!file_exists($dbPath)) { http_response_code(404); exit; }

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
    http_response_code(404); exit;
}

$base = realpath(__DIR__ . '/data/media');
if ($base === false) { http_response_code(404); exit; }
$matches = glob($base . DIRECTORY_SEPARATOR . 'vt' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'photo.*');
if (empty($matches)) { http_response_code(404); exit; }
$file = $matches[0];
$real = realpath($file);
if ($real === false || !str_starts_with($real, $base)) { http_response_code(403); exit; }

$ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($file));
header('Cache-Control: public, max-age=86400');
readfile($file);
