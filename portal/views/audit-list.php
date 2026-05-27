<?php /** @var array $rows */
$pageTitle = 'Audit log';
$subtitle  = 'Last 500 portal events (newest first).';
?>
<div class="card">
  <table class="data-table compact">
    <thead><tr><th>When</th><th>Actor</th><th>Action</th><th>Entity</th><th>Details</th><th>IP</th></tr></thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="6" class="muted">No audit events yet.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td class="muted small"><?= e(fmt_dt($r['created_at'])) ?></td>
          <td><?= e($r['actor_email'] ?? '—') ?></td>
          <td><span class="pill pill-default"><?= e($r['action']) ?></span></td>
          <td><?= e($r['entity_type']) ?><?= $r['entity_id'] ? ' #' . (int) $r['entity_id'] : '' ?></td>
          <td class="muted small"><?= e($r['details']) ?></td>
          <td class="muted small"><?= e($r['ip']) ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
