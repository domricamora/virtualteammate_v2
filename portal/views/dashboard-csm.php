<?php /** @var array $user @var array $data */
$pageTitle = 'CSM Dashboard';
$subtitle  = 'Your client portfolio and recent meetings.';
?>

<div class="card">
  <div class="card-h">
    <h3>My clients (<?= count($data['clients']) ?>)</h3>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('meetings.edit')) ?>"><i class="fa-solid fa-plus"></i> Schedule meeting</a>
  </div>
  <?php if (empty($data['clients'])): ?>
    <p class="muted">No clients assigned to you yet. Ask a super admin to assign you via the Assignments page.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Company</th><th>Primary contact</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($data['clients'] as $c): ?>
          <tr>
            <td><?= e($c['company_name']) ?></td>
            <td>
              <?php $name = trim(($c['c_fn'] ?? '') . ' ' . ($c['c_ln'] ?? '')); ?>
              <?php if ($name): ?><div><?= e($name) ?></div><?php endif; ?>
              <div class="muted"><?= e($c['c_email'] ?? $c['company_email']) ?></div>
            </td>
            <td><span class="pill pill-<?= e($c['contract_status']) ?>"><?= e($c['contract_status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="card">
  <h3>Recent meetings</h3>
  <?php if (empty($data['meetings'])): ?>
    <p class="muted">No meetings yet.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>When</th><th>Client</th><th>With</th><th>Topic</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($data['meetings'] as $m): ?>
          <tr>
            <td><?= e(fmt_dt($m['scheduled_at'], 'Y-m-d H:i')) ?></td>
            <td><?= e($m['company_name']) ?></td>
            <td><?= e(ucfirst($m['meeting_with_role'])) ?></td>
            <td><?= e($m['topic']) ?></td>
            <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
