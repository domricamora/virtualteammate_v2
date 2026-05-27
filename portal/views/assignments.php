<?php /** @var array $clients @var array $csms @var array $hired @var array $csm_links @var array $vt_links */
$pageTitle = 'Assignments';
$subtitle  = 'Toggle CSM ↔ Client coverage, and Client ↔ hired-VT assignments.';
?>
<div class="card">
  <h3>CSM ↔ Client</h3>
  <?php if (empty($csms) || empty($clients)): ?>
    <p class="muted">Need at least one active CSM and one client to assign.</p>
  <?php else: ?>
    <div class="matrix-wrap">
      <table class="matrix">
        <thead>
          <tr><th></th><?php foreach ($clients as $cl): ?><th><?= e($cl['company_name']) ?></th><?php endforeach; ?></tr>
        </thead>
        <tbody>
          <?php foreach ($csms as $cs): $cId = (int) $cs['id']; ?>
            <tr>
              <th class="matrix-row-h"><?= e(trim(($cs['first_name'] ?? '') . ' ' . ($cs['last_name'] ?? ''))) ?></th>
              <?php foreach ($clients as $cl): $on = !empty($csm_links[$cId][(int)$cl['id']]); ?>
                <td>
                  <form method="post" action="<?= e(portal_url('assignments.csm')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="csm_id" value="<?= $cId ?>">
                    <input type="hidden" name="client_id" value="<?= (int) $cl['id'] ?>">
                    <?php if ($on): ?>
                      <button class="matrix-cell on" title="Click to unassign" type="submit"><i class="fa-solid fa-check"></i></button>
                    <?php else: ?>
                      <input type="hidden" name="on" value="1">
                      <button class="matrix-cell" title="Click to assign" type="submit"></button>
                    <?php endif; ?>
                  </form>
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3>Hired VT ↔ Client</h3>
  <?php if (empty($hired) || empty($clients)): ?>
    <p class="muted">Need at least one hired VT and one client to assign.</p>
  <?php else: ?>
    <div class="matrix-wrap">
      <table class="matrix">
        <thead>
          <tr><th></th><?php foreach ($clients as $cl): ?><th><?= e($cl['company_name']) ?></th><?php endforeach; ?></tr>
        </thead>
        <tbody>
          <?php foreach ($hired as $v): $vId = (int) $v['id']; ?>
            <tr>
              <th class="matrix-row-h">
                <?= e(trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''))) ?>
                <span class="muted small"><?= e($v['role_title'] ?? '') ?></span>
              </th>
              <?php foreach ($clients as $cl): $on = !empty($vt_links[$vId][(int)$cl['id']]); ?>
                <td>
                  <form method="post" action="<?= e(portal_url('assignments.vt')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="vt_id" value="<?= $vId ?>">
                    <input type="hidden" name="client_id" value="<?= (int) $cl['id'] ?>">
                    <?php if ($on): ?>
                      <button class="matrix-cell on" title="Click to unassign" type="submit"><i class="fa-solid fa-check"></i></button>
                    <?php else: ?>
                      <input type="hidden" name="on" value="1">
                      <button class="matrix-cell" title="Click to assign" type="submit"></button>
                    <?php endif; ?>
                  </form>
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
