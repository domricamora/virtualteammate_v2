<?php /** @var ?array $user */
$isNew = $user === null;
$pageTitle = $isNew ? 'New user' : 'Edit user — ' . user_display_name($user);
?>
<div class="card">
  <form method="post" action="<?= e(portal_url('users.edit', $isNew ? [] : ['id'=>$user['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>

    <label>Email
      <input type="email" name="email" required value="<?= e($user['email'] ?? '') ?>">
    </label>

    <label>Role
      <select name="role" required>
        <?php foreach (PORTAL_ROLES as $r): ?>
          <option value="<?= e($r) ?>"<?= ($user['role'] ?? '') === $r ? ' selected' : '' ?>><?= e(role_label($r)) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>First name
      <input type="text" name="first_name" value="<?= e($user['first_name'] ?? '') ?>">
    </label>

    <label>Last name
      <input type="text" name="last_name" value="<?= e($user['last_name'] ?? '') ?>">
    </label>

    <label>Phone
      <input type="tel" name="phone" value="<?= e($user['phone'] ?? '') ?>">
    </label>

    <label>Country
      <input type="text" name="country" value="<?= e($user['country'] ?? '') ?>">
    </label>

    <label class="span-2">Photo URL
      <input type="url" name="photo_url" value="<?= e($user['photo_url'] ?? '') ?>" placeholder="https://…">
    </label>

    <label class="span-2 check-row">
      <input type="checkbox" name="active" <?= empty($user) || !empty($user['active']) ? 'checked' : '' ?>>
      Active account (uncheck to disable login without deleting)
    </label>

    <label class="span-2">
      <?= $isNew ? 'Password (required, min 10 chars)' : 'Set new password (leave blank to keep current)' ?>
      <input type="password" name="password" autocomplete="new-password" minlength="10" <?= $isNew ? 'required' : '' ?>>
    </label>

    <div class="form-actions span-2">
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
      <a class="btn-portal-secondary" href="<?= e(portal_url('users')) ?>">Cancel</a>
    </div>
  </form>
</div>

<?php if (!$isNew): ?>
  <div class="card">
    <h3>Danger zone</h3>
    <form method="post" action="<?= e(portal_url('users.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this user permanently?');">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
      <button class="btn-portal-danger" type="submit"><i class="fa-solid fa-trash"></i> Delete user</button>
    </form>
  </div>
<?php endif; ?>
