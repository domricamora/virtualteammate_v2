<?php /** @var array $user */
$pageTitle = 'My profile';
$subtitle  = 'Your personal info, photo, cover image and password.';
$photoSrc  = media_src($user['photo_url'] ?: '');
$coverSrc  = $user['cover_url'] ?: '';
?>
<!-- Profile / cover preview -->
<div class="card pf-preview">
  <div class="pf-cover" <?php if ($coverSrc): ?>style="background-image:url('<?= e($coverSrc) ?>');"<?php endif; ?>>
    <?php if (!$coverSrc): ?><div class="pf-cover-empty"><i class="fa-solid fa-image"></i> No cover photo yet</div><?php endif; ?>
  </div>
  <div class="pf-photo-wrap">
    <?php if ($photoSrc): ?>
      <img class="pf-photo" src="<?= e($photoSrc) ?>" alt="Profile photo"
           onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
    <?php else: ?>
      <div class="pf-photo placeholder"><?= e(strtoupper(mb_substr($user['first_name'] ?: $user['email'], 0, 1))) ?></div>
    <?php endif; ?>
    <div class="pf-name">
      <h2 style="margin:0;"><?= e(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?: e($user['email']) ?></h2>
      <div class="muted small"><?= e(role_label($user['role'])) ?> &middot; <?= e($user['email']) ?></div>
    </div>
  </div>
</div>

<div class="grid-2" style="margin-top:18px;">
  <div class="card">
    <h3>Profile</h3>
    <form method="post" action="<?= e(portal_url('profile')) ?>" class="form-grid" enctype="multipart/form-data">
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

      <label class="span-2">Profile photo
        <input type="file" name="photo_upload" accept="image/jpeg,image/png,image/gif,image/webp">
        <small class="muted">JPG / PNG / GIF / WebP, up to 8MB. Or paste a URL below.</small>
      </label>
      <label class="span-2">…or a Photo URL
        <input type="url" name="photo_url" value="<?= e($user['photo_url']) ?>" placeholder="https://…">
      </label>

      <label class="span-2">Cover photo
        <input type="file" name="cover_upload" accept="image/jpeg,image/png,image/gif,image/webp">
        <small class="muted">Wide banner image (1500&times;500 looks best). Up to 8MB.</small>
      </label>
      <label class="span-2">…or a Cover URL
        <input type="url" name="cover_url" value="<?= e($user['cover_url']) ?>" placeholder="https://…">
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
