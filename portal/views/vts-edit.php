<?php /** @var ?array $profile @var array $vt_users */
$isNew = $profile === null;
$pageTitle = $isNew ? 'New VT profile' : 'Edit VT profile';
$st = $profile['status'] ?? 'onpool';
?>
<div class="card">
  <form method="post" action="<?= e(portal_url('vts.edit', $isNew ? [] : ['id'=>$profile['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>

    <label class="span-2">Linked VT user *
      <select name="user_id" required <?= $isNew ? '' : 'disabled' ?>>
        <?php if ($isNew): ?><option value="">— choose —</option><?php endif; ?>
        <?php foreach ($vt_users as $u): ?>
          <option value="<?= (int) $u['id'] ?>"<?= (int) ($profile['user_id'] ?? 0) === (int) $u['id'] ? ' selected' : '' ?>>
            <?= e(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?> &middot; <?= e($u['email']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!$isNew): ?>
        <input type="hidden" name="user_id" value="<?= (int) $profile['user_id'] ?>">
      <?php endif; ?>
    </label>

    <label>Status
      <select name="status">
        <option value="onpool"<?= $st === 'onpool' ? ' selected' : '' ?>>On-pool</option>
        <option value="hired"<?= $st === 'hired'  ? ' selected' : '' ?>>Hired</option>
      </select>
    </label>

    <label>Experience (years)
      <input type="number" name="experience_years" min="0" max="60" value="<?= (int) ($profile['experience_years'] ?? 0) ?>">
    </label>

    <label>Department
      <input type="text" name="department" value="<?= e($profile['department'] ?? '') ?>" placeholder="Medical / Dental / Admin…">
    </label>

    <label>Role title
      <input type="text" name="role_title" value="<?= e($profile['role_title'] ?? '') ?>" placeholder="Medical Biller, Dental Receptionist…">
    </label>

    <label class="span-2">EHR / Software experience
      <input type="text" name="ehr_software" value="<?= e($profile['ehr_software'] ?? '') ?>" placeholder="Epic, Cerner, Dentrix, Eaglesoft…">
    </label>

    <label>English level
      <input type="text" name="english_level" value="<?= e($profile['english_level'] ?? '') ?>" placeholder="C1 / Fluent / Native…">
    </label>

    <label>IQ band
      <input type="text" name="iq_band" value="<?= e($profile['iq_band'] ?? '') ?>">
    </label>

    <label>Technical band
      <input type="text" name="technical_band" value="<?= e($profile['technical_band'] ?? '') ?>">
    </label>

    <label>Workday tracker ID
      <input type="text" name="workday_tracker_id" value="<?= e($profile['workday_tracker_id'] ?? '') ?>">
    </label>

    <label class="span-2">Workday link
      <input type="url" name="workday_link" value="<?= e($profile['workday_link'] ?? '') ?>">
    </label>

    <label class="span-2">Resume URL
      <input type="url" name="resume_url" value="<?= e($profile['resume_url'] ?? '') ?>" placeholder="https://…">
    </label>

    <label class="span-2">Intro video URL
      <input type="url" name="video_url" value="<?= e($profile['video_url'] ?? '') ?>" placeholder="https://…">
    </label>

    <label class="span-2">Summary
      <textarea name="summary" rows="4"><?= e($profile['summary'] ?? '') ?></textarea>
    </label>

    <label class="span-2">Experience details
      <textarea name="experience_text" rows="5"><?= e($profile['experience_text'] ?? '') ?></textarea>
    </label>

    <div class="form-actions span-2">
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
      <a class="btn-portal-secondary" href="<?= e(portal_url('vts')) ?>">Cancel</a>
    </div>
  </form>
</div>

<?php if (!$isNew): ?>
  <div class="card">
    <h3>Danger zone</h3>
    <form method="post" action="<?= e(portal_url('vts.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this VT profile?');">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $profile['id'] ?>">
      <button class="btn-portal-danger" type="submit"><i class="fa-solid fa-trash"></i> Delete profile</button>
    </form>
  </div>
<?php endif; ?>
