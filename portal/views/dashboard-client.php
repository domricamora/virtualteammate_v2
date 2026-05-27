<?php /** @var array $user @var array $data */
$pageTitle = 'Client Dashboard';
$subtitle  = 'Your team, your CSM, and your upcoming meetings.';
$client    = $data['client'] ?? null;
?>

<?php if (!$client): ?>
  <div class="card">
    <h3>No client record linked yet</h3>
    <p>Your user account isn't linked to a client (company) record. Ask your super admin to create one and link it to your user.</p>
  </div>
<?php else: ?>
  <div class="card">
    <h3><?= e($client['company_name']) ?></h3>
    <p class="muted">
      <i class="fa-solid fa-circle-info"></i> Contract: <strong><?= e($client['contract_status']) ?></strong>
      <?php if ($client['company_email']): ?>&middot; <?= e($client['company_email']) ?><?php endif; ?>
      <?php if ($client['workday_link']): ?>&middot; <a href="<?= e($client['workday_link']) ?>" target="_blank" rel="noopener">Workday tracker</a><?php endif; ?>
    </p>
  </div>

  <div class="grid-2">
    <div class="card">
      <div class="card-h">
        <h3>My Virtual Teammates</h3>
        <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('eod')) ?>"><i class="fa-solid fa-file-pen"></i> View EOD reports</a>
      </div>
      <?php if (empty($data['vts'])): ?>
        <p class="muted">No hired VTs assigned to your account yet.</p>
      <?php else: ?>
        <ul class="people-list">
          <?php foreach ($data['vts'] as $v): ?>
            <li>
              <span class="people-name"><?= e(trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''))) ?></span>
              <span class="people-meta"><?= e($v['role_title'] ?? '') ?> &middot; <?= e($v['department'] ?? '') ?></span>
              <span class="people-meta"><i class="fa-solid fa-envelope"></i> <?= e($v['email']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3>My Client Success Manager</h3>
      <?php if (empty($data['csms'])): ?>
        <p class="muted">No CSM assigned yet.</p>
      <?php else: ?>
        <ul class="people-list">
          <?php foreach ($data['csms'] as $c): ?>
            <li>
              <span class="people-name"><?= e(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))) ?></span>
              <span class="people-meta"><i class="fa-solid fa-envelope"></i> <?= e($c['email']) ?></span>
              <?php if (!empty($c['phone'])): ?>
                <span class="people-meta"><i class="fa-solid fa-phone"></i> <?= e($c['phone']) ?></span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-h">
      <h3>Recent meetings</h3>
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('meetings.edit')) ?>"><i class="fa-solid fa-plus"></i> Schedule meeting</a>
    </div>
    <?php if (empty($data['meetings'])): ?>
      <p class="muted">No meetings yet.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>When</th><th>With</th><th>Topic</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($data['meetings'] as $m): ?>
            <tr>
              <td><?= e(fmt_dt($m['scheduled_at'], 'Y-m-d H:i')) ?></td>
              <td><?= e(ucfirst($m['meeting_with_role'])) ?></td>
              <td><?= e($m['topic']) ?></td>
              <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php endif; ?>
