<?php
/** @var array $user @var array $vts */
$pageTitle = 'My Virtual Teammates';
$subtitle  = $user['role'] === 'client'
    ? 'Your assigned Virtual Teammates — open the workday tracker, EOD reports, or full profile.'
    : ($user['role'] === 'csm'
        ? 'VTs across all your assigned client engagements.'
        : 'All Virtual Teammates in the portal (hired and on-pool).');

$nameOf = static function (array $v): string {
    $n = trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($v['email'] ?? 'Virtual Teammate');
};
$initial = static function (array $v): string {
    $n = trim(($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''));
    if ($n !== '') { return strtoupper(mb_substr($n, 0, 1)); }
    return strtoupper(mb_substr((string) ($v['email'] ?? '?'), 0, 1));
};
/** Resolve the workday URL for a VT row (matches the productivity-page resolver). */
$resolveWorkday = static function (array $v): string {
    foreach (['workday_link','profile_workday_link','cv_workday_link'] as $k) {
        $val = trim((string) ($v[$k] ?? ''));
        if ($val !== '' && preg_match('#^https?://#i', $val)) { return $val; }
    }
    foreach (['workday_tracker_id','profile_tracker_id','cv_workday_tracker_id'] as $k) {
        $tid = trim((string) ($v[$k] ?? ''));
        if ($tid !== '') { return 'https://workdaytracker.com/' . rawurlencode($tid); }
    }
    return '';
};
?>
<div class="card" style="margin-bottom:0;background:transparent;border:0;box-shadow:none;padding:0;">
  <div class="card-h" style="border:0;padding:0;margin-bottom:0;">
    <h3 style="margin:0;color:#fff;"><i class="fa-solid fa-user-doctor"></i> <?= e($pageTitle) ?> <span class="muted small">(<?= count($vts) ?>)</span></h3>
  </div>
</div>

<?php if (empty($vts)): ?>
  <div class="codex-selected-va-list codex-selected-va-list--empty">
    <p>No Virtual Teammates assigned yet. Talk to your CSM about adding one to your team.</p>
  </div>
<?php else: ?>
  <div class="codex-selected-va-list">
    <?php foreach ($vts as $v):
      $vid        = (int) ($v['user_id'] ?? $v['id'] ?? 0);
      $status     = $v['status'] ?? ($v['role'] ?? '');
      $isHired    = $status === 'hired' || $status === 'vt_hired';
      $role       = trim((string) ($v['role_title'] ?? ''));
      $department = trim((string) ($v['department'] ?? ''));
      $country    = trim((string) ($v['country'] ?? ''));
      $company    = trim((string) ($v['company_name'] ?? ''));
      $meta       = implode(' | ', array_filter([$department, $country]));
      $workdayUrl = $resolveWorkday($v);
      $profileUrl = $vid > 0 ? portal_url('vts.view', ['id' => $vid]) : '';
    ?>
      <article class="codex-selected-va-card" tabindex="0" aria-label="<?= e('Open profile for ' . $nameOf($v)) ?>">
        <a class="codex-selected-va-card__inner" <?php if ($profileUrl): ?>href="<?= e($profileUrl) ?>"<?php endif; ?>>
          <div class="codex-selected-va-card__media">
            <?php if (!empty($v['photo_url'])): ?>
              <img src="<?= e($v['photo_url']) ?>" alt="<?= e($nameOf($v)) ?>" loading="lazy"
                   onerror="this.onerror=null;this.outerHTML='<span class=&quot;codex-selected-va-card__avatar&quot;><?= e($initial($v)) ?></span>';">
            <?php else: ?>
              <span class="codex-selected-va-card__avatar"><?= e($initial($v)) ?></span>
            <?php endif; ?>
          </div>
          <p class="codex-selected-va-card__meta"><?= e($isHired ? 'Team member' : 'On talent pool') ?></p>
          <h3 class="codex-selected-va-card__name"><?= e($nameOf($v)) ?></h3>
          <?php if ($role !== ''): ?>
            <p class="codex-selected-va-card__role"><?= e($role) ?></p>
          <?php endif; ?>
          <?php if ($meta !== ''): ?>
            <p class="codex-selected-va-card__details"><?= e($meta) ?></p>
          <?php endif; ?>
          <?php if ($company !== ''): ?>
            <p class="codex-selected-va-card__details"><i class="fa-solid fa-building"></i> <?= e($company) ?></p>
          <?php endif; ?>
        </a>
        <div class="codex-selected-va-card__actions">
          <?php if ($workdayUrl !== ''): ?>
            <a class="codex-selected-va-card__btn" href="<?= e($workdayUrl) ?>" target="_blank" rel="noopener">Workday</a>
          <?php else: ?>
            <span class="codex-selected-va-card__btn codex-selected-va-card__btn--disabled" aria-disabled="true">Workday</span>
          <?php endif; ?>
          <a class="codex-selected-va-card__btn" href="<?= e(portal_url('productivity')) ?>#prod-eod">EOD</a>
          <?php if ($profileUrl !== ''): ?>
            <a class="codex-selected-va-card__btn" href="<?= e($profileUrl) ?>">Profile</a>
          <?php else: ?>
            <span class="codex-selected-va-card__btn codex-selected-va-card__btn--disabled" aria-disabled="true">Profile</span>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<style>
/* [selected_va_list] structure + features, themed for the dark portal (gold accent). */
.codex-selected-va-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin:18px 0;}
@media (max-width:1024px){.codex-selected-va-list{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media (max-width:767px){.codex-selected-va-list{grid-template-columns:1fr;}}
.codex-selected-va-card{
  position:relative;display:flex;flex-direction:column;min-height:100%;
  border:1px solid rgba(255,255,255,.08);border-radius:20px;
  background:linear-gradient(180deg,rgba(255,255,255,.06) 0%,rgba(255,255,255,.02) 100%);
  box-shadow:0 18px 44px rgba(0,0,0,.35);
  overflow:hidden;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
}
.codex-selected-va-card::before{
  content:'';position:absolute;inset:0 auto auto 0;width:100%;height:3px;
  background:linear-gradient(90deg,#3919BA 0%,#7c3aed 50%,#F6B845 100%);
}
.codex-selected-va-card:hover,.codex-selected-va-card:focus-visible{
  transform:translateY(-4px);border-color:rgba(247,185,69,.42);
  box-shadow:0 28px 56px -20px rgba(247,185,69,.35);outline:none;
}
.codex-selected-va-card__inner{
  display:flex;flex-direction:column;align-items:center;justify-content:flex-start;
  width:100%;padding:26px 20px 14px;text-align:center;
  text-decoration:none;color:#fff;flex:1;
}
.codex-selected-va-card__inner:hover{color:#fff;}
.codex-selected-va-card__media{display:flex;align-items:center;justify-content:center;margin-bottom:14px;}
.codex-selected-va-card__media img,
.codex-selected-va-card__avatar{
  width:96px;height:96px;border-radius:999px;
  display:flex;align-items:center;justify-content:center;
  object-fit:cover;border:2px solid rgba(247,185,69,.35);
  box-shadow:0 10px 26px rgba(0,0,0,.4);
  background:linear-gradient(135deg,#322a5a 0%,#4a4178 100%);
}
.codex-selected-va-card__avatar{font-size:34px;font-weight:800;color:#fff;}
.codex-selected-va-card__meta{
  margin:0 0 8px;font-size:10px;font-weight:800;letter-spacing:.10em;
  text-transform:uppercase;color:var(--gold,#d4a64a);
}
.codex-selected-va-card__name{
  margin:0;font-size:21px;line-height:1.2;color:#fff;font-weight:800;letter-spacing:-.2px;
}
.codex-selected-va-card__role{margin:8px 0 0;font-size:13px;line-height:1.5;color:rgba(255,255,255,.78);}
.codex-selected-va-card__details{margin:6px 0 0;font-size:11.5px;line-height:1.5;color:rgba(255,255,255,.55);}
.codex-selected-va-card__details i{color:rgba(255,255,255,.4);margin-right:3px;}
.codex-selected-va-card__actions{
  display:flex;gap:6px;flex-wrap:wrap;justify-content:center;
  padding:6px 20px 18px;
}
.codex-selected-va-card__btn{
  display:inline-flex;align-items:center;justify-content:center;
  padding:7px 14px;border-radius:999px;
  background:rgba(247,185,69,.14);color:var(--gold,#d4a64a) !important;
  font-size:10.5px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;
  text-decoration:none !important;border:1px solid rgba(247,185,69,.22);cursor:pointer;
  transition:background .15s,transform .15s,border-color .15s;font-family:inherit;line-height:1;
}
.codex-selected-va-card__btn:hover,.codex-selected-va-card__btn:focus{
  background:rgba(247,185,69,.26);border-color:rgba(247,185,69,.5);
  transform:translateY(-1px);color:var(--gold,#d4a64a) !important;outline:none;
}
.codex-selected-va-card__btn--disabled{opacity:.35;pointer-events:none;}
.codex-selected-va-list--empty{
  margin:18px 0;padding:18px 22px;
  background:rgba(255,255,255,.03);border:1px dashed rgba(255,255,255,.14);
  border-radius:18px;color:rgba(255,255,255,.7);
}
.codex-selected-va-list--empty p{margin:0;}
</style>
