<?php /** @var array $csms @var string $q */
$pageTitle = 'CSMs';
$subtitle  = 'Client Success Managers and their assigned client portfolios.';
$totalAll = count($csms);
?>
<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search name or email…" value="<?= e($q) ?>" autocomplete="off">
      <select data-list-pagesize>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> CSMs &middot; <span data-list-counter>—</span></span>
    </div>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('users.edit', ['role'=>'csm'])) ?>"><i class="fa-solid fa-user-plus"></i> New CSM</a>
  </div>

  <table class="data-table" data-paginate>
    <thead><tr><th>Name</th><th>Email</th><th>Country</th><th>Clients</th><th>Active</th><th>Last login</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($csms)): ?>
        <tr data-empty><td colspan="7" class="muted">No CSMs match those filters.</td></tr>
      <?php else: foreach ($csms as $u): ?>
        <tr>
          <td><?= e(user_display_name($u)) ?></td>
          <td><?= e($u['email']) ?></td>
          <td class="muted"><?= e($u['country']) ?></td>
          <td><strong><?= (int) $u['clients_count'] ?></strong></td>
          <td><?= $u['active'] ? '<span class="pill pill-active">Yes</span>' : '<span class="pill pill-paused">No</span>' ?></td>
          <td class="muted small"><?= e(fmt_dt($u['last_login_at'])) ?></td>
          <td class="row-actions">
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('csms.view', ['id'=>$u['id']])) ?>" title="View"><i class="fa-solid fa-eye"></i></a>
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('users.edit', ['id'=>$u['id']])) ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
            <form method="post" action="<?= e(portal_url('users.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this CSM permanently?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
              <button class="btn-portal-danger btn-sm" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  <div class="list-pager" data-list-pager></div>
</div>
