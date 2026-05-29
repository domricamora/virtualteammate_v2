<?php
/** @var array $user @var array $notifications */
$pageTitle = 'Notifications';
$subtitle  = 'Updates from your portal activity, tasks and team.';
$unread    = array_filter($notifications, static fn($n) => empty($n['read_at']));
?>
<div class="card">
  <div class="card-h">
    <h3 style="margin:0;">Inbox <span class="muted small">(<?= count($notifications) ?>)</span></h3>
    <?php if (!empty($unread)): ?>
      <form method="post" action="<?= e(portal_url('notifications.read')) ?>" class="inline-form">
        <?= csrf_field() ?>
        <button class="btn-portal-secondary btn-sm" type="submit"><i class="fa-solid fa-check-double"></i> Mark all read</button>
      </form>
    <?php endif; ?>
  </div>
  <?php if (empty($notifications)): ?>
    <p class="muted">No notifications yet.</p>
  <?php else: ?>
    <ul class="cd-noti-list">
      <?php foreach ($notifications as $n): ?>
        <li class="cd-noti <?= empty($n['read_at']) ? 'unread' : '' ?>">
          <div class="cd-noti-ico cd-noti-<?= e($n['kind']) ?>"><i class="fa-solid fa-<?= e($n['kind'] === 'task' ? 'list-check' : 'info-circle') ?>"></i></div>
          <div class="cd-noti-body">
            <div class="cd-noti-title"><?= e($n['title']) ?></div>
            <?php if (!empty($n['body'])): ?><div class="muted small"><?= e($n['body']) ?></div><?php endif; ?>
            <div class="muted small"><?= e(fmt_dt($n['created_at'])) ?></div>
          </div>
          <div class="cd-noti-actions">
            <?php if (!empty($n['link'])): ?>
              <a class="btn-portal-secondary btn-sm" href="<?= e($n['link']) ?>"><i class="fa-solid fa-arrow-right"></i> Open</a>
            <?php endif; ?>
            <?php if (empty($n['read_at'])): ?>
              <form method="post" action="<?= e(portal_url('notifications.read')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                <button class="btn-portal-secondary btn-sm" type="submit" title="Mark read"><i class="fa-solid fa-check"></i></button>
              </form>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
