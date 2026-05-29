<?php
/** @var array $user @var array $notifications */
$pageTitle = 'Notifications';
$subtitle  = 'Updates from your portal activity, tasks and team.';
$unread    = array_filter($notifications, static fn($n) => empty($n['read_at']));
$totalAll  = count($notifications);
?>
<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search notifications…" autocomplete="off">
      <select data-list-pagesize>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> &middot; <strong style="color:#e53e3e;"><?= count($unread) ?></strong> unread &middot; <span data-list-counter>—</span></span>
    </div>
    <?php if (!empty($unread)): ?>
      <form method="post" action="<?= e(portal_url('notifications.read')) ?>" class="inline-form">
        <?= csrf_field() ?>
        <button class="btn-portal-secondary btn-sm" type="submit"><i class="fa-solid fa-check-double"></i> Mark all read</button>
      </form>
    <?php endif; ?>
  </div>
  <ul class="cd-noti-list" data-paginate>
    <?php if (empty($notifications)): ?>
      <li data-empty class="muted" style="text-align:center;padding:24px;">No notifications yet.</li>
    <?php else: foreach ($notifications as $n): ?>
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
    <?php endforeach; endif; ?>
  </ul>
  <div class="list-pager" data-list-pager></div>
</div>
