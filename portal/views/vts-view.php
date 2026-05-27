<?php /** @var array $vt @var array $clients @var array $csms @var array $eod_recent */
$pageTitle = 'VT &middot; ' . trim(($vt['first_name'] ?? '') . ' ' . ($vt['last_name'] ?? ''));
$subtitle  = 'Profile, assignments, media and recent EOD reports.';
?>
<div class="card view-head">
  <div class="view-id">
    <?php if (!empty($vt['photo_url'])): ?>
      <img class="view-photo" src="<?= e($vt['photo_url']) ?>" alt="">
    <?php else: ?>
      <div class="view-photo placeholder"><?= e(strtoupper(substr($vt['first_name'] ?: $vt['email'], 0, 1))) ?></div>
    <?php endif; ?>
    <div>
      <h2><?= e(trim(($vt['first_name'] ?? '') . ' ' . ($vt['last_name'] ?? ''))) ?></h2>
      <div class="muted">
        <span class="pill pill-<?= e($vt['status']) ?>"><?= e(ucfirst($vt['status'])) ?></span>
        &middot; <?= e($vt['email']) ?>
        <?php if (!empty($vt['country'])): ?> &middot; <?= e($vt['country']) ?><?php endif; ?>
        <?php if (!$vt['active']): ?> &middot; <span class="pill pill-paused">Disabled</span><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="view-actions">
    <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('vts.edit', ['id'=>$vt['id']])) ?>"><i class="fa-solid fa-pen"></i> Edit</a>
    <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('vts')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to VTs</a>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <h3>Profile</h3>
    <dl class="kv-list">
      <dt>Department</dt>   <dd><?= e($vt['department'] ?: '—') ?></dd>
      <dt>Role title</dt>   <dd><?= e($vt['role_title'] ?: '—') ?></dd>
      <dt>Experience</dt>   <dd><?= (int) $vt['experience_years'] ?> yrs</dd>
      <dt>EHR / software</dt><dd><?= e($vt['ehr_software'] ?: '—') ?></dd>
      <dt>English</dt>      <dd><?= e($vt['english_level'] ?: '—') ?></dd>
      <dt>IQ band</dt>      <dd><?= e($vt['iq_band'] ?: '—') ?></dd>
      <dt>Technical band</dt><dd><?= e($vt['technical_band'] ?: '—') ?></dd>
      <dt>Workday tracker</dt>
      <dd>
        <?= e($vt['workday_tracker_id'] ?: '—') ?>
        <?php if (!empty($vt['workday_link'])): ?>
          &middot; <a href="<?= e($vt['workday_link']) ?>" target="_blank" rel="noopener">open</a>
        <?php endif; ?>
      </dd>
    </dl>
  </div>

  <div class="card">
    <h3>Contact</h3>
    <dl class="kv-list">
      <dt>Email</dt>            <dd><?= e($vt['email']) ?></dd>
      <dt>Phone</dt>            <dd><?= e($vt['phone'] ?: '—') ?></dd>
      <dt>Country</dt>          <dd><?= e($vt['country'] ?: '—') ?></dd>
      <dt>Last login</dt>       <dd class="muted"><?= e(fmt_dt($vt['last_login_at']) ?: '—') ?></dd>
      <dt>HubSpot contact id</dt><dd class="muted small"><?= e($vt['hubspot_contact_id'] ?: '—') ?></dd>
    </dl>
  </div>
</div>

<?php if (!empty($vt['summary']) || !empty($vt['experience_text'])): ?>
  <div class="card">
    <h3>Summary &amp; experience</h3>
    <?php if (!empty($vt['summary'])): ?>
      <p class="vt-summary"><?= nl2br(e($vt['summary'])) ?></p>
    <?php endif; ?>
    <?php if (!empty($vt['experience_text'])): ?>
      <h4 style="margin-top:14px;color:var(--text-mute);font-size:12px;text-transform:uppercase;letter-spacing:1.1px;">Experience</h4>
      <p class="vt-summary"><?= nl2br(e($vt['experience_text'])) ?></p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="card">
  <h3>Media</h3>
  <div class="media-tiles">
    <div class="media-tile">
      <div class="media-tile-h">Photo</div>
      <?php if (!empty($vt['photo_url'])): ?>
        <img src="<?= e($vt['photo_url']) ?>" alt="" class="media-thumb">
        <a class="btn-portal-secondary btn-sm" href="<?= e($vt['photo_url']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Open</a>
      <?php else: ?>
        <div class="muted">No photo on file.</div>
      <?php endif; ?>
    </div>
    <div class="media-tile">
      <div class="media-tile-h">Resume</div>
      <?php if (!empty($vt['resume_url'])): ?>
        <a class="btn-portal-secondary btn-sm" href="<?= e($vt['resume_url']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> Open resume</a>
      <?php else: ?>
        <div class="muted">No resume on file.</div>
      <?php endif; ?>
    </div>
    <div class="media-tile">
      <div class="media-tile-h">Intro video</div>
      <?php if (!empty($vt['video_url'])): ?>
        <a class="btn-portal-secondary btn-sm" href="<?= e($vt['video_url']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-video"></i> Open video</a>
      <?php else: ?>
        <div class="muted">No video on file.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <h3>Assigned client(s)</h3>
    <?php if (empty($clients)): ?>
      <p class="muted">Not currently assigned to any client.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($clients as $c): ?>
          <li>
            <span class="people-name"><a href="<?= e(portal_url('clients.view', ['id'=>$c['id']])) ?>"><?= e($c['company_name']) ?></a></span>
            <span class="people-meta">Engagement: <?= e($c['cv_status']) ?> &middot; started <?= e(fmt_dt($c['started_at'], 'Y-m-d')) ?></span>
            <?php if (!empty($c['cv_workday_link'])): ?>
              <span class="people-meta"><i class="fa-solid fa-up-right-from-square"></i> <a href="<?= e($c['cv_workday_link']) ?>" target="_blank" rel="noopener">Workday tracker</a></span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3>CSM(s) on this engagement</h3>
    <?php if (empty($csms)): ?>
      <p class="muted">No CSMs cover this VT's clients.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($csms as $c): ?>
          <li>
            <span class="people-name"><a href="<?= e(portal_url('csms.view', ['id'=>$c['id']])) ?>"><?= e(trim($c['first_name'] . ' ' . $c['last_name'])) ?></a></span>
            <span class="people-meta"><?= e($c['email']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3>Recent EOD reports (<?= count($eod_recent) ?>)</h3>
  <?php if (empty($eod_recent)): ?>
    <p class="muted">No EOD reports yet.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Date</th><th>Best work</th><th>Help needed</th><th>Updated</th></tr></thead>
      <tbody>
        <?php foreach ($eod_recent as $r): ?>
          <tr>
            <td><strong><?= e($r['report_date']) ?></strong></td>
            <td><?= e(mb_strimwidth($r['best_work'], 0, 60, '…')) ?></td>
            <td><?= e(mb_strimwidth($r['help_needed'], 0, 60, '…')) ?></td>
            <td class="muted small"><?= e(fmt_dt($r['updated_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
