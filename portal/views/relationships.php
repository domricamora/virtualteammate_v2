<?php
/**
 * Relationship dashboard — three stacked tables mirroring the WP plugin's
 * admin map view. Read-only; live from csm_clients + client_vts.
 *
 * @var array $clients
 * @var array $csmsByClient  client_id -> [{uid,email,first_name,last_name}]
 * @var array $vtsByClient   client_id -> [{uid,email,first_name,last_name,role,contract_status}]
 * @var array $allCsms
 * @var array $clientsByCsm  csm_id -> [client_id, ...]
 * @var array $allVts
 * @var array $clientsByVt   vt_id  -> [client_id, ...]
 * @var array $clientNames   client_id -> company_name
 */
$pageTitle = 'Relationships';
$subtitle  = 'Live view of the client &middot; CSM &middot; VT network. Synced from HubSpot company associations + owners.';

$nameOrEmail = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($u['email'] ?? '—');
};
?>
<div class="rel-tabs" role="tablist" style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;">
  <a href="#rel-clients" class="btn-portal-secondary btn-sm"><i class="fa-solid fa-building"></i> Clients <?= count($clients) ?></a>
  <a href="#rel-csms"    class="btn-portal-secondary btn-sm"><i class="fa-solid fa-user-tie"></i> CSMs <?= count($allCsms) ?></a>
  <a href="#rel-vts"     class="btn-portal-secondary btn-sm"><i class="fa-solid fa-user-doctor"></i> VTs <?= count($allVts) ?></a>
</div>

<!-- ─────────────── Clients ─────────────── -->
<div class="card" id="rel-clients">
  <div class="card-h">
    <h2 style="margin:0;"><i class="fa-solid fa-building"></i> Clients &rarr; CSM + VTs</h2>
    <span class="muted small"><?= count($clients) ?> client<?= count($clients) === 1 ? '' : 's' ?></span>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Client</th>
        <th>Status</th>
        <th>CSM(s)</th>
        <th>VT(s)</th>
        <th>HubSpot</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($clients)): ?>
        <tr><td colspan="5" class="muted">No clients in the portal yet — run a HubSpot sync to populate.</td></tr>
      <?php else: foreach ($clients as $c):
        $cid     = (int) $c['id'];
        $csmRows = $csmsByClient[$cid] ?? [];
        $vtRows  = $vtsByClient[$cid]  ?? [];
      ?>
        <tr>
          <td>
            <a href="<?= e(portal_url('clients.view', ['id' => $cid])) ?>"><strong><?= e($c['company_name']) ?></strong></a>
          </td>
          <td><span class="pill pill-<?= e($c['contract_status']) ?>"><?= e(ucfirst($c['contract_status'])) ?></span></td>
          <td>
            <?php if (empty($csmRows)): ?>
              <span class="muted">—</span>
            <?php else: foreach ($csmRows as $i => $cs): ?>
              <?php if ($i > 0): ?>, <?php endif; ?>
              <a href="<?= e(portal_url('csms.view', ['id' => (int) $cs['uid']])) ?>"><?= e($nameOrEmail($cs)) ?></a>
            <?php endforeach; endif; ?>
          </td>
          <td>
            <?php if (empty($vtRows)): ?>
              <span class="muted">—</span>
            <?php else: foreach ($vtRows as $i => $v): ?>
              <?php if ($i > 0): ?>, <?php endif; ?>
              <a href="<?= e(portal_url('vts.view', ['id' => (int) $v['uid']])) ?>"><?= e($nameOrEmail($v)) ?></a><?php
                if (!empty($v['contract_status']) && $v['contract_status'] !== 'active'): ?> <span class="pill pill-paused" style="font-size:10px;"><?= e($v['contract_status']) ?></span><?php endif; ?>
            <?php endforeach; endif; ?>
          </td>
          <td class="muted small">
            <?= !empty($c['hubspot_company_id']) ? 'co:' . e($c['hubspot_company_id']) : '—' ?>
            <?= !empty($c['hubspot_owner_id']) ? ' &middot; own:' . e($c['hubspot_owner_id']) : '' ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<!-- ─────────────── CSMs ─────────────── -->
<div class="card" id="rel-csms" style="margin-top:22px;">
  <div class="card-h">
    <h2 style="margin:0;"><i class="fa-solid fa-user-tie"></i> CSMs &rarr; Clients + (indirect) VTs</h2>
    <span class="muted small"><?= count($allCsms) ?> CSM<?= count($allCsms) === 1 ? '' : 's' ?></span>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>CSM</th>
        <th>Email</th>
        <th>Clients</th>
        <th>VTs (via clients)</th>
        <th>Owner&nbsp;ID</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($allCsms)): ?>
        <tr><td colspan="5" class="muted">No CSMs yet.</td></tr>
      <?php else: foreach ($allCsms as $cs):
        $cid       = (int) $cs['id'];
        $clientIds = $clientsByCsm[$cid] ?? [];
        // Indirectly: every VT linked to any of this CSM's clients.
        $vtSet = [];
        foreach ($clientIds as $clientId) {
          foreach ($vtsByClient[$clientId] ?? [] as $vRow) {
            $vtSet[(int) $vRow['uid']] = $vRow;
          }
        }
      ?>
        <tr>
          <td>
            <a href="<?= e(portal_url('csms.view', ['id' => $cid])) ?>"><strong><?= e($nameOrEmail($cs)) ?></strong></a>
          </td>
          <td class="muted small"><?= e($cs['email']) ?></td>
          <td>
            <?php if (empty($clientIds)): ?>
              <span class="muted">—</span>
            <?php else: foreach ($clientIds as $i => $clientId): ?>
              <?php if ($i > 0): ?>, <?php endif; ?>
              <a href="<?= e(portal_url('clients.view', ['id' => $clientId])) ?>"><?= e($clientNames[$clientId] ?? ('Client ' . $clientId)) ?></a>
            <?php endforeach; endif; ?>
          </td>
          <td>
            <?php if (empty($vtSet)): ?>
              <span class="muted">—</span>
            <?php else: $j = 0; foreach ($vtSet as $vid => $v): ?>
              <?php if ($j > 0): ?>, <?php endif; $j++; ?>
              <a href="<?= e(portal_url('vts.view', ['id' => (int) $vid])) ?>"><?= e($nameOrEmail($v)) ?></a>
            <?php endforeach; endif; ?>
          </td>
          <td class="muted small"><?= !empty($cs['hubspot_owner_id']) ? e($cs['hubspot_owner_id']) : '—' ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<!-- ─────────────── VTs ─────────────── -->
<div class="card" id="rel-vts" style="margin-top:22px;">
  <div class="card-h">
    <h2 style="margin:0;"><i class="fa-solid fa-user-doctor"></i> VTs &rarr; Client + (indirect) CSM</h2>
    <span class="muted small"><?= count($allVts) ?> VT<?= count($allVts) === 1 ? '' : 's' ?></span>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>VT</th>
        <th>Email</th>
        <th>Status</th>
        <th>Client(s)</th>
        <th>CSM(s) via clients</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($allVts)): ?>
        <tr><td colspan="5" class="muted">No VTs yet.</td></tr>
      <?php else: foreach ($allVts as $v):
        $vid       = (int) $v['id'];
        $clientIds = $clientsByVt[$vid] ?? [];
        $csmSet    = [];
        foreach ($clientIds as $clientId) {
          foreach ($csmsByClient[$clientId] ?? [] as $cRow) {
            $csmSet[(int) $cRow['uid']] = $cRow;
          }
        }
      ?>
        <tr>
          <td>
            <a href="<?= e(portal_url('vts.view', ['id' => $vid])) ?>"><strong><?= e($nameOrEmail($v)) ?></strong></a>
          </td>
          <td class="muted small"><?= e($v['email']) ?></td>
          <td><span class="pill pill-<?= e($v['role'] === 'vt_hired' ? 'active' : 'paused') ?>"><?= e($v['role'] === 'vt_hired' ? 'Hired' : 'On pool') ?></span></td>
          <td>
            <?php if (empty($clientIds)): ?>
              <span class="muted">—</span>
            <?php else: foreach ($clientIds as $i => $clientId): ?>
              <?php if ($i > 0): ?>, <?php endif; ?>
              <a href="<?= e(portal_url('clients.view', ['id' => $clientId])) ?>"><?= e($clientNames[$clientId] ?? ('Client ' . $clientId)) ?></a>
            <?php endforeach; endif; ?>
          </td>
          <td>
            <?php if (empty($csmSet)): ?>
              <span class="muted">—</span>
            <?php else: $j = 0; foreach ($csmSet as $csid => $cs): ?>
              <?php if ($j > 0): ?>, <?php endif; $j++; ?>
              <a href="<?= e(portal_url('csms.view', ['id' => (int) $csid])) ?>"><?= e($nameOrEmail($cs)) ?></a>
            <?php endforeach; endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
