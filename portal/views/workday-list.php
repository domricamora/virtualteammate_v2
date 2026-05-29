<?php
/** @var array $user @var array $logs @var string $scope */
$pageTitle = 'Workday tracker';
$subtitle  = $scope === 'mine' ? 'Your recent workday log entries.' : 'Recent workday log entries for your team.';

$fmtHours = static function (int $minutes): string {
    if ($minutes <= 0) { return '—'; }
    $h = intdiv($minutes, 60); $m = $minutes % 60;
    return $m === 0 ? "{$h}h" : "{$h}h {$m}m";
};
$totalAll = count($logs);
?>
<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search VT, date, notes…" autocomplete="off">
      <select data-list-pagesize>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> entries &middot; <span data-list-counter>—</span></span>
    </div>
  </div>
  <table class="data-table" data-paginate>
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
      <?php if (empty($logs)): ?>
        <tr data-empty><td colspan="<?= $scope === 'mine' ? 6 : 6 ?>" class="muted">No workday entries logged yet.</td></tr>
      <?php else: foreach ($logs as $l): ?>
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
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  <div class="list-pager" data-list-pager></div>
</div>
