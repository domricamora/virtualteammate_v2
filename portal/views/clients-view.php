<?php /** @var array $client @var ?array $login @var array $vts @var array $csms @var array $meetings */
$pageTitle = 'Client &middot; ' . $client['company_name'];
$subtitle  = 'Contract, login user, team & meetings.';
?>
<div class="card view-head">
  <div class="view-id">
    <div class="view-photo placeholder"><?= e(strtoupper(substr($client['company_name'], 0, 1))) ?></div>
    <div>
      <h2><?= e($client['company_name']) ?></h2>
      <div class="muted">
        <span class="pill pill-<?= e($client['contract_status']) ?>"><?= e($client['contract_status']) ?></span>
        <?php if (!empty($client['company_email'])): ?>&middot; <?= e($client['company_email']) ?><?php endif; ?>
        <?php if (!empty($client['company_domain'])): ?>&middot; <?= e($client['company_domain']) ?><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="view-actions">
    <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('clients.edit', ['id'=>$client['id']])) ?>"><i class="fa-solid fa-pen"></i> Edit</a>
    <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('clients')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to Clients</a>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <h3>Company &amp; contract</h3>
    <dl class="kv-list">
      <dt>Name</dt>            <dd><?= e($client['company_name']) ?></dd>
      <dt>Company email</dt>   <dd><?= e($client['company_email'] ?: '—') ?></dd>
      <dt>Domain</dt>          <dd><?= e($client['company_domain'] ?: '—') ?></dd>
      <dt>Billing contact</dt> <dd><?= e($client['billing_contact_email'] ?: '—') ?></dd>
      <dt>Contract status</dt> <dd><?= e($client['contract_status']) ?></dd>
      <dt>Workday link</dt>
      <dd>
        <?php if (!empty($client['workday_link'])): ?>
          <a href="<?= e($client['workday_link']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Open</a>
        <?php else: ?>—<?php endif; ?>
      </dd>
      <dt>HubSpot company id</dt>
      <dd class="muted small"><?= e($client['hubspot_company_id'] ?: '—') ?></dd>
      <dt>Created</dt>         <dd class="muted small"><?= e(fmt_dt($client['created_at'])) ?></dd>
    </dl>
  </div>

  <div class="card">
    <h3>Login user</h3>
    <?php if ($login): ?>
      <dl class="kv-list">
        <dt>Name</dt>      <dd><?= e(user_display_name($login)) ?></dd>
        <dt>Email</dt>     <dd><?= e($login['email']) ?></dd>
        <dt>Active</dt>    <dd><?= $login['active'] ? 'Yes' : 'No' ?></dd>
        <dt>Last login</dt><dd class="muted"><?= e(fmt_dt($login['last_login_at']) ?: '—') ?></dd>
      </dl>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('users.edit', ['id'=>$login['id']])) ?>"><i class="fa-solid fa-pen"></i> Edit user</a>
    <?php else: ?>
      <p class="muted">No login user linked. Add one from the Edit Client screen.</p>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($client['notes'])): ?>
  <div class="card">
    <h3>Notes</h3>
    <p class="vt-summary"><?= nl2br(e($client['notes'])) ?></p>
  </div>
<?php endif; ?>

<div class="grid-2">
  <div class="card">
    <h3>Hired VTs (<?= count($vts) ?>)</h3>
    <?php if (empty($vts)): ?>
      <p class="muted">No hired VTs assigned. Use the Assignments page to link VTs.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($vts as $v): ?>
          <li>
            <span class="people-name"><?= e(trim($v['first_name'] . ' ' . $v['last_name'])) ?></span>
            <span class="people-meta"><?= e($v['role_title'] ?? '') ?> &middot; <?= e($v['department'] ?? '') ?></span>
            <span class="people-meta"><i class="fa-solid fa-envelope"></i> <?= e($v['email']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3>CSMs (<?= count($csms) ?>)</h3>
    <?php if (empty($csms)): ?>
      <p class="muted">No CSMs assigned to this client yet.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($csms as $c): ?>
          <li>
            <span class="people-name"><a href="<?= e(portal_url('csms.view', ['id'=>$c['id']])) ?>"><?= e(trim($c['first_name'] . ' ' . $c['last_name'])) ?></a></span>
            <span class="people-meta"><i class="fa-solid fa-envelope"></i> <?= e($c['email']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3>Recent meetings (<?= count($meetings) ?>)</h3>
  <?php if (empty($meetings)): ?>
    <p class="muted">No meetings on file.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>When</th><th>With</th><th>Topic</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($meetings as $m):
          $att = trim(($m['att_fn'] ?? '') . ' ' . ($m['att_ln'] ?? ''));
        ?>
          <tr>
            <td><?= e(fmt_dt($m['scheduled_at'])) ?></td>
            <td><?= e(ucfirst($m['meeting_with_role'])) ?><?= $att ? ' — ' . e($att) : '' ?></td>
            <td><?= e($m['topic']) ?></td>
            <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
