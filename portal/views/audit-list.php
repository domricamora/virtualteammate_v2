<?php /** @var array $rows */
$pageTitle = 'Audit log';
$subtitle  = 'Last 500 portal events (newest first).';
$totalAll  = count($rows);
?>
<div class="card" data-list>
  <div class="card-h">
    <div class="list-toolbar">
      <input type="search" data-list-search placeholder="Search actor, action, entity, IP…" autocomplete="off">
      <select data-list-pagesize>
        <option value="50" selected>50 / page</option>
        <option value="100">100 / page</option>
        <option value="250">250 / page</option>
        <option value="0">All</option>
      </select>
      <span class="list-counter">Total <strong><?= (int) $totalAll ?></strong> events &middot; <span data-list-counter>—</span></span>
    </div>
    <details class="hs-danger" style="margin-left:auto;">
      <summary class="btn-portal-danger btn-sm" style="cursor:pointer;list-style:none;"><i class="fa-solid fa-trash"></i> Clear all</summary>
      <form method="post" action="<?= e(portal_url('audit.clear')) ?>" style="margin-top:10px;display:flex;gap:6px;align-items:center;">
        <?= csrf_field() ?>
        <input type="text" name="confirm" placeholder='Type "DELETE ALL"' style="background:rgba(255,255,255,.04);border:1px solid rgba(229,62,62,.4);color:#fff;padding:6px 10px;border-radius:6px;font-size:12.5px;">
        <button class="btn-portal-danger btn-sm" type="submit">Confirm clear</button>
      </form>
    </details>
  </div>
  <table class="data-table compact" data-paginate>
    <thead><tr><th>When</th><th>Actor</th><th>Action</th><th>Entity</th><th>Details</th><th>IP</th><th></th></tr></thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr data-empty><td colspan="7" class="muted">No audit events yet.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td class="muted small"><?= local_dt($r['created_at']) ?></td>
          <td><?= e($r['actor_email'] ?? '—') ?></td>
          <td><span class="pill pill-default"><?= e($r['action']) ?></span></td>
          <td><?= e($r['entity_type']) ?><?= $r['entity_id'] ? ' #' . (int) $r['entity_id'] : '' ?></td>
          <td class="muted small"><?= e($r['details']) ?></td>
          <td class="muted small"><?= e($r['ip']) ?></td>
          <td class="row-actions">
            <form method="post" action="<?= e(portal_url('audit.delete')) ?>" class="inline-form" onsubmit="return confirm('Remove this audit entry?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="btn-portal-danger btn-sm" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  <div class="list-pager" data-list-pager></div>
</div>
