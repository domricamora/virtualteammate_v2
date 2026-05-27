<?php /** @var array $users @var string $filter_role @var string $q */
$pageTitle = 'Users';
$subtitle  = 'Create, edit and remove user accounts of any role.';
?>

<div class="card">
  <div class="card-h">
    <form method="get" class="inline-filter">
      <input type="hidden" name="p" value="users">
      <input type="search" name="q" placeholder="Search name or email…" value="<?= e($q) ?>">
      <select name="role">
        <option value="">All roles</option>
        <?php foreach (PORTAL_ROLES as $r): ?>
          <option value="<?= e($r) ?>"<?= $r === $filter_role ? ' selected' : '' ?>><?= e(role_label($r)) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn-portal-secondary btn-sm" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
    </form>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('users.edit')) ?>"><i class="fa-solid fa-user-plus"></i> New user</a>
  </div>

  <table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Last login</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($users)): ?>
        <tr><td colspan="6" class="muted">No users match those filters.</td></tr>
      <?php else: foreach ($users as $u): ?>
        <tr>
          <td><?= e(user_display_name($u)) ?></td>
          <td><?= e($u['email']) ?></td>
          <td><?= role_badge($u['role']) ?></td>
          <td><?= $u['active'] ? '<span class="pill pill-active">Yes</span>' : '<span class="pill pill-paused">No</span>' ?></td>
          <td class="muted"><?= e(fmt_dt($u['last_login_at'])) ?></td>
          <td class="row-actions">
            <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('users.edit', ['id'=>$u['id']])) ?>"><i class="fa-solid fa-pen"></i></a>
            <form method="post" action="<?= e(portal_url('users.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this user permanently?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
              <button class="btn-portal-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
