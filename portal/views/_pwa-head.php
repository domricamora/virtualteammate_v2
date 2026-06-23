<?php
/**
 * Shared PWA <head> tags. Included inside the <head> of every portal entry
 * point (layout.php, login.php, error.php) so the app is installable on any
 * page. Paths are relative to /portal/ — they resolve the same on localhost
 * (/vtnew/portal/) and staging (/portal/).
 *
 * NOTE: the viewport meta is intentionally NOT here (the host pages own it,
 * with viewport-fit=cover added for notch-safe insets) — two viewport metas
 * would conflict.
 */
?>
<meta name="theme-color" content="#241b52">
<link rel="manifest" href="manifest.webmanifest">

<!-- iOS / standalone -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="VT Portal">
<link rel="apple-touch-icon" href="assets/icons/apple-touch-180.png">

<script src="assets/pwa.js?v=<?= @filemtime(__DIR__ . '/../assets/pwa.js') ?: time() ?>" defer></script>
