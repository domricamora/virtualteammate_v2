<?php
/**
 * Root entry point for first-time setup / migrations.
 *
 * The actual installer — which creates data/portal.sqlite, applies schema.sql,
 * runs idempotent migrations, and seeds the super_admin account (password shown
 * once + written to data/SUPERADMIN_CREDENTIALS.txt) — lives at
 * portal/install.php. It is access-controlled: runnable only from localhost/CLI,
 * or remotely with the secret from portal/install.key.php passed as ?key=… .
 *
 * This forwarder just gives a memorable /installer.php URL and hands off there,
 * preserving the query string (so ?key=THE_SECRET carries through).
 */
$qs = (string) ($_SERVER['QUERY_STRING'] ?? '');
header('Location: portal/install.php' . ($qs !== '' ? '?' . $qs : ''), true, 302);
echo 'Redirecting to the portal installer… '
   . '<a href="portal/install.php' . ($qs !== '' ? '?' . htmlspecialchars($qs, ENT_QUOTES) : '') . '">continue</a>.';
