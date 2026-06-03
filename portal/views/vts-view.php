<?php
/** @var array $vt @var array $clients @var array $csms @var array $eod_recent */
$fullName  = trim(($vt['first_name'] ?? '') . ' ' . ($vt['last_name'] ?? ''));
$pageTitle = 'VT · ' . ($fullName ?: $vt['email']);
$subtitle  = 'Profile, media, assignments and recent EOD reports.';

$photoUrl  = media_src($vt['photo_url']  ?? '');
$coverUrl  = media_src($vt['cover_url']  ?? '');
$resumeUrl = $vt['resume_url'] ?? '';
$videoUrl  = $vt['video_url']  ?? '';

$initials = strtoupper(mb_substr($vt['first_name'] ?: $vt['email'], 0, 1)
                     . ($vt['last_name'] ? mb_substr($vt['last_name'], 0, 1) : ''));

// Is the URL a portal-served local file (auth-gated) or an external link?
$isLocal = static fn(string $u): bool => str_starts_with($u, 'index.php?p=media');
?>

<!-- ── Hero header with cover banner + circular avatar ──────────────── -->
<div class="vt-hero">
  <div class="vt-hero-cover"<?= $coverUrl ? ' style="background-image:url(\'' . e($coverUrl) . '\');"' : '' ?>></div>
  <div class="vt-hero-body">
    <div class="vt-hero-avatar">
      <?php if ($photoUrl !== ''): ?>
        <img src="<?= e($photoUrl) ?>" alt="<?= e($fullName) ?>" loading="lazy"
             onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
      <?php else: ?>
        <span class="vt-hero-initials"><?= e($initials) ?></span>
      <?php endif; ?>
    </div>
    <div class="vt-hero-meta">
      <h1 class="vt-hero-name"><?= e($fullName ?: $vt['email']) ?></h1>
      <div class="vt-hero-sub">
        <?php if (!empty($vt['role_title'])): ?>
          <span><i class="fa-solid fa-briefcase"></i> <?= e($vt['role_title']) ?></span>
        <?php endif; ?>
        <?php if (!empty($vt['country'])): ?>
          <span><i class="fa-solid fa-location-dot"></i> <?= e($vt['country']) ?></span>
        <?php endif; ?>
        <span><i class="fa-solid fa-envelope"></i> <?= e($vt['email']) ?></span>
        <?php if (!empty($vt['phone'])): ?>
          <span><i class="fa-solid fa-phone"></i> <?= e($vt['phone']) ?></span>
        <?php endif; ?>
      </div>
      <div class="vt-hero-pills">
        <span class="pill pill-<?= e($vt['status']) ?>"><?= e(ucfirst($vt['status'])) ?></span>
        <?php if (!$vt['active']): ?><span class="pill pill-paused">Disabled</span><?php endif; ?>
        <?php if (!empty($vt['english_level'])): ?>
          <span class="pill pill-scheduled">English: <?= e($vt['english_level']) ?></span>
        <?php endif; ?>
        <?php if ((int) ($vt['experience_years'] ?? 0) > 0): ?>
          <span class="pill pill-active"><?= (int) $vt['experience_years'] ?> yrs experience</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="vt-hero-actions">
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('vts.edit', ['id' => $vt['id']])) ?>"><i class="fa-solid fa-pen"></i> Edit profile</a>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('vts')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to VTs</a>
    </div>
  </div>
</div>

<!-- ── Quick-stat strip ────────────────────────────────────────────── -->
<div class="vt-stats">
  <div class="vt-stat">
    <div class="vt-stat-l">Department</div>
    <div class="vt-stat-v"><?= e($vt['department'] ?: '—') ?></div>
  </div>
  <div class="vt-stat">
    <div class="vt-stat-l">Active clients</div>
    <div class="vt-stat-v"><?= count(array_filter($clients, fn($c) => ($c['cv_status'] ?? '') === 'active')) ?></div>
  </div>
  <div class="vt-stat">
    <div class="vt-stat-l">IQ band</div>
    <div class="vt-stat-v"><?= e($vt['iq_band'] ?: '—') ?></div>
  </div>
  <div class="vt-stat">
    <div class="vt-stat-l">Technical band</div>
    <div class="vt-stat-v"><?= e($vt['technical_band'] ?: '—') ?></div>
  </div>
  <div class="vt-stat">
    <div class="vt-stat-l">Last login</div>
    <div class="vt-stat-v"><?= (local_dt($vt['last_login_at']) ?: '—') ?></div>
  </div>
</div>

<!-- ── Media: video + resume side-by-side with inline previews ──────── -->
<div class="card">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-photo-film"></i> Media</h3>
    <span class="muted small">Resume + intro video imported from HubSpot, served locally.</span>
  </div>
  <div class="vt-media-grid">

    <!-- Intro video -->
    <div class="vt-media-card">
      <div class="vt-media-h"><i class="fa-solid fa-video"></i> Intro video</div>
      <?php if ($videoUrl !== ''): ?>
        <div class="vt-video-frame">
          <video controls preload="metadata" playsinline poster="<?= $photoUrl !== '' ? e($photoUrl) : '' ?>">
            <source src="<?= e($videoUrl) ?>">
            Your browser doesn't support inline video.
          </video>
        </div>
        <div class="vt-media-foot">
          <a class="btn-portal-secondary btn-sm" href="<?= e($videoUrl) ?>" target="_blank" rel="noopener">
            <i class="fa-solid fa-up-right-from-square"></i>
            <?= $isLocal($videoUrl) ? 'Open in new tab' : 'Open external link' ?>
          </a>
          <?php if ($isLocal($videoUrl)): ?>
            <a class="btn-portal-secondary btn-sm" href="<?= e($videoUrl) ?>&dl=1" download>
              <i class="fa-solid fa-download"></i> Download
            </a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="vt-media-empty">
          <i class="fa-solid fa-video-slash"></i>
          <p>No intro video on file.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Resume -->
    <div class="vt-media-card">
      <div class="vt-media-h"><i class="fa-solid fa-file-pdf"></i> Resume</div>
      <?php if ($resumeUrl !== ''): ?>
        <div class="vt-pdf-frame">
          <embed src="<?= e($resumeUrl) ?>#toolbar=1&navpanes=0" type="application/pdf">
        </div>
        <div class="vt-media-foot">
          <a class="btn-portal-secondary btn-sm" href="<?= e($resumeUrl) ?>" target="_blank" rel="noopener">
            <i class="fa-solid fa-up-right-from-square"></i>
            <?= $isLocal($resumeUrl) ? 'Open full-page' : 'Open external link' ?>
          </a>
          <?php if ($isLocal($resumeUrl)): ?>
            <a class="btn-portal-secondary btn-sm" href="<?= e($resumeUrl) ?>" download>
              <i class="fa-solid fa-download"></i> Download
            </a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="vt-media-empty">
          <i class="fa-solid fa-file-circle-xmark"></i>
          <p>No resume on file.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── Profile + Contact + HubSpot details ──────────────────────────── -->
<div class="grid-2">
  <div class="card">
    <h3><i class="fa-solid fa-id-badge"></i> Profile</h3>
    <dl class="kv-list">
      <dt>Role title</dt>     <dd><?= e($vt['role_title'] ?: '—') ?></dd>
      <dt>Department</dt>     <dd><?= e($vt['department'] ?: '—') ?></dd>
      <dt>Experience</dt>     <dd><?= (int) $vt['experience_years'] ?> yrs</dd>
      <dt>EHR / software</dt> <dd><?= e($vt['ehr_software'] ?: '—') ?></dd>
      <dt>English</dt>        <dd><?= e($vt['english_level'] ?: '—') ?></dd>
      <dt>IQ band</dt>        <dd><?= e($vt['iq_band'] ?: '—') ?></dd>
      <dt>Technical band</dt> <dd><?= e($vt['technical_band'] ?: '—') ?></dd>
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
    <h3><i class="fa-solid fa-address-card"></i> Contact &amp; HubSpot</h3>
    <dl class="kv-list">
      <dt>Email</dt>             <dd><?= e($vt['email']) ?></dd>
      <dt>Phone</dt>             <dd><?= e($vt['phone'] ?: '—') ?></dd>
      <dt>Country</dt>           <dd><?= e($vt['country'] ?: '—') ?></dd>
      <dt>Last login</dt>        <dd class="muted"><?= (local_dt($vt['last_login_at']) ?: '—') ?></dd>
      <dt>HubSpot contact id</dt><dd class="muted small"><?= e($vt['hubspot_contact_id'] ?: '—') ?></dd>
      <?php if (!empty($vt['ci_role'])): ?>
        <dt>CI role</dt><dd><?= e($vt['ci_role']) ?></dd>
      <?php endif; ?>
      <?php if (!empty($vt['disc_profile'])): ?>
        <dt>DISC profile</dt><dd><?= e($vt['disc_profile']) ?></dd>
      <?php endif; ?>
      <?php if (!empty($vt['hipaa_certified'])): ?>
        <dt>HIPAA</dt><dd><?= e($vt['hipaa_certified']) ?></dd>
      <?php endif; ?>
    </dl>
  </div>
</div>

<?php if (!empty($vt['summary']) || !empty($vt['experience_text'])): ?>
  <div class="card">
    <h3><i class="fa-solid fa-circle-info"></i> Summary &amp; experience</h3>
    <?php if (!empty($vt['summary'])): ?>
      <p class="vt-summary"><?= nl2br(e($vt['summary'])) ?></p>
    <?php endif; ?>
    <?php if (!empty($vt['experience_text'])): ?>
      <h4 style="margin-top:14px;color:rgba(255,255,255,.6);font-size:11px;text-transform:uppercase;letter-spacing:1.1px;">Experience</h4>
      <p class="vt-summary"><?= nl2br(e($vt['experience_text'])) ?></p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="grid-2">
  <div class="card">
    <h3><i class="fa-solid fa-building"></i> Assigned client(s)</h3>
    <?php if (empty($clients)): ?>
      <p class="muted">Not currently assigned to any client.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($clients as $c): ?>
          <li>
            <span class="people-name"><a href="<?= e(portal_url('clients.view', ['id' => $c['id']])) ?>"><?= e($c['company_name']) ?></a></span>
            <span class="people-meta">Engagement: <?= e($c['cv_status']) ?> &middot; started <?= local_dt($c['started_at'], 'Y-m-d') ?></span>
            <?php if (!empty($c['cv_workday_link'])): ?>
              <span class="people-meta"><i class="fa-solid fa-up-right-from-square"></i> <a href="<?= e($c['cv_workday_link']) ?>" target="_blank" rel="noopener">Workday tracker</a></span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3><i class="fa-solid fa-user-tie"></i> CSM(s) on this engagement</h3>
    <?php if (empty($csms)): ?>
      <p class="muted">No CSMs cover this VT's clients.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($csms as $c): ?>
          <li>
            <span class="people-name"><a href="<?= e(portal_url('csms.view', ['id' => $c['id']])) ?>"><?= e(trim($c['first_name'] . ' ' . $c['last_name'])) ?></a></span>
            <span class="people-meta"><?= e($c['email']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3><i class="fa-solid fa-file-pen"></i> Recent EOD reports (<?= count($eod_recent) ?>)</h3>
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
            <td class="muted small"><?= local_dt($r['updated_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
