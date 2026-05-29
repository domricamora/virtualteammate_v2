<?php /** @var array $reports @var array $user */
$pageTitle = 'EOD Reports';
$subtitle  = match ($user['role']) {
    'super_admin' => 'All daily reports submitted by VTs.',
    'client'      => 'Daily reports from VTs assigned to your account.',
    'csm'         => 'Daily reports from VTs in your client portfolio.',
    'vt_hired',
    'vt_onpool'   => 'Your daily reports.',
    default       => '',
};
$canEdit  = in_array($user['role'], ['vt_hired','vt_onpool','super_admin'], true);
$totalAll = count($reports);
?>
<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search VT, date, content…" autocomplete="off">
      <select data-list-pagesize>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> reports &middot; <span data-list-counter>—</span></span>
    </div>
    <?php if ($canEdit): ?>
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('eod.edit')) ?>"><i class="fa-solid fa-plus"></i> New report</a>
    <?php endif; ?>
  </div>
  <table class="data-table" data-paginate>
    <thead>
      <tr>
        <th>Date</th><th>VT</th><th>Best work</th><th>Help needed</th><th>Updated</th><th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($reports)): ?>
        <tr data-empty><td colspan="6" class="muted">No reports yet.</td></tr>
      <?php else: foreach ($reports as $r):
        $vtName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
      ?>
        <tr>
          <td><strong><?= e($r['report_date']) ?></strong></td>
          <td><?= e($vtName ?: $r['email']) ?></td>
          <td><?= e(mb_strimwidth($r['best_work'], 0, 60, '…')) ?></td>
          <td><?= e(mb_strimwidth($r['help_needed'], 0, 60, '…')) ?></td>
          <td class="muted small"><?= e(fmt_dt($r['updated_at'])) ?></td>
          <td class="row-actions">
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('eod.edit', ['id'=>$r['id']])) ?>"><i class="fa-solid fa-eye"></i></a>
            <?php if ($canEdit): ?>
              <form method="post" action="<?= e(portal_url('eod.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this report?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button class="btn-portal-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i></button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  <div class="list-pager" data-list-pager></div>
</div>
