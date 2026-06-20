<?php
/**
 * Force-HTTPS guard. Include at the very TOP of an entry point (before any
 * output) — e.g. includes/head.php for the marketing site and portal/bootstrap.php
 * for the portal.
 *
 * Control: force-HTTPS is OFF by default (safe — never locks out a host whose
 * SSL cert isn't active yet). A super admin turns it ON from the portal
 * (Dashboard → Force HTTPS), which creates the file data/force_ssl.on. Disabling
 * in the portal (or deleting that file on the server) turns it back off. data/
 * is web-blocked and server-only, so the toggle persists across deploys.
 *
 * Skipped on localhost/CLI (dev) and when the request is already HTTPS (directly
 * or via a TLS-terminating proxy/CDN — X-Forwarded-Proto / X-Forwarded-SSL /
 * Cloudflare), so it never causes a redirect loop behind a proxy.
 */
(static function (): void {
    if (PHP_SAPI === 'cli') { return; }

    // ON switch set by the portal toggle (absent = disabled, the safe default).
    if (!is_file(__DIR__ . '/../data/force_ssl.on')) { return; }

    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    if ($host === ''
        || str_contains($host, 'localhost')
        || str_starts_with($host, '127.')
        || str_starts_with($host, '::1')
        || str_contains($host, '.local')) {
        return;
    }

    $xfProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    $xfSsl   = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? ''));
    $cfVisitor = (string) ($_SERVER['HTTP_CF_VISITOR'] ?? '');
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? '') === '443'
        || $xfProto === 'https'
        || $xfSsl === 'on'
        || str_contains($cfVisitor, '"scheme":"https"');
    if ($isHttps) { return; }

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    header('Location: https://' . $host . $uri, true, 301);
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    exit;
})();
