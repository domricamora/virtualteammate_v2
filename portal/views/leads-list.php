<?php
/** @var array $rows */
$total = count($rows);
?>
<div class="card" data-list>
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-bullseye"></i> Leads
      <span class="muted small">(<?= (int) $total ?>)</span>
    </h3>
    <span class="muted small">Captured from website forms &middot; newest first</span>
  </div>

  <?php if ($total === 0): ?>
    <p class="muted" style="padding:24px;text-align:center;">No leads captured yet. Submissions from the website's lead forms will appear here.</p>
  <?php else: ?>
  <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
  <table class="data-table compact" style="min-width:760px;">
    <thead>
      <tr>
        <th>When</th><th>Name</th><th>Email</th><th>Phone</th>
        <th>Company</th><th>Form</th><th>Interested in</th><th>Details</th><th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="muted small" style="white-space:nowrap;"><?= local_dt($r['created_at']) ?></td>
          <td><strong><?= e($r['name'] ?: '—') ?></strong></td>
          <td><a href="mailto:<?= e($r['email']) ?>" style="color:var(--gold,#d4a64a);"><?= e($r['email']) ?></a></td>
          <td class="muted small"><?= e($r['phone'] ?: '—') ?></td>
          <td class="muted small"><?= e($r['company'] ?: '—') ?></td>
          <td><span class="pill pill-default"><?= e($r['form'] ?: ($r['source'] ?: 'website')) ?></span></td>
          <td class="muted small"><?= e($r['vt_interest'] ?: '—') ?><?= $r['vt_id'] ? ' (#' . (int) $r['vt_id'] . ')' : '' ?></td>
          <td>
            <?php $extra = trim((string) ($r['details'] ?? '')); $msg = trim((string) ($r['message'] ?? '')); ?>
            <?php if ($extra !== '' || $msg !== ''): ?>
              <details>
                <summary class="muted small" style="cursor:pointer;">View</summary>
                <?php if ($msg !== ''): ?><div class="small" style="margin-top:6px;"><strong>Message:</strong> <?= nl2br(e($msg)) ?></div><?php endif; ?>
                <?php if ($extra !== ''): ?><pre class="muted small" style="margin-top:6px;white-space:pre-wrap;font-family:inherit;"><?= e($extra) ?></pre><?php endif; ?>
                <?php if (!empty($r['ip'])): ?><div class="muted small" style="margin-top:6px;">IP: <?= e($r['ip']) ?></div><?php endif; ?>
              </details>
            <?php else: ?><span class="muted small">—</span><?php endif; ?>
          </td>
          <td class="row-actions">
            <form method="post" action="<?= e(portal_url('leads.delete')) ?>" onsubmit="return confirm('Delete this lead?');" style="display:inline;">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="btn-portal-danger btn-sm" type="submit" title="Delete lead"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>
