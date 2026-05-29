<?php
/**
 * @var array $user
 * @var array $tasks
 * @var string $scope            'all' | 'client' | 'mine'
 * @var int $client_id
 * @var array $assignees
 * @var string $tm_view          'list' | 'calendar' | 'year'
 * @var int $cal_year
 * @var int $cal_month
 * @var array $tasks_by_date     keyed YYYY-MM-DD → list of tasks
 * @var array $attach_counts     keyed task_id → int
 */
$role = $user['role'];
$canCreate = in_array($role, ['super_admin','client','csm'], true);
$canDeleteRow = static function (array $t) use ($role): bool {
    if ($role === 'vt_hired') { return false; }
    return true;
};

$pageTitle = match ($scope) {
    'mine'   => 'My Tasks',
    'client' => 'Tasks',
    'all'    => 'Task Management',
    default  => 'Tasks',
};
$subtitle = match ($scope) {
    'mine'   => 'Every task assigned to you. Update status, drop attachments, or mark complete.',
    'client' => 'Active and completed tasks for this client.',
    'all'    => 'Create, assign, and track tasks across every client and VT.',
    default  => 'Tasks',
};

$nameOf = static function (array $t): string {
    $n = trim(($t['a_fn'] ?? '') . ' ' . ($t['a_ln'] ?? ''));
    return $n !== '' ? $n : (string) ($t['a_email'] ?? 'Unassigned');
};
$prioLabel = static fn(string $p): string => ucfirst($p);

// ── Build a 6×7 month grid for the calendar view ──────────────────────
// We pad with cells from the previous month at the start and next month
// at the end so the grid is always rectangular.
$today = date('Y-m-d');
$first = mktime(0,0,0, $cal_month, 1, $cal_year);
$daysInMonth = (int) date('t', $first);
$startWeekday = (int) date('w', $first); // 0=Sun … 6=Sat
$gridStart   = $first - ($startWeekday * 86400);
$grid = [];
for ($i = 0; $i < 42; $i++) {
    $ts = $gridStart + ($i * 86400);
    $d  = date('Y-m-d', $ts);
    $grid[] = [
        'date'    => $d,
        'day'     => (int) date('j', $ts),
        'inMonth' => ((int) date('n', $ts)) === $cal_month,
        'isToday' => $d === $today,
        'tasks'   => $tasks_by_date[$d] ?? [],
    ];
}
$monthLabel = date('F Y', $first);

// Navigation timestamps for prev/next month + year.
$prevMonth = ($cal_month === 1)  ? ['y' => $cal_year - 1, 'm' => 12] : ['y' => $cal_year, 'm' => $cal_month - 1];
$nextMonth = ($cal_month === 12) ? ['y' => $cal_year + 1, 'm' => 1]  : ['y' => $cal_year, 'm' => $cal_month + 1];

// Helper to build a tasks URL preserving the view + client filter.
$navUrl = static function (array $extra) use ($tm_view, $client_id, $cal_year, $cal_month, $scope, $role) {
    $q = ['view' => $tm_view, 'y' => $cal_year, 'm' => $cal_month];
    if ($scope === 'all' && $client_id > 0) { $q['client_id'] = $client_id; }
    if ($scope === 'client' && $client_id > 0 && $role !== 'client') {
        $q['client_id'] = $client_id;
    }
    return portal_url('tasks', array_merge($q, $extra));
};

$activeCount = 0;
$completedCount = 0;
$overdueCount = 0;
$dueTodayCount = 0;
foreach ($tasks as $t) {
    if ($t['status'] === 'completed') { $completedCount++; continue; }
    if ($t['status'] === 'active') {
        $activeCount++;
        if (!empty($t['due_date'])) {
            if ($t['due_date'] === $today) { $dueTodayCount++; }
            elseif ($t['due_date'] < $today) { $overdueCount++; }
        }
    }
}
?>
<!-- Stats strip -->
<div class="tm-stats">
  <div class="tm-stat"><div class="tm-stat-n"><?= count($tasks) ?></div><div class="tm-stat-l">Total</div></div>
  <div class="tm-stat"><div class="tm-stat-n" style="color:#f7b945;"><?= $activeCount ?></div><div class="tm-stat-l">Active</div></div>
  <div class="tm-stat"><div class="tm-stat-n" style="color:#e53e3e;"><?= $overdueCount ?></div><div class="tm-stat-l">Overdue</div></div>
  <div class="tm-stat"><div class="tm-stat-n" style="color:#3b82f6;"><?= $dueTodayCount ?></div><div class="tm-stat-l">Due today</div></div>
  <div class="tm-stat"><div class="tm-stat-n" style="color:#7ec27e;"><?= $completedCount ?></div><div class="tm-stat-l">Completed</div></div>
</div>

<div class="card">
  <div class="card-h" style="gap:14px;">
    <div class="tm-tabs">
      <a class="tm-tab<?= $tm_view === 'list' ? ' is-active' : '' ?>"     href="<?= e($navUrl(['view' => 'list'])) ?>"><i class="fa-solid fa-list-ul"></i> List</a>
      <a class="tm-tab<?= $tm_view === 'calendar' ? ' is-active' : '' ?>" href="<?= e($navUrl(['view' => 'calendar'])) ?>"><i class="fa-regular fa-calendar"></i> Month</a>
      <a class="tm-tab<?= $tm_view === 'year' ? ' is-active' : '' ?>"     href="<?= e($navUrl(['view' => 'year'])) ?>"><i class="fa-solid fa-calendar-days"></i> Year</a>
    </div>

    <?php if ($tm_view === 'calendar' || $tm_view === 'year'): ?>
      <div class="tm-cal-nav">
        <?php if ($tm_view === 'calendar'): ?>
          <a class="btn-portal-secondary btn-sm" href="<?= e($navUrl(['view' => 'calendar', 'y' => $prevMonth['y'], 'm' => $prevMonth['m']])) ?>"><i class="fa-solid fa-chevron-left"></i></a>
          <strong style="min-width:160px;text-align:center;"><?= e($monthLabel) ?></strong>
          <a class="btn-portal-secondary btn-sm" href="<?= e($navUrl(['view' => 'calendar', 'y' => $nextMonth['y'], 'm' => $nextMonth['m']])) ?>"><i class="fa-solid fa-chevron-right"></i></a>
          <a class="btn-portal-secondary btn-sm" href="<?= e($navUrl(['view' => 'calendar', 'y' => (int) date('Y'), 'm' => (int) date('n')])) ?>">Today</a>
        <?php else: ?>
          <a class="btn-portal-secondary btn-sm" href="<?= e($navUrl(['view' => 'year', 'y' => $cal_year - 1])) ?>"><i class="fa-solid fa-chevron-left"></i></a>
          <strong style="min-width:80px;text-align:center;"><?= $cal_year ?></strong>
          <a class="btn-portal-secondary btn-sm" href="<?= e($navUrl(['view' => 'year', 'y' => $cal_year + 1])) ?>"><i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ($canCreate): ?>
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('tasks.edit', $client_id ? ['client_id' => $client_id] : [])) ?>"><i class="fa-solid fa-plus"></i> New task</a>
    <?php endif; ?>
  </div>

  <?php if ($tm_view === 'list'): ?>
    <!-- ── LIST VIEW ─────────────────────────────────────────────────── -->
    <div data-list style="margin-top:8px;">
    <div class="list-toolbar" style="margin-bottom:10px;">
      <input type="search" data-list-search placeholder="Search title, assignee, client, priority…" autocomplete="off">
      <select data-list-pagesize>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter"><span data-list-counter>—</span></span>
    </div>
    <?php if (empty($tasks)): ?>
      <p class="muted" style="padding:14px;">No tasks yet.</p>
    <?php else: ?>
      <table class="data-table" data-paginate>
        <thead>
          <tr>
            <th style="width:36px;"></th>
            <th>Title</th>
            <?php if ($scope !== 'client'): ?><th>Client</th><?php endif; ?>
            <th>Assignee</th>
            <th>Priority</th>
            <th>Due</th>
            <th>Status</th>
            <th style="width:60px;text-align:center;" title="Attachments"><i class="fa-solid fa-paperclip"></i></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tasks as $t):
            $tid = (int) $t['id'];
            $isOverdue = $t['status'] === 'active' && !empty($t['due_date']) && $t['due_date'] < $today;
          ?>
            <tr style="<?= $t['status'] === 'completed' ? 'opacity:.65;' : '' ?>">
              <td>
                <form method="post" action="<?= e(portal_url('tasks.toggle')) ?>" class="inline-form">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= $tid ?>">
                  <button class="btn-portal-secondary btn-sm" type="submit" title="<?= $t['status'] === 'active' ? 'Mark complete' : 'Re-open' ?>">
                    <i class="fa-<?= $t['status'] === 'completed' ? 'solid fa-rotate-left' : 'regular fa-square' ?>"></i>
                  </button>
                </form>
              </td>
              <td>
                <strong><a href="<?= e(portal_url('tasks.edit', ['id' => $tid])) ?>"><?= e($t['title']) ?></a></strong>
                <?php if (!empty($t['description'])): ?>
                  <div class="muted small"><?= e(mb_substr($t['description'], 0, 140)) ?><?= mb_strlen($t['description']) > 140 ? '…' : '' ?></div>
                <?php endif; ?>
              </td>
              <?php if ($scope !== 'client'): ?>
                <td class="muted small"><?= e($t['company_name'] ?? '—') ?></td>
              <?php endif; ?>
              <td><?= !empty($t['a_email']) ? e($nameOf($t)) : '<span class="muted">Unassigned</span>' ?></td>
              <td><span class="cd-prio-pill cd-prio-<?= e($t['priority']) ?>"><?= e($prioLabel($t['priority'])) ?></span></td>
              <td class="muted small">
                <?php if (!empty($t['due_date'])): ?>
                  <span style="<?= $isOverdue ? 'color:#e53e3e;font-weight:700;' : '' ?>"><?= e($t['due_date']) ?></span>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td><span class="pill pill-<?= $t['status'] === 'completed' ? 'active' : ($t['status'] === 'cancelled' ? 'paused' : 'scheduled') ?>"><?= e($t['status']) ?></span></td>
              <td style="text-align:center;color:rgba(255,255,255,.7);">
                <?php $ac = $attach_counts[$tid] ?? 0; ?>
                <?= $ac > 0 ? '<i class="fa-solid fa-paperclip" style="margin-right:4px;"></i>' . $ac : '<span class="muted">—</span>' ?>
              </td>
              <td class="row-actions">
                <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('tasks.edit', ['id' => $tid])) ?>" title="Edit"><i class="fa-solid fa-pen"></i></a>
                <?php if ($canDeleteRow($t)): ?>
                  <form method="post" action="<?= e(portal_url('tasks.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this task and all its attachments?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $tid ?>">
                    <button class="btn-portal-danger btn-sm" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="list-pager" data-list-pager></div>
    <?php endif; ?>
    </div>

  <?php elseif ($tm_view === 'calendar'): ?>
    <!-- ── MONTH CALENDAR ────────────────────────────────────────────── -->
    <div class="tm-cal">
      <div class="tm-cal-dows">
        <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d): ?>
          <div class="tm-cal-dow"><?= $d ?></div>
        <?php endforeach; ?>
      </div>
      <div class="tm-cal-grid">
        <?php foreach ($grid as $cell): ?>
          <div class="tm-cal-cell<?= $cell['inMonth'] ? '' : ' tm-cal-cell-dim' ?><?= $cell['isToday'] ? ' tm-cal-cell-today' : '' ?>">
            <div class="tm-cal-num"><?= $cell['day'] ?></div>
            <div class="tm-cal-tasks">
              <?php foreach (array_slice($cell['tasks'], 0, 4) as $t):
                $tid = (int) $t['id'];
              ?>
                <a class="tm-cal-task cd-prio-<?= e($t['priority']) ?><?= $t['status'] === 'completed' ? ' is-done' : '' ?>"
                   href="<?= e(portal_url('tasks.edit', ['id' => $tid])) ?>"
                   title="<?= e($t['title']) ?> · <?= e($nameOf($t)) ?>">
                  <?= e(mb_substr($t['title'], 0, 30)) ?>
                </a>
              <?php endforeach; ?>
              <?php if (count($cell['tasks']) > 4): ?>
                <div class="tm-cal-more">+<?= count($cell['tasks']) - 4 ?> more</div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  <?php elseif ($tm_view === 'year'): ?>
    <!-- ── YEAR OVERVIEW (12 mini months) ────────────────────────────── -->
    <div class="tm-year">
      <?php for ($mo = 1; $mo <= 12; $mo++):
        $monthFirst = mktime(0,0,0, $mo, 1, $cal_year);
        $dim = (int) date('t', $monthFirst);
        $startW = (int) date('w', $monthFirst);
        $monthName = date('F', $monthFirst);
        $taskCount = 0;
        $monthPrefix = sprintf('%04d-%02d-', $cal_year, $mo);
        foreach ($tasks_by_date as $k => $arr) {
            if (strpos($k, $monthPrefix) === 0) { $taskCount += count($arr); }
        }
      ?>
        <div class="tm-year-month">
          <div class="tm-year-h">
            <a href="<?= e($navUrl(['view' => 'calendar', 'y' => $cal_year, 'm' => $mo])) ?>"><strong><?= e($monthName) ?></strong></a>
            <span class="muted small"><?= $taskCount ?> tasks</span>
          </div>
          <div class="tm-year-dows">
            <?php foreach (['S','M','T','W','T','F','S'] as $d): ?><span><?= $d ?></span><?php endforeach; ?>
          </div>
          <div class="tm-year-days">
            <?php for ($pad = 0; $pad < $startW; $pad++): ?><span></span><?php endfor; ?>
            <?php for ($d = 1; $d <= $dim; $d++):
              $dStr = sprintf('%04d-%02d-%02d', $cal_year, $mo, $d);
              $count = count($tasks_by_date[$dStr] ?? []);
              $isToday = $dStr === $today;
              $cls = '';
              if ($isToday) { $cls .= ' is-today'; }
              if ($count > 0) { $cls .= ' has-tasks'; }
            ?>
              <a class="tm-year-day<?= $cls ?>" href="<?= e($navUrl(['view' => 'calendar', 'y' => $cal_year, 'm' => $mo])) ?>" title="<?= $count ?> task(s) on <?= $dStr ?>"><?= $d ?></a>
            <?php endfor; ?>
          </div>
        </div>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
