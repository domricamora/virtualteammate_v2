<?php /** @var array $user @var array $stats @var array $traffic */
$pageTitle = 'Super Admin Dashboard';
$subtitle  = 'Operational overview of the VT portal.';
$traffic   = $traffic ?? ['recent' => [], 'top_countries' => [], 'top_pages' => []];
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
  <a class="stat-card" href="<?= e(portal_url('traffic')) ?>">
    <div class="stat-num"><?= (int) ($stats['traffic_today'] ?? 0) ?></div>
    <div class="stat-lbl">Site views today</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('traffic')) ?>">
    <div class="stat-num"><?= (int) ($stats['traffic_visitors_7d'] ?? 0) ?></div>
    <div class="stat-lbl">Unique visitors (7d)</div>
  </a>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-h">
      <h3><i class="fa-solid fa-chart-line" style="color:var(--gold);margin-right:8px;"></i> Recent site traffic</h3>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('traffic')) ?>">View all <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <?php if (empty($traffic['recent'])): ?>
      <p class="muted">No traffic logged yet. Visits to the marketing site will appear here once the beacon fires (and the portal DB is installed in that environment).</p>
    <?php else: ?>
      <table class="data-table compact">
        <thead><tr><th>When</th><th>Location</th><th>IP</th><th>Page</th></tr></thead>
        <tbody>
          <?php foreach ($traffic['recent'] as $r):
            $loc = trim(($r['city'] ?? '') . ($r['city'] && $r['country'] ? ', ' : '') . ($r['country'] ?? '')) ?: '—';
          ?>
            <tr>
              <td class="muted small"><?= e(fmt_dt($r['created_at'], 'm-d H:i')) ?></td>
              <td><?= e($loc) ?></td>
              <td class="muted small"><?= e($r['ip'] ?: '—') ?></td>
              <td class="muted small"><?= e($r['path'] ?: '/') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3><i class="fa-solid fa-earth-americas" style="color:var(--gold);margin-right:8px;"></i> Top countries (30d)</h3>
    <?php if (empty($traffic['top_countries'])): ?>
      <p class="muted">No geo data yet.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($traffic['top_countries'] as $c): ?>
          <li style="flex-direction:row;align-items:center;justify-content:space-between;">
            <span class="people-name"><?= e($c['country']) ?></span>
            <span class="pill pill-default"><?= (int) $c['n'] ?> views</span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
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
