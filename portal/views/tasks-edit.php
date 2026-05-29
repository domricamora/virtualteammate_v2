<?php
/**
 * @var array $user
 * @var ?array $task
 * @var int $client_id
 * @var array $assignees
 * @var array $clients      (super_admin only: full active client list)
 * @var array $attachments
 */
$isNew     = $task === null;
$pageTitle = $isNew ? 'New task' : 'Edit task';
$subtitle  = $isNew ? 'Create a task and assign it to a Virtual Teammate.' : 'Update task details and manage attachments.';
$action    = portal_url('tasks.edit');

$role = $user['role'];
$isVt = $role === 'vt_hired';

$fmtBytes = static function (int $b): string {
    if ($b < 1024) return $b . ' B';
    if ($b < 1024 * 1024) return number_format($b / 1024, 1) . ' KB';
    return number_format($b / 1024 / 1024, 2) . ' MB';
};
$iconFor = static function (string $ext): string {
    return match (strtolower($ext)) {
        'pdf' => 'fa-file-pdf',
        'doc','docx' => 'fa-file-word',
        'xls','xlsx','csv' => 'fa-file-excel',
        'ppt','pptx' => 'fa-file-powerpoint',
        'zip' => 'fa-file-zipper',
        'jpg','jpeg','png','gif','webp' => 'fa-file-image',
        'txt' => 'fa-file-lines',
        default => 'fa-file',
    };
};
$canDeleteAttachment = static function (array $a) use ($role, $user): bool {
    if ($role === 'super_admin') { return true; }
    if ($role === 'vt_hired') {
        return (int) $a['uploaded_by'] === (int) $user['id'];
    }
    return in_array($role, ['client','csm'], true);
};
?>
<div class="card" style="max-width:760px;">
  <form method="post" action="<?= e($action) ?>" class="form-grid">
    <?= csrf_field() ?>
    <?php if (!$isNew): ?><input type="hidden" name="id" value="<?= (int) $task['id'] ?>"><?php endif; ?>

    <?php if ($role === 'super_admin'): ?>
      <label class="span-2">Client (optional — leave blank for cross-client tasks)
        <select name="client_id">
          <option value="">— No specific client —</option>
          <?php foreach ($clients as $c):
            $sel = ((int) ($task['client_id'] ?? $client_id) === (int) $c['id']) ? ' selected' : '';
          ?>
            <option value="<?= (int) $c['id'] ?>"<?= $sel ?>><?= e($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    <?php elseif (in_array($role, ['client','csm'], true)): ?>
      <input type="hidden" name="client_id" value="<?= (int) $client_id ?>">
    <?php endif; ?>

    <label class="span-2">Title
      <input type="text" name="title" required maxlength="200" value="<?= e($task['title'] ?? '') ?>" placeholder="e.g. Update January A/R aging report" <?= $isVt ? '' : '' ?>>
    </label>

    <label class="span-2">Description
      <textarea name="description" rows="5" placeholder="Add any context, links, or expected outcome..."><?= e($task['description'] ?? '') ?></textarea>
    </label>

    <label>Assign to
      <?php if ($isVt): ?>
        <?php
          $aid = (int) ($task['assignee_user_id'] ?? 0);
          $aLabel = trim(($task['a_fn'] ?? '') . ' ' . ($task['a_ln'] ?? '')) ?: 'Me';
        ?>
        <input type="text" disabled value="<?= e($aLabel) ?>" style="opacity:.7;">
        <input type="hidden" name="assignee_user_id" value="<?= $aid ?>">
      <?php else: ?>
        <select name="assignee_user_id">
          <option value="">— Unassigned —</option>
          <?php foreach ($assignees as $a):
            $aid = (int) ($a['user_id'] ?? $a['id'] ?? 0);
            $sel = isset($task['assignee_user_id']) && (int) $task['assignee_user_id'] === $aid ? ' selected' : '';
            $nm  = trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''));
            $nm  = $nm !== '' ? $nm : (string) ($a['email'] ?? ('User #' . $aid));
          ?>
            <option value="<?= $aid ?>"<?= $sel ?>><?= e($nm) ?></option>
          <?php endforeach; ?>
        </select>
      <?php endif; ?>
    </label>

    <label>Priority
      <select name="priority">
        <?php foreach (['low','normal','high','urgent'] as $p): ?>
          <option value="<?= e($p) ?>"<?= ($task['priority'] ?? 'normal') === $p ? ' selected' : '' ?>><?= e(ucfirst($p)) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Due date
      <input type="date" name="due_date" value="<?= e($task['due_date'] ?? '') ?>">
    </label>

    <div class="span-2" style="display:flex;gap:10px;align-items:center;justify-content:flex-end;">
      <a class="btn-portal-secondary" href="<?= e(portal_url('tasks', ($role !== 'client' && $client_id) ? ['client_id' => $client_id] : [])) ?>">Cancel</a>
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $isNew ? 'Create task' : 'Save changes' ?></button>
    </div>
  </form>
</div>

<?php if (!$isNew): ?>
  <!-- ── Attachments panel ────────────────────────────────────────── -->
  <div class="card" style="max-width:760px;margin-top:16px;">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-paperclip"></i> Attachments <span class="muted small">(<?= count($attachments) ?>)</span></h3>
    </div>

    <form method="post" action="<?= e(portal_url('tasks.attach')) ?>" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px;">
      <?= csrf_field() ?>
      <input type="hidden" name="task_id" value="<?= (int) $task['id'] ?>">
      <input type="file" name="file" required style="flex:1;min-width:240px;color:rgba(255,255,255,.85);padding:8px;background:rgba(255,255,255,.04);border:1px dashed rgba(255,255,255,.15);border-radius:8px;font-size:13px;">
      <button class="btn-portal-primary btn-sm" type="submit"><i class="fa-solid fa-upload"></i> Upload</button>
    </form>
    <p class="muted small" style="margin:0 0 8px;">PDF, Word, Excel, PowerPoint, CSV, TXT, ZIP, images. Max 20 MB per file.</p>

    <?php if (empty($attachments)): ?>
      <p class="muted">No files attached yet.</p>
    <?php else: ?>
      <div class="tm-attach-list">
        <?php foreach ($attachments as $a):
          $uploader = trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''));
          $uploader = $uploader !== '' ? $uploader : (string) ($a['email'] ?? '—');
        ?>
          <div class="tm-attach">
            <i class="fa-solid <?= e($iconFor((string) $a['ext'])) ?> tm-attach-icon"></i>
            <div class="tm-attach-meta">
              <div class="tm-attach-name"><?= e($a['original_name']) ?></div>
              <div class="tm-attach-sub">
                <?= e($fmtBytes((int) $a['size_bytes'])) ?>
                &middot; uploaded by <?= e($uploader) ?>
                &middot; <?= e(substr((string) $a['created_at'], 0, 16)) ?>
              </div>
            </div>
            <a class="tm-attach-dl" href="<?= e(portal_url('tasks.attachment', ['id' => (int) $a['id']])) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-download"></i> Download</a>
            <?php if ($canDeleteAttachment($a)): ?>
              <form method="post" action="<?= e(portal_url('tasks.attachment.delete')) ?>" class="inline-form" onsubmit="return confirm('Remove this attachment?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                <button class="btn-portal-danger btn-sm" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
