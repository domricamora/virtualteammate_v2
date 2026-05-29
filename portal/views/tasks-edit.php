<?php
/** @var array $user @var ?array $task @var int $client_id @var array $assignees */
$isNew     = $task === null;
$pageTitle = $isNew ? 'New task' : 'Edit task';
$subtitle  = $isNew ? 'Create a task and assign it to a Virtual Teammate.' : 'Update task details.';
$action    = portal_url('tasks.edit');
?>
<div class="card" style="max-width:760px;">
  <form method="post" action="<?= e($action) ?>" class="form-grid">
    <?= csrf_field() ?>
    <?php if (!$isNew): ?><input type="hidden" name="id" value="<?= (int) $task['id'] ?>"><?php endif; ?>
    <?php if ($user['role'] === 'super_admin' || $user['role'] === 'csm'): ?>
      <input type="hidden" name="client_id" value="<?= (int) $client_id ?>">
    <?php endif; ?>

    <label class="span-2">Title
      <input type="text" name="title" required maxlength="200" value="<?= e($task['title'] ?? '') ?>" placeholder="e.g. Update January A/R aging report">
    </label>

    <label class="span-2">Description
      <textarea name="description" rows="5" placeholder="Add any context, links, or expected outcome..."><?= e($task['description'] ?? '') ?></textarea>
    </label>

    <label>Assign to
      <select name="assignee_user_id">
        <option value="">— Unassigned —</option>
        <?php foreach ($assignees as $a):
          $aid = (int) $a['user_id'];
          $sel = isset($task['assignee_user_id']) && (int) $task['assignee_user_id'] === $aid ? ' selected' : '';
          $nm  = trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''));
          $nm  = $nm !== '' ? $nm : (string) ($a['email'] ?? ('User #' . $aid));
        ?>
          <option value="<?= $aid ?>"<?= $sel ?>><?= e($nm) ?></option>
        <?php endforeach; ?>
      </select>
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
      <a class="btn-portal-secondary" href="<?= e(portal_url('tasks', ($user['role'] !== 'client' && isset($client_id)) ? ['client_id' => $client_id] : [])) ?>">Cancel</a>
      <button class="btn-portal-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $isNew ? 'Create task' : 'Save changes' ?></button>
    </div>
  </form>
</div>
