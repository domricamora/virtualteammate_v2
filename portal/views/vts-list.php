<?php /** @var array $vts @var string $status @var string $q */
$pageTitle = 'VT profiles';
$subtitle  = 'Virtual Teammates — both hired and on-pool.';
$totalAll = count($vts);
?>
<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search name, email, role…" value="<?= e($q) ?>" autocomplete="off">
      <select data-list-pagesize>
        <option value="25">25 / page</option>
        <option value="50" selected>50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> VTs &middot; <span data-list-counter>—</span></span>
    </div>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('vts.edit')) ?>"><i class="fa-solid fa-plus"></i> New profile</a>
  </div>

  <table class="data-table" data-paginate>
    <thead><tr><th>Name</th><th>Email</th><th>Role title</th><th>Department</th><th>Country</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($vts)): ?>
        <tr data-empty><td colspan="7" class="muted">No VT profiles yet.</td></tr>
      <?php else: foreach ($vts as $p): ?>
        <tr>
          <td><?= e(trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''))) ?></td>
          <td><?= e($p['email']) ?></td>
          <td><?= e($p['role_title']) ?></td>
          <td><?= e($p['department']) ?></td>
          <td><?= e($p['country']) ?></td>
          <td><span class="pill pill-<?= e($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span></td>
          <td class="row-actions">
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('vts.view', ['id'=>$p['id']])) ?>" title="View"><i class="fa-solid fa-eye"></i></a>
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('vts.edit', ['id'=>$p['id']])) ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
            <form method="post" action="<?= e(portal_url('vts.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this VT profile?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <button class="btn-portal-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  <div class="list-pager" data-list-pager></div>
</div>
