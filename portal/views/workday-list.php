<?php
/** @var array $user @var array $logs @var string $scope */
$pageTitle = 'Workday tracker';
$subtitle  = $scope === 'mine' ? 'Your recent workday log entries.' : 'Recent workday log entries for your team.';

$fmtHours = static function (int $minutes): string {
    if ($minutes <= 0) { return '—'; }
    $h = intdiv($minutes, 60); $m = $minutes % 60;
    return $m === 0 ? "{$h}h" : "{$h}h {$m}m";
};
?>
<div class="card">
  <div class="card-h">
    <h3 style="margin:0;">Workday log <span class="muted small">(<?= count($logs) ?> rows)</span></h3>
  </div>
  <?php if (empty($logs)): ?>
    <p class="muted">No workday entries logged yet.</p>
  <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>Date</th>
          <?php if ($scope !== 'mine'): ?><th>Virtual Teammate</th><?php endif; ?>
          <?php if ($scope === 'mine'): ?><th>Client</th><?php endif; ?>
          <th>Started</th>
          <th>Ended</th>
          <th>Total</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
          <tr>
            <td><strong><?= e($l['work_date']) ?></strong></td>
            <?php if ($scope !== 'mine'): ?>
              <td><?= e(trim(($l['first_name'] ?? '') . ' ' . ($l['last_name'] ?? ''))) ?></td>
            <?php else: ?>
              <td class="muted small"><?= e($l['company_name'] ?? '—') ?></td>
            <?php endif; ?>
            <td class="muted small"><?= e($l['started_at'] ?? '—') ?></td>
            <td class="muted small"><?= e($l['ended_at'] ?? '—') ?></td>
            <td><?= e($fmtHours((int) $l['minutes'])) ?></td>
            <td class="muted small"><?= e(mb_substr((string) ($l['notes'] ?? ''), 0, 120)) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
