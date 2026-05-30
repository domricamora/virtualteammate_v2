<?php
/**
 * Shared portal layout — sidebar nav + topbar + flash messages.
 * Loaded by render() when a view doesn't set $_naked = true.
 *
 * Expected vars in scope:
 *   $view      — the view filename that's being rendered
 *   $flashes   — array of flash messages pulled this request
 *   ...plus whatever the view passed in
 */
$me = current_user();
$role = $me['role'] ?? '';
$active = $_GET['p'] ?? 'dashboard';
$baseFlag = static function (string $page) use ($active): string {
    return str_starts_with($active, $page) ? ' is-active' : '';
};

$nav = [];
// Common to all roles
$nav[] = ['p' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house'];

if ($role === 'super_admin') {
    $nav[] = ['p' => 'users',       'label' => 'Users',        'icon' => 'fa-users'];
    $nav[] = ['p' => 'clients',     'label' => 'Clients',      'icon' => 'fa-building'];
    $nav[] = ['p' => 'csms',        'label' => 'CSMs',         'icon' => 'fa-user-tie'];
    $nav[] = ['p' => 'vts',         'label' => 'VT Profiles',  'icon' => 'fa-user-doctor'];
    $nav[] = ['p' => 'tasks',       'label' => 'Task Management', 'icon' => 'fa-list-check'];
    $nav[] = ['p' => 'relationships', 'label' => 'Relationships', 'icon' => 'fa-share-nodes'];
    $nav[] = ['p' => 'meetings',    'label' => 'Meetings',     'icon' => 'fa-calendar-check'];
    $nav[] = ['p' => 'eod',         'label' => 'EOD Reports',  'icon' => 'fa-file-pen'];
    $nav[] = ['p' => 'messages',    'label' => 'Messages',     'icon' => 'fa-comments'];
    $nav[] = ['p' => 'email',       'label' => 'Email',        'icon' => 'fa-paper-plane'];
    $nav[] = ['p' => 'hubspot',     'label' => 'HubSpot Sync', 'icon' => 'fa-cloud-arrow-down'];
    $nav[] = ['p' => 'traffic',     'label' => 'Traffic',      'icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'notifications','label' => 'Notifications','icon' => 'fa-bell'];
    $nav[] = ['p' => 'audit',       'label' => 'Audit Log',    'icon' => 'fa-list-check'];
} elseif ($role === 'client') {
    $nav[] = ['p' => 'my-vts',        'label' => 'My VTs',              'icon' => 'fa-user-doctor'];
    $nav[] = ['p' => 'tasks',         'label' => 'VT Assignments',      'icon' => 'fa-list-check'];
    $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'messages',      'label' => 'Messages',            'icon' => 'fa-comments'];
    $nav[] = ['p' => 'meetings',      'label' => 'Meetings',            'icon' => 'fa-calendar-check'];
    $nav[] = ['p' => 'resources',     'label' => 'Resources',           'icon' => 'fa-book-open'];
    $nav[] = ['p' => 'notifications', 'label' => 'Notifications',       'icon' => 'fa-bell'];
} elseif ($role === 'csm') {
    $nav[] = ['p' => 'tasks',         'label' => 'VT Assignments',      'icon' => 'fa-list-check'];
    $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'messages',      'label' => 'Messages',            'icon' => 'fa-comments'];
    $nav[] = ['p' => 'meetings',      'label' => 'Meetings',            'icon' => 'fa-calendar-check'];
    $nav[] = ['p' => 'resources',     'label' => 'Resources',           'icon' => 'fa-book-open'];
    $nav[] = ['p' => 'notifications', 'label' => 'Notifications',       'icon' => 'fa-bell'];
} elseif ($role === 'vt_hired' || $role === 'vt_onpool') {
    if ($role === 'vt_hired') {
        $nav[] = ['p' => 'tasks',         'label' => 'My Assignments',      'icon' => 'fa-list-check'];
        $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
        $nav[] = ['p' => 'messages',      'label' => 'Messages',            'icon' => 'fa-comments'];
    } else {
        $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
    }
    if ($role === 'vt_hired') {
        $nav[] = ['p' => 'meetings',  'label' => 'My Meetings',     'icon' => 'fa-calendar-check'];
    }
    $nav[] = ['p' => 'resources',     'label' => 'Resources',       'icon' => 'fa-book-open'];
    $nav[] = ['p' => 'notifications', 'label' => 'Notifications',   'icon' => 'fa-bell'];
}
$nav[] = ['p' => 'profile',     'label' => 'My Profile',   'icon' => 'fa-id-card'];

$pageTitle = $title ?? 'Virtual Teammate Portal';

// Unread-notifications count for the top-bar bell badge. Try/catch so the
// portal still renders if the notifications table somehow isn't ready yet.
$unreadNotiCount = 0;
if ($me) {
    try {
        $stmt = db()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :u AND read_at IS NULL');
        $stmt->execute([':u' => (int) $me['id']]);
        $unreadNotiCount = (int) $stmt->fetchColumn();
    } catch (Throwable $_) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= e($pageTitle) ?> &middot; VT Portal</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/png" href="<?= e(site_url('images/favicon-32x32.png')) ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="assets/portal.css?v=<?= @filemtime(__DIR__ . '/../assets/portal.css') ?: time() ?>">
</head>
<body class="portal-app">
<div class="portal-shell">

  <aside class="portal-side">
    <a class="portal-brand" href="<?= e(site_url()) ?>">
      <span class="portal-brand-mark">VT</span>
      <span class="portal-brand-text">Virtual Teammate<br><em>Portal</em></span>
    </a>

    <nav class="portal-nav" aria-label="Portal">
      <?php foreach ($nav as $item): ?>
        <a class="portal-nav-link<?= $baseFlag($item['p']) ?>" href="<?= e(portal_url($item['p'])) ?>">
          <i class="fa-solid <?= e($item['icon']) ?>"></i>
          <span><?= e($item['label']) ?></span>
          <?php if ($item['p'] === 'notifications' && $unreadNotiCount > 0): ?>
            <span class="portal-nav-badge"><?= $unreadNotiCount > 99 ? '99+' : (int) $unreadNotiCount ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="portal-side-foot">
      <a href="<?= e(site_url()) ?>" class="portal-side-link"><i class="fa-solid fa-arrow-left"></i> Marketing site</a>
      <a href="<?= e(portal_url('logout')) ?>" class="portal-side-link"><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
    </div>
  </aside>

  <main class="portal-main">
    <header class="portal-top">
      <div class="portal-top-l">
        <h1 class="portal-top-h"><?= e($pageTitle) ?></h1>
        <?php if (!empty($subtitle)): ?>
          <div class="portal-top-sub"><?= e($subtitle) ?></div>
        <?php endif; ?>
      </div>
      <div class="portal-top-r">
        <a class="portal-top-bell" href="<?= e(portal_url('notifications')) ?>" title="<?= (int) $unreadNotiCount ?> unread notification<?= $unreadNotiCount === 1 ? '' : 's' ?>">
          <i class="fa-solid fa-bell"></i>
          <?php if ($unreadNotiCount > 0): ?>
            <span class="portal-top-bell-badge"><?= $unreadNotiCount > 99 ? '99+' : (int) $unreadNotiCount ?></span>
          <?php endif; ?>
        </a>
        <span class="portal-me"><?= e(user_display_name($me)) ?> <?= role_badge($role) ?></span>
        <a class="portal-me-link" href="<?= e(portal_url('profile')) ?>" title="My profile"><i class="fa-solid fa-id-card"></i></a>
      </div>
    </header>

    <?php if (!empty($flashes)): ?>
      <div class="portal-flashes">
        <?php foreach ($flashes as $f): ?>
          <div class="portal-flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <section class="portal-content">
      <?php require __DIR__ . '/' . $view . '.php'; ?>
    </section>
  </main>

</div>
<script src="assets/portal.js?v=<?= @filemtime(__DIR__ . '/../assets/portal.js') ?: time() ?>" defer></script>
</body>
</html>
