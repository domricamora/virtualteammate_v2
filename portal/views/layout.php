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
    // Lead-gen + people first, then operations, then comms, then system tools.
    $nav[] = ['p' => 'leads',       'label' => 'Leads',         'icon' => 'fa-bullseye'];
    $nav[] = ['p' => 'clients',     'label' => 'Clients',       'icon' => 'fa-building'];
    $nav[] = ['p' => 'csms',        'label' => 'CSMs',          'icon' => 'fa-user-tie'];
    $nav[] = ['p' => 'vts',         'label' => 'VT Profiles',   'icon' => 'fa-user-doctor'];
    $nav[] = ['p' => 'users',       'label' => 'Users',         'icon' => 'fa-users'];
    $nav[] = ['p' => 'tasks',       'label' => 'Task Management','icon' => 'fa-list-check'];
    $nav[] = ['p' => 'relationships','label' => 'Relationships', 'icon' => 'fa-share-nodes'];
    $nav[] = ['p' => 'meetings',    'label' => 'Meetings',      'icon' => 'fa-calendar-check'];
    $nav[] = ['p' => 'eod',         'label' => 'EOD Reports',   'icon' => 'fa-file-pen'];
    $nav[] = ['p' => 'messages',    'label' => 'Messages',      'icon' => 'fa-comments'];
    $nav[] = ['p' => 'notifications','label' => 'Notifications', 'icon' => 'fa-bell'];
    $nav[] = ['p' => 'email',        'label' => 'Email',        'icon' => 'fa-paper-plane'];
    $nav[] = ['p' => 'hubspot',     'label' => 'HubSpot Sync',  'icon' => 'fa-cloud-arrow-down'];
    $nav[] = ['p' => 'traffic',     'label' => 'Traffic',       'icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'audit',       'label' => 'Audit Log',     'icon' => 'fa-clock-rotate-left'];
} elseif ($role === 'client') {
    // Your team first, then the work (assignments → meetings → reports), then
    // comms, then account actions (grow the team, billing), then help + alerts.
    $nav[] = ['p' => 'my-vts',        'label' => 'My VTs',              'icon' => 'fa-user-doctor'];
    $nav[] = ['p' => 'tasks',         'label' => 'VT Assignments',      'icon' => 'fa-list-check'];
    $nav[] = ['p' => 'meetings',      'label' => 'Meetings',            'icon' => 'fa-calendar-check'];
    $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'messages',      'label' => 'Messages',            'icon' => 'fa-comments'];
    $nav[] = ['p' => 'request-vt',    'label' => 'Request a VT',        'icon' => 'fa-user-plus'];
    $nav[] = ['p' => 'invoices',      'label' => 'My Invoices',         'icon' => 'fa-file-invoice-dollar'];
    $nav[] = ['p' => 'resources',     'label' => 'Resources',           'icon' => 'fa-book-open'];
    $nav[] = ['p' => 'notifications', 'label' => 'Notifications',       'icon' => 'fa-bell'];
} elseif ($role === 'csm') {
    // Book of business first (clients + their VTs), then the work
    // (assignments → meetings → reports), then comms, then tools, then alerts.
    $nav[] = ['p' => 'my-clients',    'label' => 'My Clients & VTs',    'icon' => 'fa-building-user'];
    $nav[] = ['p' => 'tasks',         'label' => 'VT Assignments',      'icon' => 'fa-list-check'];
    $nav[] = ['p' => 'meetings',      'label' => 'Meetings',            'icon' => 'fa-calendar-check'];
    $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'messages',      'label' => 'Messages',            'icon' => 'fa-comments'];
    $nav[] = ['p' => 'special-links', 'label' => 'Special Links',       'icon' => 'fa-link'];
    $nav[] = ['p' => 'resources',     'label' => 'Resources',           'icon' => 'fa-book-open'];
    $nav[] = ['p' => 'notifications', 'label' => 'Notifications',       'icon' => 'fa-bell'];
} elseif ($role === 'vt_hired') {
    // Logical flow: the daily work first, then comms + people, then pay,
    // then learning/tools, then referral, then alerts.
    $nav[] = ['p' => 'tasks',         'label' => 'My Assignments',      'icon' => 'fa-list-check'];
    $nav[] = ['p' => 'meetings',      'label' => 'My Meetings',         'icon' => 'fa-calendar-check'];
    $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'messages',      'label' => 'Messages',            'icon' => 'fa-comments'];
    $nav[] = ['p' => 'my-team',       'label' => 'My CSM & Teammates',  'icon' => 'fa-people-group'];
    $nav[] = ['p' => 'payslips',      'label' => 'Payslips',            'icon' => 'fa-money-check-dollar'];
    $nav[] = ['p' => 'resources',     'label' => 'Resources',           'icon' => 'fa-book-open'];
    $nav[] = ['p' => 'vtm-apps',      'label' => 'VTM Apps',            'icon' => 'fa-grip'];
    $nav[] = ['p' => 'refer',         'label' => 'Refer A Friend',      'icon' => 'fa-gift'];
    $nav[] = ['p' => 'notifications', 'label' => 'Notifications',       'icon' => 'fa-bell'];
} elseif ($role === 'vt_onpool') {
    $nav[] = ['p' => 'productivity',  'label' => 'Productivity Reports','icon' => 'fa-chart-line'];
    $nav[] = ['p' => 'resources',     'label' => 'Resources',           'icon' => 'fa-book-open'];
    $nav[] = ['p' => 'vtm-apps',      'label' => 'VTM Apps',            'icon' => 'fa-grip'];
    $nav[] = ['p' => 'refer',         'label' => 'Refer A Friend',      'icon' => 'fa-gift'];
    $nav[] = ['p' => 'notifications', 'label' => 'Notifications',       'icon' => 'fa-bell'];
}
$nav[] = ['p' => 'profile',     'label' => 'My Profile',   'icon' => 'fa-id-card'];
// Public talent directory (marketing site) — opens in a new tab. Only the
// super admin needs the public lead-gen funnel; VTs/CSMs/clients do not.
if ($role === 'super_admin') {
    $nav[] = ['p' => 'virtual-teammates', 'label' => 'Virtual Teammates', 'icon' => 'fa-user-group', 'url' => site_url('virtual-teammates/'), 'external' => true];
}

// On-pool VTs (not yet placed with a client) get a trimmed menu — the native
// equivalent of the staging [vtonpool_hidetabs] shortcode. Hide payslips,
// meetings, productivity reports, the CSM/VT relationship tab, and assigned
// tasks. Matched case-insensitively by label so future tabs are covered too.
if ($role === 'vt_onpool') {
    $hiddenLabels = [
        'payslips', 'payslip', 'my payslips',
        'meetings', 'my meetings',
        'productivity reports',
        'my csm and virtual teammate', 'my csm', 'csm',
        'my assigned tasks', 'assigned tasks', 'vt assignments', 'my assignments',
    ];
    $nav = array_values(array_filter($nav, static function (array $item) use ($hiddenLabels): bool {
        $label = strtolower(trim((string) ($item['label'] ?? '')));
        return !in_array($label, $hiddenLabels, true);
    }));
}

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

// New-lead count for the Leads nav badge (super admin). Counts leads captured
// since the admin last opened the Leads page (app_settings leads_last_seen_at).
$newLeadsCount = 0;
if ($me && $role === 'super_admin') {
    try {
        $seen = get_setting('leads_last_seen_at', '');
        if ($seen !== '') {
            $stmt = db()->prepare('SELECT COUNT(*) FROM leads WHERE datetime(created_at) > datetime(:s)');
            $stmt->execute([':s' => $seen]);
        } else {
            $stmt = db()->query('SELECT COUNT(*) FROM leads');
        }
        $newLeadsCount = (int) $stmt->fetchColumn();
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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="assets/portal.css?v=<?= @filemtime(__DIR__ . '/../assets/portal.css') ?: time() ?>">
</head>
<body class="portal-app <?= $role === 'super_admin' ? 'nav-side' : 'nav-top' ?>">
<div class="portal-shell" id="portalShell">

  <aside class="portal-side">
    <button class="portal-hamburger" id="portalHamburger" type="button" aria-label="Toggle menu" aria-expanded="false" aria-controls="portalShell">
      <i class="fa-solid fa-bars"></i><span class="portal-ham-label">Menu</span>
    </button>

    <nav class="portal-nav" aria-label="Portal">
      <?php foreach ($nav as $item): ?>
        <?php $itemUrl = $item['url'] ?? portal_url($item['p']); $itemExt = !empty($item['external']); ?>
        <a class="portal-nav-link<?= $itemExt ? '' : $baseFlag($item['p']) ?>" href="<?= e($itemUrl) ?>"<?= $itemExt ? ' target="_blank" rel="noopener"' : '' ?>>
          <i class="fa-solid <?= e($item['icon']) ?>"></i>
          <span><?= e($item['label']) ?></span>
          <?php $navBadge = $item['p'] === 'notifications' ? $unreadNotiCount : ($item['p'] === 'leads' ? $newLeadsCount : 0); ?>
          <?php if ($navBadge > 0): ?>
            <span class="portal-nav-badge"><?= $navBadge > 99 ? '99+' : (int) $navBadge ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="portal-side-foot">
      <?php if (in_array($role, ['super_admin', 'csm'], true)): ?>
        <a href="<?= e(site_url()) ?>" class="portal-side-link"><i class="fa-solid fa-arrow-left"></i> Marketing site</a>
      <?php endif; ?>
      <a href="<?= e(portal_url('logout')) ?>" class="portal-side-link"><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
    </div>
  </aside>

  <main class="portal-main">
    <header class="portal-top">
      <a class="portal-top-logo" href="<?= e(site_url()) ?>" title="Virtual Teammate — visit our website">
        <img src="<?= e(site_url('images/logo.webp')) ?>" alt="Virtual Teammate">
      </a>
      <div class="portal-top-l">
        <h1 class="portal-top-h"><?= e($pageTitle) ?></h1>
        <?php if (!empty($subtitle)): ?>
          <div class="portal-top-sub"><?= e($subtitle) ?></div>
        <?php endif; ?>
      </div>
      <div class="portal-top-r">
        <?php if ($me): ?>
          <a class="portal-top-bell" href="<?= e(portal_url('notifications')) ?>" title="<?= (int) $unreadNotiCount ?> unread notification<?= $unreadNotiCount === 1 ? '' : 's' ?>">
            <i class="fa-solid fa-bell"></i>
            <?php if ($unreadNotiCount > 0): ?>
              <span class="portal-top-bell-badge"><?= $unreadNotiCount > 99 ? '99+' : (int) $unreadNotiCount ?></span>
            <?php endif; ?>
          </a>
          <span class="portal-me"><?= e(user_display_name($me)) ?> <?= role_badge($role) ?></span>
          <a class="portal-me-link" href="<?= e(portal_url('profile')) ?>" title="My profile"><i class="fa-solid fa-id-card"></i></a>
        <?php else: ?>
          <a class="portal-me-link" href="<?= e(portal_url('login')) ?>" title="Log in"><i class="fa-solid fa-right-to-bracket"></i></a>
        <?php endif; ?>
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
