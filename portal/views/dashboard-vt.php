<?php /** @var array $user @var array $data */
$pageTitle = ($user['role'] === 'vt_hired') ? 'VT (Hired) Dashboard' : 'VT (On-Pool) Dashboard';
$profile   = $data['profile'] ?? null;
$client    = $data['client'] ?? null;

$userPhoto = media_src($user['photo_url'] ?? '');
$userCover = $user['cover_url'] ?? '';
$vtName    = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: (string) $user['email'];
$roleTitle = trim((string) ($profile['role_title'] ?? '')) ?: trim((string) ($profile['department'] ?? ''));
$expYears  = (int) ($profile['experience_years'] ?? 0);
$csmCount  = count($data['csms'] ?? []);
$wdLink    = '';
if ($client && !empty($client['workday_link']))        { $wdLink = (string) $client['workday_link']; }
elseif ($profile && !empty($profile['workday_link']))  { $wdLink = (string) $profile['workday_link']; }
?>

<!-- HERO: cover photo + profile photo overlap (user's own) -->
<div class="card cd-cover-card" style="padding:0;overflow:hidden;">
  <?php $coverBg = $userCover !== '' ? $userCover : 'assets/default-banner.webp'; ?>
  <div class="cd-cover" style="background-image:url('<?= e($coverBg) ?>');"></div>
  <div class="cd-cover-body">
    <div class="cd-cover-photo-wrap">
      <?php if ($userPhoto): ?>
        <img class="cd-cover-photo" src="<?= e($userPhoto) ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
      <?php else: ?>
        <div class="cd-cover-photo placeholder"><?= e(strtoupper(mb_substr($user['first_name'] ?: $user['email'], 0, 1))) ?></div>
      <?php endif; ?>
    </div>
    <div class="cd-cover-meta">
      <div class="cd-hero-eyebrow"><i class="fa-solid fa-user-doctor"></i> <?= e(role_label($user['role'])) ?><?php if ($roleTitle !== ''): ?> &middot; <?= e($roleTitle) ?><?php endif; ?></div>
      <h2 class="cd-hero-h" style="margin:0 0 4px;"><?= e($vtName) ?></h2>
      <div class="cd-hero-sub muted">
        <?php if ($user['role'] === 'vt_hired' && $client): ?>
          <i class="fa-solid fa-building"></i> <?= e($client['company_name']) ?>
          &middot; <i class="fa-solid fa-user-tie"></i> <?= (int) $csmCount ?> CSM
        <?php endif; ?>
        <?php if ($expYears > 0): ?>
          <?= ($user['role'] === 'vt_hired' && $client) ? ' &middot; ' : '' ?><i class="fa-solid fa-briefcase"></i> <?= (int) $expYears ?> yr<?= $expYears === 1 ? '' : 's' ?> experience
        <?php endif; ?>
        <?php if (!empty($user['email'])): ?> &middot; <i class="fa-solid fa-envelope"></i> <?= e($user['email']) ?><?php endif; ?>
      </div>
    </div>
    <div class="cd-cover-actions">
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('eod.edit')) ?>"><i class="fa-solid fa-file-pen"></i> New EOD</a>
      <?php if ($wdLink !== ''): ?>
        <a class="btn-portal-secondary btn-sm" href="<?= e($wdLink) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-clock"></i> Workday Tracker</a>
      <?php endif; ?>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('profile')) ?>"><i class="fa-solid fa-camera"></i> Edit photos</a>
    </div>
  </div>
</div>

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
