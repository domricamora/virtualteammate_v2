<?php /** @var array $user @var array $data */
$pageTitle = ($user['role'] === 'vt_hired') ? 'VT (Hired) Dashboard' : 'VT (On-Pool) Dashboard';
$profile   = $data['profile'] ?? null;
$client    = $data['client'] ?? null;
?>

<div class="card">
  <h3>My profile</h3>
  <?php if (!$profile): ?>
    <p class="muted">No VT profile yet. Ask a super admin to create one for you.</p>
  <?php else: ?>
    <div class="vt-profile-summary">
      <div><span class="muted">Department:</span> <strong><?= e($profile['department'] ?: '—') ?></strong></div>
      <div><span class="muted">Role:</span> <strong><?= e($profile['role_title'] ?: '—') ?></strong></div>
      <div><span class="muted">Experience:</span> <strong><?= (int) $profile['experience_years'] ?> yrs</strong></div>
      <div><span class="muted">EHR / Software:</span> <strong><?= e($profile['ehr_software'] ?: '—') ?></strong></div>
      <div><span class="muted">English:</span> <strong><?= e($profile['english_level'] ?: '—') ?></strong></div>
      <div><span class="muted">Status:</span> <strong><?= e(role_label($user['role'])) ?></strong></div>
    </div>
    <?php if ($profile['summary']): ?>
      <p class="vt-summary"><?= nl2br(e($profile['summary'])) ?></p>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php if ($user['role'] === 'vt_hired'): ?>
  <div class="grid-2">
    <div class="card">
      <h3>My client</h3>
      <?php if (!$client): ?>
        <p class="muted">You are not currently linked to an active client engagement.</p>
      <?php else: ?>
        <p><strong><?= e($client['company_name']) ?></strong></p>
        <p class="muted">Contract status: <?= e($client['contract_status']) ?></p>
        <?php if (!empty($client['workday_link'])): ?>
          <p><a href="<?= e($client['workday_link']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Workday tracker</a></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3>My CSM</h3>
      <?php if (empty($data['csms'])): ?>
        <p class="muted">No CSM assigned to this engagement yet.</p>
      <?php else: ?>
        <ul class="people-list">
          <?php foreach ($data['csms'] as $c): ?>
            <li>
              <span class="people-name"><?= e(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))) ?></span>
              <span class="people-meta"><?= e($c['email']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-h">
    <h3>Recent EOD reports</h3>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('eod.edit')) ?>"><i class="fa-solid fa-plus"></i> New EOD report</a>
  </div>
  <?php if (empty($data['eod_recent'])): ?>
    <p class="muted">No reports yet. Submit one to keep your client and CSM in the loop.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Date</th><th>Best work today</th><th>Updated</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($data['eod_recent'] as $r): ?>
          <tr>
            <td><?= e($r['report_date']) ?></td>
            <td><?= e(mb_strimwidth($r['best_work'], 0, 80, '…')) ?></td>
            <td class="muted"><?= local_dt($r['updated_at']) ?></td>
            <td><a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('eod.edit', ['id'=>$r['id']])) ?>">Edit</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
