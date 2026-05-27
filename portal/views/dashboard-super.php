<?php /** @var array $user @var array $stats */
$pageTitle = 'Super Admin Dashboard';
$subtitle  = 'Operational overview of the VT portal.';
?>

<div class="stat-grid">
  <a class="stat-card" href="<?= e(portal_url('users')) ?>">
    <div class="stat-num"><?= (int) $stats['users_total'] ?></div>
    <div class="stat-lbl">Total users</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'client'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_clients'] ?></div>
    <div class="stat-lbl">Clients</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'csm'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_csm'] ?></div>
    <div class="stat-lbl">CSMs</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'vt_hired'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_hired'] ?></div>
    <div class="stat-lbl">VTs hired</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'vt_onpool'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_onpool'] ?></div>
    <div class="stat-lbl">VTs on-pool</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('clients')) ?>">
    <div class="stat-num"><?= (int) $stats['clients_active'] ?></div>
    <div class="stat-lbl">Active clients</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('meetings')) ?>">
    <div class="stat-num"><?= (int) $stats['meetings_upcoming'] ?></div>
    <div class="stat-lbl">Upcoming meetings</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('eod')) ?>">
    <div class="stat-num"><?= (int) $stats['eod_today'] ?></div>
    <div class="stat-lbl">EOD reports today</div>
  </a>
</div>

<div class="card">
  <h3>Quick actions</h3>
  <div class="actions-row">
    <a class="btn-portal-primary"   href="<?= e(portal_url('users.edit')) ?>"><i class="fa-solid fa-user-plus"></i> New user</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('clients.edit')) ?>"><i class="fa-solid fa-building"></i> New client</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('vts.edit')) ?>"><i class="fa-solid fa-user-tie"></i> New VT profile</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('assignments')) ?>"><i class="fa-solid fa-diagram-project"></i> Edit assignments</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('audit')) ?>"><i class="fa-solid fa-list-check"></i> Audit log</a>
  </div>
</div>
