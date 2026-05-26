<?php
// /services/ is not a real hub page yet — bounce to the homepage specialties section.
// Computed relative redirect so it works under both dev (/vtnew/services/) and prod (/services/).
$base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/services/index.php'), '/\\');
$base = $base === '' || $base === '.' ? '' : $base;
$home = preg_replace('#/services$#', '/', $base) ?: '/';
header('Location: ' . $home . '#specialties', true, 302);
exit;
