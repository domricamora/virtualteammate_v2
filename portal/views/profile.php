<?php /** @var array $user */
$pageTitle = 'My profile';
$subtitle  = 'Your personal info and password.';
?>
<div class="grid-2">
  <div class="card">
    <h3>Profile</h3>
    <form method="post" action="<?= e(portal_url('profile')) ?>" class="form-grid">
      <?= csrf_field() ?>

      <label>Email
        <input type="email" value="<?= e($user['email']) ?>" disabled>
      </label>
      <label>Role
        <input type="text" value="<?= e(role_label($user['role'])) ?>" disabled>
      </label>

      <label>First name
        <input type="text" name="first_name" value="<?= e($user['first_name']) ?>">
      </label>
      <label>Last name
        <input type="text" name="last_name" value="<?= e($user['last_name']) ?>">
      </label>

      <label>Phone
        <input type="tel" name="phone" value="<?= e($user['phone']) ?>">
      </label>
      <label>Country
        <input type="text" name="country" value="<?= e($user['country']) ?>">
      </label>

      <label class="span-2">Photo URL
        <input type="url" name="photo_url" value="<?= e($user['photo_url']) ?>" placeholder="https://…">
      </label>

      <div class="form-actions span-2">
        <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save profile</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h3>Change password</h3>
    <form method="post" action="<?= e(portal_url('password')) ?>" class="form-grid">
      <?= csrf_field() ?>

      <label class="span-2">Current password
        <input type="password" name="current_password" autocomplete="current-password" required>
      </label>
      <label class="span-2">New password (min 10 chars)
        <input type="password" name="new_password" autocomplete="new-password" minlength="10" required>
      </label>
      <label class="span-2">Repeat new password
        <input type="password" name="repeat_password" autocomplete="new-password" minlength="10" required>
      </label>

      <div class="form-actions span-2">
        <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-key"></i> Update password</button>
      </div>
    </form>
  </div>
</div>
