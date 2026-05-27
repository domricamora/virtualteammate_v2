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
$canEdit = in_array($user['role'], ['vt_hired','vt_onpool','super_admin'], true);
?>
<div class="card">
  <?php if ($canEdit): ?>
    <div class="card-h">
      <h3>&nbsp;</h3>
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('eod.edit')) ?>"><i class="fa-solid fa-plus"></i> New report</a>
    </div>
  <?php endif; ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>Date</th><th>VT</th><th>Best work</th><th>Help needed</th><th>Updated</th><th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($reports)): ?>
        <tr><td colspan="6" class="muted">No reports yet.</td></tr>
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
</div>
