<?php /** @var array $meetings @var array $user */
$pageTitle = 'Meetings';
$subtitle  = match ($user['role']) {
    'super_admin' => 'All scheduled meetings across the portal.',
    'client'      => 'Meetings you or your CSM/VT have scheduled.',
    'csm'         => 'Meetings with your client portfolio.',
    'vt_hired',
    'vt_onpool'   => 'Meetings you are attending.',
    default       => '',
};
$canCreate = in_array($user['role'], ['super_admin','client','csm'], true);
$totalAll  = count($meetings);
?>
<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search client, topic, attendee…" autocomplete="off">
      <select data-list-pagesize>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> meetings &middot; <span data-list-counter>—</span></span>
    </div>
    <?php if ($canCreate): ?>
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('meetings.edit')) ?>"><i class="fa-solid fa-plus"></i> New meeting</a>
    <?php endif; ?>
  </div>
  <?php
    $appIcon = static function (string $a): array {
        return match (strtolower($a)) {
            'zoom'        => ['fa-video',          '#2D8CFF', 'Zoom'],
            'google_meet' => ['fa-video',          '#00897B', 'Google Meet'],
            'teams'       => ['fa-users',          '#4B53BC', 'Microsoft Teams'],
            'webex'       => ['fa-video',          '#00BCEB', 'Webex'],
            'phone'       => ['fa-phone',          '#9b9b9b', 'Phone call'],
            default       => ['fa-up-right-from-square', '#d4a64a', 'Other'],
        };
    };
  ?>
  <table class="data-table" data-paginate>
    <thead>
      <tr>
        <th>When</th><th>Client</th><th>With</th><th>Topic</th><th>Link</th><th>Status</th><th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($meetings)): ?>
        <tr data-empty><td colspan="7" class="muted">No meetings.</td></tr>
      <?php else: foreach ($meetings as $m):
        $orgName = trim(($m['org_fn'] ?? '') . ' ' . ($m['org_ln'] ?? ''));
        $attName = trim(($m['att_fn'] ?? '') . ' ' . ($m['att_ln'] ?? ''));
        [$ico, $col, $appLabel] = $appIcon((string) ($m['call_app'] ?? 'other'));
      ?>
        <tr>
          <td>
            <div><?= e(fmt_dt($m['scheduled_at'], 'Y-m-d H:i')) ?></div>
            <div class="muted small">
              <?php if (!empty($m['end_at'])): ?>
                ends <?= e(fmt_dt($m['end_at'], 'H:i')) ?> &middot;
              <?php endif; ?>
              <?= (int) $m['duration_minutes'] ?> min
            </div>
          </td>
          <td><?= e($m['company_name']) ?></td>
          <td>
            <div><?= e(ucfirst($m['meeting_with_role'])) ?><?= $attName ? ' &mdash; ' . e($attName) : '' ?></div>
            <div class="muted small">organized by <?= e($orgName ?: '—') ?></div>
          </td>
          <td><?= e($m['topic']) ?></td>
          <td>
            <?php if (!empty($m['meeting_link'])): ?>
              <a class="mtg-link" href="<?= e($m['meeting_link']) ?>" target="_blank" rel="noopener" title="<?= e($appLabel) ?>">
                <i class="fa-solid <?= e($ico) ?>" style="color:<?= e($col) ?>;"></i>
                <span><?= e($appLabel) ?></span>
                <i class="fa-solid fa-up-right-from-square" style="font-size:9px;opacity:.6;"></i>
              </a>
            <?php else: ?>
              <span class="muted small">
                <i class="fa-solid <?= e($ico) ?>" style="color:<?= e($col) ?>;opacity:.7;"></i>
                <?= e($appLabel) ?>
              </span>
            <?php endif; ?>
          </td>
          <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
          <td class="row-actions">
            <?php if ($canCreate): ?>
              <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('meetings.edit', ['id'=>$m['id']])) ?>"><i class="fa-solid fa-pen"></i></a>
              <form method="post" action="<?= e(portal_url('meetings.delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this meeting?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                <button class="btn-portal-danger btn-sm" type="submit"><i class="fa-solid fa-trash"></i></button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  <style>
    .mtg-link{display:inline-flex;align-items:center;gap:6px;color:#fff;text-decoration:none;font-size:12.5px;padding:4px 10px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:6px;transition:background .15s,border-color .15s;}
    .mtg-link:hover{background:rgba(247,185,69,.1);border-color:rgba(247,185,69,.35);}
  </style>
  <div class="list-pager" data-list-pager></div>
</div>
