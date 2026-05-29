<?php
/** @var array $user @var array $tasks @var string $scope @var array $assignees */
$pageTitle = $scope === 'mine' ? 'My Tasks' : 'Tasks';
$subtitle  = $scope === 'mine' ? 'Every task assigned to you.' : 'Active and completed tasks for this client.';
$clientId  = $clientId ?? null;
$nameOf    = static function (array $t): string {
    $n = trim(($t['a_fn'] ?? '') . ' ' . ($t['a_ln'] ?? ''));
    return $n !== '' ? $n : (string) ($t['a_email'] ?? 'Unassigned');
};
?>
<div class="card">
  <div class="card-h">
    <h3 style="margin:0;"><?= e($pageTitle) ?> <span class="muted small">(<?= count($tasks) ?>)</span></h3>
    <?php if ($scope !== 'mine'): ?>
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('tasks.edit', $clientId ? ['client_id' => $clientId] : [])) ?>"><i class="fa-solid fa-plus"></i> New task</a>
    <?php endif; ?>
  </div>

  <?php if (empty($tasks)): ?>
    <p class="muted">No tasks yet.</p>
  <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th></th>
          <th>Title</th>
          <th>Assignee</th>
          <th>Priority</th>
          <th>Due</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tasks as $t): ?>
          <tr style="<?= $t['status'] === 'completed' ? 'opacity:.65;' : '' ?>">
            <td style="width:36px;">
              <form method="post" action="<?= e(portal_url('tasks.toggle')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                <button class="btn-portal-secondary btn-sm" type="submit" title="<?= $t['status'] === 'active' ? 'Mark complete' : 'Re-open' ?>">
                  <i class="fa-<?= $t['status'] === 'completed' ? 'solid fa-rotate-left' : 'regular fa-square' ?>"></i>
                </button>
              </form>
            </td>
            <td>
              <strong><a href="<?= e(portal_url('tasks.edit', ['id' => $t['id']])) ?>"><?= e($t['title']) ?></a></strong>
              <?php if (!empty($t['description'])): ?>
                <div class="muted small"><?= e(mb_substr($t['description'], 0, 140)) ?><?= mb_strlen($t['description']) > 140 ? '…' : '' ?></div>
              <?php endif; ?>
            </td>
            <td><?= isset($t['a_fn']) || isset($t['a_email']) ? e($nameOf($t)) : '<span class="muted">Unassigned</span>' ?></td>
            <td><span class="cd-prio-pill cd-prio-<?= e($t['priority']) ?>"><?= e($t['priority']) ?></span></td>
            <td class="muted small"><?= !empty($t['due_date']) ? e($t['due_date']) : '—' ?></td>
            <td><span class="pill pill-<?= $t['status'] === 'completed' ? 'active' : ($t['status'] === 'cancelled' ? 'paused' : 'scheduled') ?>"><?= e($t['status']) ?></span></td>
            <td class="row-actions">
              <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('tasks.edit', ['id' => $t['id']])) ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
              <?php if ($scope !== 'mine'): ?>
                <form method="post" action="<?= e(portal_url('tasks.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this task?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                  <button class="btn-portal-danger btn-sm" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
