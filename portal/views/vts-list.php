<?php /** @var array $vts @var string $status @var string $q */
$pageTitle = 'VT profiles';
$subtitle  = 'Virtual Teammates — both hired and on-pool.';
?>
<div class="card">
  <div class="card-h">
    <form method="get" class="inline-filter">
      <input type="hidden" name="p" value="vts">
      <input type="search" name="q" placeholder="Search name, email, role…" value="<?= e($q) ?>">
      <select name="status">
        <option value="">All statuses</option>
        <option value="hired"<?= $status === 'hired' ? ' selected' : '' ?>>Hired</option>
        <option value="onpool"<?= $status === 'onpool' ? ' selected' : '' ?>>On-pool</option>
      </select>
      <button class="btn-portal-secondary btn-sm" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
    </form>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('vts.edit')) ?>"><i class="fa-solid fa-plus"></i> New profile</a>
  </div>

  <table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Role title</th><th>Department</th><th>Country</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($vts)): ?>
        <tr><td colspan="7" class="muted">No VT profiles yet.</td></tr>
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
</div>
