<?php /** @var ?array $meeting @var array $clients @var array $candidates @var array $user */
$isNew = $meeting === null;
$pageTitle = $isNew ? 'Schedule meeting' : 'Edit meeting';

$scheduled = $meeting['scheduled_at'] ?? '';
$scheduledInput = '';
if ($scheduled !== '') {
    try {
        $scheduledInput = (new DateTime($scheduled))->format('Y-m-d\TH:i');
    } catch (Throwable $_) { $scheduledInput = ''; }
}
?>
<div class="card">
  <form method="post" action="<?= e(portal_url('meetings.edit', $isNew ? [] : ['id'=>$meeting['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>

    <label>Client *
      <select name="client_id" required>
        <option value="">— choose —</option>
        <?php foreach ($clients as $cl): ?>
          <option value="<?= (int) $cl['id'] ?>"<?= (int) ($meeting['client_id'] ?? 0) === (int) $cl['id'] ? ' selected' : '' ?>><?= e($cl['company_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Meeting is with *
      <select name="meeting_with_role" required>
        <?php $mw = $meeting['meeting_with_role'] ?? 'csm'; ?>
        <option value="csm"<?= $mw === 'csm' ? ' selected' : '' ?>>CSM</option>
        <option value="vt"<?= $mw === 'vt'  ? ' selected' : '' ?>>VT</option>
      </select>
    </label>

    <label class="span-2">Attendee (optional, name a specific person)
      <select name="attendee_user_id">
        <option value="0">— none —</option>
        <?php foreach ($candidates as $u): ?>
          <option value="<?= (int) $u['id'] ?>"<?= (int) ($meeting['attendee_user_id'] ?? 0) === (int) $u['id'] ? ' selected' : '' ?>>
            <?= e(role_label($u['role'])) ?> &middot; <?= e(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?> &middot; <?= e($u['email']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>When *
      <input type="datetime-local" name="scheduled_at" required value="<?= e($scheduledInput) ?>">
    </label>

    <label>Duration (minutes)
      <input type="number" name="duration_minutes" min="15" max="240" value="<?= (int) ($meeting['duration_minutes'] ?? 30) ?>">
    </label>

    <label class="span-2">Topic
      <input type="text" name="topic" value="<?= e($meeting['topic'] ?? '') ?>">
    </label>

    <label class="span-2">Notes
      <textarea name="notes" rows="4"><?= e($meeting['notes'] ?? '') ?></textarea>
    </label>

    <label>Status
      <?php $st = $meeting['status'] ?? 'scheduled'; ?>
      <select name="status">
        <?php foreach (['scheduled','completed','cancelled'] as $s): ?>
          <option value="<?= e($s) ?>"<?= $s === $st ? ' selected' : '' ?>><?= e(ucfirst($s)) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <div class="form-actions span-2">
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
      <a class="btn-portal-secondary" href="<?= e(portal_url('meetings')) ?>">Cancel</a>
    </div>
  </form>
</div>
