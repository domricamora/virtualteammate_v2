<?php /** @var array $meetings @var array $user */
$pageTitle = 'Meetings';
$subtitle  = match ($user['role']) {
    'super_admin' => 'All scheduled meetings across the portal.',
    'client'      => 'Meetings you or your CSM/VT have scheduled.',
    'csm'         => 'Meetings with your client portfolio.',
    'vt_hired',
    'vt_onpool'   => 'Meetings you are attending.',
    default       => '',
};
$canCreate = in_array($user['role'], ['super_admin','client','csm'], true);
?>
<div class="card">
  <?php if ($canCreate): ?>
    <div class="card-h">
      <h3>&nbsp;</h3>
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('meetings.edit')) ?>"><i class="fa-solid fa-plus"></i> New meeting</a>
    </div>
  <?php endif; ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>When</th><th>Client</th><th>With</th><th>Topic</th><th>Status</th><th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($meetings)): ?>
        <tr><td colspan="6" class="muted">No meetings.</td></tr>
      <?php else: foreach ($meetings as $m):
        $orgName = trim(($m['org_fn'] ?? '') . ' ' . ($m['org_ln'] ?? ''));
        $attName = trim(($m['att_fn'] ?? '') . ' ' . ($m['att_ln'] ?? ''));
      ?>
        <tr>
          <td>
            <div><?= e(fmt_dt($m['scheduled_at'], 'Y-m-d H:i')) ?></div>
            <div class="muted small"><?= (int) $m['duration_minutes'] ?> min</div>
          </td>
          <td><?= e($m['company_name']) ?></td>
          <td>
            <div><?= e(ucfirst($m['meeting_with_role'])) ?><?= $attName ? ' &mdash; ' . e($attName) : '' ?></div>
            <div class="muted small">organized by <?= e($orgName ?: '—') ?></div>
          </td>
          <td><?= e($m['topic']) ?></td>
          <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
          <td class="row-actions">
            <?php if ($canCreate): ?>
              <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('meetings.edit', ['id'=>$m['id']])) ?>"><i class="fa-solid fa-pen"></i></a>
              <form method="post" action="<?= e(portal_url('meetings.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this meeting?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                <button class="btn-portal-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i></button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
