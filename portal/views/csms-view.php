<?php /** @var array $csm @var array $clients @var array $meetings */
$pageTitle = 'CSM &middot; ' . user_display_name($csm);
$subtitle  = 'Read-only profile + assigned clients.';
?>
<div class="card view-head">
  <div class="view-id">
    <?php if (!empty($csm['photo_url'])): ?>
      <img class="view-photo" src="<?= e(media_src($csm['photo_url'])) ?>" alt="" loading="lazy"
           onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
    <?php else: ?>
      <div class="view-photo placeholder"><?= e(strtoupper(substr($csm['first_name'] ?: $csm['email'], 0, 1))) ?></div>
    <?php endif; ?>
    <div>
      <h2><?= e(user_display_name($csm)) ?></h2>
      <div class="muted"><?= role_badge($csm['role']) ?> &middot; <?= e($csm['email']) ?> &middot; <?= $csm['active'] ? 'Active' : 'Disabled' ?></div>
    </div>
  </div>
  <div class="view-actions">
    <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('users.edit', ['id'=>$csm['id']])) ?>"><i class="fa-solid fa-pen"></i> Edit</a>
    <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('csms')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to CSMs</a>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <h3>Contact</h3>
    <dl class="kv-list">
      <dt>Email</dt>           <dd><?= e($csm['email']) ?></dd>
      <dt>Phone</dt>           <dd><?= e($csm['phone'] ?: '—') ?></dd>
      <dt>Country</dt>         <dd><?= e($csm['country'] ?: '—') ?></dd>
      <dt>Last login</dt>      <dd class="muted"><?= e(fmt_dt($csm['last_login_at']) ?: '—') ?></dd>
      <dt>HubSpot contact id</dt><dd class="muted small"><?= e($csm['hubspot_contact_id'] ?: '—') ?></dd>
      <dt>Created</dt>         <dd class="muted small"><?= e(fmt_dt($csm['created_at'])) ?></dd>
    </dl>
  </div>

  <div class="card">
    <h3>Assigned clients (<?= count($clients) ?>)</h3>
    <?php if (empty($clients)): ?>
      <p class="muted">No clients assigned. Use the Assignments page to attach this CSM to one or more clients.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($clients as $c): ?>
          <li>
            <span class="people-name"><a href="<?= e(portal_url('clients.view', ['id'=>$c['id']])) ?>"><?= e($c['company_name']) ?></a></span>
            <span class="people-meta">Contract: <?= e($c['contract_status']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3>Recent meetings (<?= count($meetings) ?>)</h3>
  <?php if (empty($meetings)): ?>
    <p class="muted">No meetings yet.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>When</th><th>Client</th><th>Topic</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($meetings as $m): ?>
          <tr>
            <td><?= e(fmt_dt($m['scheduled_at'])) ?></td>
            <td><?= e($m['company_name']) ?></td>
            <td><?= e($m['topic']) ?></td>
            <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
