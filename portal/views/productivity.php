<?php
/** @var array $user @var array $vts @var array $eod */
$pageTitle = 'Productivity Reports';
$subtitle  = 'Workday tracking and End-of-Day reports for your team, in one place.';

$nameOrEmail = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($u['email'] ?? '—');
};
$initial = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    if ($n !== '') { return strtoupper(mb_substr($n, 0, 1)); }
    return strtoupper(mb_substr((string) ($u['email'] ?? '?'), 0, 1));
};
$resolveWorkday = static function (array $v): string {
    $cvLink = (string) ($v['cv_workday_link'] ?? '');
    if ($cvLink !== '' && preg_match('#^https?://#i', $cvLink)) { return $cvLink; }
    $pLink = (string) ($v['profile_workday_link'] ?? ($v['workday_link'] ?? ''));
    if ($pLink !== '' && preg_match('#^https?://#i', $pLink)) { return $pLink; }
    $tid = (string) ($v['cv_workday_tracker_id'] ?? ($v['profile_tracker_id'] ?? ($v['workday_tracker_id'] ?? '')));
    if ($tid !== '') { return 'https://workdaytracker.com/app/public-report/' . rawurlencode($tid) . '/'; }
    return '';
};
// One VT card — reused inside each client modal (CSM/admin) and the flat grid (client/VT).
$renderVtCard = static function (array $v) use ($resolveWorkday, $nameOrEmail, $initial): void {
    $vid   = (int) ($v['user_id'] ?? 0);
    $href  = $resolveWorkday($v);
    $thumb = media_thumb_src($v['photo_url'] ?? '');
    $role  = trim((string) ($v['role_title'] ?? '')) ?: (trim((string) ($v['department'] ?? '')) ?: 'Virtual Teammate');
    $name  = $nameOrEmail($v);
    ?>
    <div class="csm-wd-vt">
      <div class="csm-wd-vt__top">
        <?php if ($thumb !== ''): ?>
          <img class="csm-wd-vt__av" src="<?= e($thumb) ?>" alt="" loading="lazy"
               onerror="this.onerror=null;this.outerHTML='<span class=&quot;csm-wd-vt__av csm-wd-vt__av--ph&quot;><?= e($initial($v)) ?></span>';">
        <?php else: ?>
          <span class="csm-wd-vt__av csm-wd-vt__av--ph"><?= e($initial($v)) ?></span>
        <?php endif; ?>
        <div class="csm-wd-vt__meta">
          <div class="csm-wd-vt__name"><?= e($name) ?></div>
          <div class="csm-wd-vt__role"><?= e($role) ?></div>
        </div>
      </div>
      <div class="csm-wd-vt__btns">
        <?php if ($href !== ''): ?>
          <a class="btn-portal-primary btn-sm" href="<?= e($href) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-clock"></i> Workday Tracker</a>
        <?php else: ?>
          <span class="btn-portal-secondary btn-sm prod-nolink" title="No workday link set"><i class="fa-solid fa-link-slash"></i> No tracker</span>
        <?php endif; ?>
        <button type="button" class="btn-portal-secondary btn-sm" data-vt-eod="<?= $vid ?>" data-vt-name="<?= e($name) ?>"><i class="fa-solid fa-file-pen"></i> EOD Reports</button>
      </div>
    </div>
    <?php
};

$isVtSelf    = (string) ($user['role'] ?? '') === 'vt_hired';
$multiClient = in_array((string) ($user['role'] ?? ''), ['csm', 'super_admin'], true);
$trackable = 0; foreach ($vts as $v) { if ($resolveWorkday($v) !== '') { $trackable++; } }
$reportDates = array_filter(array_map(static fn ($r) => (string) ($r['report_date'] ?? ''), $eod));

// Group the workday VTs by client (company) — the CSM tracker view.
$vtGroups = [];
foreach ($vts as $v) {
    $key = trim((string) ($v['company_name'] ?? '')) ?: 'Unassigned';
    $vtGroups[$key][] = $v;
}
ksort($vtGroups, SORT_NATURAL | SORT_FLAG_CASE);
?>

<!-- Summary -->
<div class="stat-grid prod-stats">
  <div class="stat-card"><div class="stat-num"><?= count($vts) ?></div><div class="stat-lbl">Virtual Teammates</div></div>
  <div class="stat-card"><div class="stat-num"><?= (int) $trackable ?></div><div class="stat-lbl">With live trackers</div></div>
  <div class="stat-card"><div class="stat-num"><?= count($eod) ?></div><div class="stat-lbl">EOD reports</div></div>
  <div class="stat-card"><div class="stat-num"><?= $reportDates ? e(date('M j', strtotime(max($reportDates)))) : '—' ?></div><div class="stat-lbl">Latest report</div></div>
</div>

<div class="prod-tabs" role="tablist">
  <a href="#prod-workday" class="prod-tab is-on" data-tab="workday"><i class="fa-solid fa-clock"></i> Workday Tracker <span class="prod-tab-c"><?= count($vts) ?></span></a>
  <a href="#prod-eod"     class="prod-tab"       data-tab="eod"><i class="fa-solid fa-file-pen"></i> EOD Reports <span class="prod-tab-c"><?= count($eod) ?></span></a>
</div>

<!-- WORKDAY — CSM/admin: client cards open a modal of VT cards; client/VT: VT cards directly -->
<section class="prod-section" id="prod-workday" data-section="workday">
  <?php if (empty($vts)): ?>
    <div class="card prod-empty"><i class="fa-solid fa-clock"></i><h3>No Virtual Teammates to track yet</h3><p class="muted">Trackers appear here once VTs are on your accounts.</p></div>

  <?php elseif ($multiClient): ?>
    <div class="csm-wd-grid">
      <?php $ci = 0; foreach ($vtGroups as $company => $groupVts): $ci++; ?>
        <button type="button" class="csm-wd-card" data-modal-target="wdModal<?= $ci ?>" style="animation-delay:<?= number_format(min($ci, 16) * 0.05, 2) ?>s;">
          <span class="csm-wd-card__eyebrow"><i class="fa-solid fa-building"></i> Client account</span>
          <span class="csm-wd-card__title"><?= e($company) ?></span>
          <span class="csm-wd-card__count"><?= count($groupVts) ?> team member<?= count($groupVts) === 1 ? '' : 's' ?></span>
          <span class="csm-wd-card__cta">Open team <i class="fa-solid fa-arrow-right"></i></span>
        </button>
      <?php endforeach; ?>
    </div>

    <?php $ci = 0; foreach ($vtGroups as $company => $groupVts): $ci++; ?>
      <div class="csm-wd-modal" id="wdModal<?= $ci ?>" hidden>
        <div class="csm-wd-modal__overlay" data-modal-close></div>
        <div class="csm-wd-modal__dialog" role="dialog" aria-modal="true" aria-label="<?= e($company) ?> team">
          <button type="button" class="csm-wd-modal__close" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
          <div class="csm-wd-modal__head">
            <span class="csm-wd-modal__eyebrow"><i class="fa-solid fa-building"></i> Client account</span>
            <h3><?= e($company) ?></h3>
            <p class="muted small"><?= count($groupVts) ?> team member<?= count($groupVts) === 1 ? '' : 's' ?> · open a tracker or read their EOD reports</p>
          </div>
          <div class="csm-wd-vt-grid">
            <?php foreach ($groupVts as $v) { $renderVtCard($v); } ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  <?php else: ?>
    <div class="csm-wd-vt-grid csm-wd-vt-grid--flat">
      <?php foreach ($vts as $v) { $renderVtCard($v); } ?>
    </div>
  <?php endif; ?>
</section>

<!-- EOD -->
<section class="prod-section" id="prod-eod" data-section="eod" style="display:none;">
  <?php if ($isVtSelf): ?>
    <div class="card prod-eod-form-card">
      <div class="card-h">
        <h3 style="margin:0;"><i class="fa-solid fa-file-pen"></i> Log an End-of-Day report</h3>
        <button type="button" class="btn-portal-secondary btn-sm" id="prodEodToggle"><i class="fa-solid fa-plus"></i> New report</button>
      </div>
      <form class="prod-eod-form" id="prodEodForm" method="post" action="<?= e(portal_url('eod.edit')) ?>" hidden>
        <?= csrf_field() ?>
        <div class="prod-eod-grid">
          <label class="prod-eod-f"><span>Date</span><input type="date" name="report_date" value="<?= e(date('Y-m-d')) ?>" required></label>
          <label class="prod-eod-f"><span>KPI name</span><input type="text" name="kpi_name" placeholder="e.g. Calls handled"></label>
          <label class="prod-eod-f"><span>KPI target</span><input type="text" name="kpi_target" placeholder="e.g. 40"></label>
          <label class="prod-eod-f"><span>KPI achieved</span><input type="text" name="kpi_achieved" placeholder="e.g. 43"></label>
        </div>
        <label class="prod-eod-f prod-eod-f--full"><span>Best work today</span><textarea name="best_work" rows="2" placeholder="What went well / what you shipped"></textarea></label>
        <label class="prod-eod-f prod-eod-f--full"><span>Help needed</span><textarea name="help_needed" rows="2" placeholder="Anything blocking you"></textarea></label>
        <label class="prod-eod-f prod-eod-f--full"><span>Focus next</span><textarea name="focus_next" rows="2" placeholder="What you'll tackle next"></textarea></label>
        <label class="prod-eod-f prod-eod-f--full"><span>Pending / waiting on</span><textarea name="pending_waiting_on" rows="2" placeholder="Items waiting on someone else"></textarea></label>
        <div class="prod-eod-form-foot">
          <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-paper-plane"></i> Submit EOD report</button>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <?php if (empty($eod)): ?>
    <div class="card prod-empty"><i class="fa-solid fa-file-pen"></i><h3>No EOD reports yet</h3><p class="muted"><?= $isVtSelf ? 'Submit your first end-of-day report above.' : 'Daily end-of-day reflections from your team will show here.' ?></p></div>
  <?php else: ?>
    <?php if ($isVtSelf): ?><h3 class="prod-eod-hist-h"><i class="fa-regular fa-clock"></i> History <span class="muted small">(<?= count($eod) ?>)</span></h3><?php endif; ?>
    <div class="prod-eod-filter" hidden>
      <span><i class="fa-solid fa-filter"></i> Showing EOD reports for <strong class="prod-eod-filter-name"></strong></span>
      <button type="button" class="prod-eod-clear">Show all <i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="prod-eod-feed<?= $isVtSelf ? ' prod-eod-feed--list' : '' ?>">
      <?php foreach ($eod as $i => $r):
        $nm  = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: (string) ($r['email'] ?? '—');
        $av  = strtoupper(mb_substr($nm, 0, 1));
        $vid = (int) ($r['vt_user_id'] ?? 0);
      ?>
        <article class="prod-eod-card<?= $isVtSelf ? ' prod-eod-card--exp' : '' ?>" data-vt-id="<?= $vid ?>" style="animation-delay:<?= number_format(min($i, 16) * 0.04, 2) ?>s;">
          <header class="prod-eod-head"<?= $isVtSelf ? ' role="button" tabindex="0"' : '' ?>>
            <span class="prod-eod-av"><?= e($av) ?></span>
            <div>
              <div class="prod-eod-name"><?= $isVtSelf ? e($r['report_date'] ?? '') : e($nm) ?></div>
              <div class="muted small"><?php if ($isVtSelf): ?><i class="fa-regular fa-calendar"></i> <?= e(($r['report_date'] ?? '') !== '' ? date('l, M j, Y', strtotime((string) $r['report_date'])) : '') ?><?php else: ?><i class="fa-regular fa-calendar"></i> <?= e($r['report_date'] ?? '') ?><?php endif; ?></div>
            </div>
            <?php if (!empty($r['kpi_name'])): ?>
              <span class="prod-eod-kpi"><i class="fa-solid fa-bullseye"></i> <?= e($r['kpi_name']) ?>: <?= e($r['kpi_achieved'] ?: '—') ?>/<?= e($r['kpi_target'] ?: '—') ?></span>
            <?php endif; ?>
            <?php if ($isVtSelf): ?><i class="fa-solid fa-chevron-down prod-eod-chev"></i><?php endif; ?>
          </header>
          <div class="prod-eod-body">
            <?php if (!empty($r['best_work'])): ?><div class="prod-eod-row"><span class="prod-eod-lbl">Best work</span><p><?= e($r['best_work']) ?></p></div><?php endif; ?>
            <?php if (!empty($r['help_needed'])): ?><div class="prod-eod-row"><span class="prod-eod-lbl">Help needed</span><p><?= e($r['help_needed']) ?></p></div><?php endif; ?>
            <?php if (!empty($r['focus_next'])): ?><div class="prod-eod-row"><span class="prod-eod-lbl">Next focus</span><p><?= e($r['focus_next']) ?></p></div><?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<style>
.prod-stats{margin-bottom:16px;}
.prod-tabs{display:flex;gap:4px;margin-bottom:16px;border-bottom:1px solid var(--line);}
.prod-tab{display:inline-flex;align-items:center;gap:8px;padding:11px 16px;color:var(--text-mute);text-decoration:none;font-size:13.5px;font-weight:700;border-bottom:3px solid transparent;transition:all .15s ease;}
.prod-tab.is-on{color:var(--gold,#d4a64a);border-bottom-color:var(--gold,#d4a64a);}
.prod-tab:hover{color:#fff;}
.prod-tab-c{font-size:11px;font-weight:800;background:rgba(255,255,255,.08);color:#fff;padding:1px 8px;border-radius:30px;}
.prod-tab.is-on .prod-tab-c{background:rgba(247,185,69,.2);color:var(--gold-lt);}

/* Workday: client cards open a modal of VT cards. */
.csm-wd-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
.csm-wd-card{position:relative;overflow:hidden;display:flex;flex-direction:column;gap:7px;text-align:left;cursor:pointer;
  padding:20px;border-radius:16px;background:var(--bg-1);backdrop-filter:var(--glass-blur);-webkit-backdrop-filter:var(--glass-blur);
  border:1px solid var(--line);box-shadow:0 12px 32px -18px rgba(13,8,40,.7);color:inherit;font-family:inherit;
  transition:transform .22s cubic-bezier(.2,.7,.2,1),border-color .22s ease,box-shadow .22s ease;animation:prodIn .55s cubic-bezier(.2,.7,.2,1) both;}
.csm-wd-card::before{content:'';position:absolute;inset:0 0 auto 0;height:3px;background:linear-gradient(90deg,#3919BA,#7c3aed 55%,#F6B845);opacity:0;transition:opacity .22s ease;}
.csm-wd-card:hover{transform:translateY(-5px);border-color:rgba(247,185,69,.45);box-shadow:0 26px 54px -22px rgba(247,185,69,.35);}
.csm-wd-card:hover::before{opacity:1;}
.csm-wd-card__eyebrow{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:var(--gold,#d4a64a);}
.csm-wd-card__eyebrow i{margin-right:5px;}
.csm-wd-card__title{font-size:17px;font-weight:800;color:#fff;line-height:1.25;}
.csm-wd-card__count{font-size:12.5px;color:var(--text-mute);}
.csm-wd-card__cta{margin-top:8px;font-size:12.5px;font-weight:700;color:var(--gold-lt);display:inline-flex;align-items:center;gap:7px;transition:gap .2s ease;}
.csm-wd-card:hover .csm-wd-card__cta{gap:11px;}
@keyframes prodIn{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:none;}}
@media (prefers-reduced-motion:reduce){.csm-wd-card,.prod-eod-card{animation:none;}}

/* Per-client modal */
.csm-wd-modal{position:fixed;inset:0;z-index:1200;display:flex;align-items:center;justify-content:center;padding:24px;}
.csm-wd-modal[hidden]{display:none;}
.csm-wd-modal__overlay{position:absolute;inset:0;background:rgba(8,5,26,.72);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);animation:wdFade .2s ease;}
.csm-wd-modal__dialog{position:relative;width:min(720px,100%);max-height:88vh;overflow:auto;border-radius:20px;padding:26px;
  background:linear-gradient(180deg,#171034,#120c28);border:1px solid var(--line-2);box-shadow:0 40px 90px -30px rgba(0,0,0,.7);animation:wdPop .26s cubic-bezier(.2,.7,.2,1);}
.csm-wd-modal__close{position:absolute;top:16px;right:16px;width:34px;height:34px;border-radius:50%;border:1px solid var(--line-2);
  background:rgba(255,255,255,.06);color:#fff;font-size:15px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s ease;}
.csm-wd-modal__close:hover{background:rgba(225,87,87,.2);border-color:rgba(225,87,87,.5);}
.csm-wd-modal__head{margin-bottom:18px;padding-right:40px;}
.csm-wd-modal__eyebrow{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:var(--gold,#d4a64a);}
.csm-wd-modal__eyebrow i{margin-right:5px;}
.csm-wd-modal__head h3{margin:6px 0 4px;color:#fff;font-size:21px;}
.csm-wd-vt-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:13px;}
.csm-wd-vt-grid--flat{grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;}
.csm-wd-vt{display:flex;flex-direction:column;gap:12px;padding:15px;border-radius:14px;background:rgba(255,255,255,.03);border:1px solid var(--line);}
.csm-wd-vt__top{display:flex;align-items:center;gap:11px;}
.csm-wd-vt__av{width:44px;height:44px;flex:0 0 44px;border-radius:50%;object-fit:cover;background:#1a1535;border:2px solid rgba(247,185,69,.3);}
.csm-wd-vt__av--ph{display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;background:linear-gradient(135deg,#4a4178,#322a5a);}
.csm-wd-vt__meta{flex:1;min-width:0;}
.csm-wd-vt__name{font-size:14.5px;font-weight:800;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.csm-wd-vt__role{font-size:12px;color:var(--gold,#d4a64a);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.csm-wd-vt__btns{display:flex;flex-direction:column;gap:8px;margin-top:auto;}
.csm-wd-vt__btns .btn-portal-primary,.csm-wd-vt__btns .btn-portal-secondary{justify-content:center;}
.prod-nolink{opacity:.5;cursor:not-allowed;text-align:center;}
@keyframes wdFade{from{opacity:0;}to{opacity:1;}}
@keyframes wdPop{from{opacity:0;transform:translateY(16px) scale(.98);}to{opacity:1;transform:none;}}

/* EOD filter banner (set when arriving from a VT's "EOD Reports" button) */
.prod-eod-filter{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;padding:11px 16px;
  border-radius:12px;background:rgba(247,185,69,.1);border:1px solid rgba(247,185,69,.3);font-size:13px;color:#fff;}
.prod-eod-filter[hidden]{display:none;}
.prod-eod-filter i{color:var(--gold);}
.prod-eod-filter strong{color:var(--gold-lt);}
.prod-eod-clear{background:rgba(255,255,255,.08);border:1px solid var(--line-2);color:#fff;font-size:12px;font-weight:700;padding:6px 12px;border-radius:30px;cursor:pointer;transition:all .15s ease;}
.prod-eod-clear:hover{background:rgba(255,255,255,.16);}

.prod-eod-feed{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
.prod-eod-card{padding:18px;border-radius:16px;background:var(--bg-1);backdrop-filter:var(--glass-blur);-webkit-backdrop-filter:var(--glass-blur);
  border:1px solid var(--line);box-shadow:0 12px 32px -18px rgba(13,8,40,.7);animation:prodIn .5s cubic-bezier(.2,.7,.2,1) both;}
.prod-eod-head{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
.prod-eod-av{width:40px;height:40px;flex:0 0 40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;background:linear-gradient(135deg,#4a4178,#322a5a);}
.prod-eod-name{font-size:14.5px;font-weight:800;color:#fff;}
.prod-eod-kpi{margin-left:auto;font-size:11px;font-weight:700;color:var(--gold-lt);background:rgba(247,185,69,.12);border:1px solid rgba(247,185,69,.3);padding:4px 10px;border-radius:30px;white-space:nowrap;}
.prod-eod-body{display:flex;flex-direction:column;gap:10px;}
.prod-eod-row{display:flex;flex-direction:column;gap:3px;}
.prod-eod-lbl{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:var(--gold,#d4a64a);}
.prod-eod-row p{margin:0;font-size:13.5px;line-height:1.55;color:rgba(255,255,255,.85);}

.prod-empty{text-align:center;padding:42px 22px;}
.prod-empty i{font-size:30px;color:var(--gold);margin-bottom:12px;}
.prod-empty h3{margin:0 0 8px;color:#fff;}

/* VT EOD: submission form */
.prod-eod-form-card{margin-bottom:16px;}
.prod-eod-form{margin-top:14px;display:flex;flex-direction:column;gap:12px;}
.prod-eod-form[hidden]{display:none;}
.prod-eod-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
.prod-eod-f{display:flex;flex-direction:column;gap:5px;font-size:12px;font-weight:700;color:var(--gold-lt);}
.prod-eod-f--full{grid-column:1/-1;}
.prod-eod-f input,.prod-eod-f textarea{font-family:inherit;font-size:13.5px;color:#fff;background:rgba(255,255,255,.05);border:1px solid var(--line-2);border-radius:10px;padding:9px 12px;font-weight:500;}
.prod-eod-f input:focus,.prod-eod-f textarea:focus{outline:none;border-color:var(--gold);background:rgba(255,255,255,.08);}
.prod-eod-f textarea{resize:vertical;}
.prod-eod-form-foot{display:flex;justify-content:flex-end;}
.prod-eod-hist-h{margin:6px 0 14px;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:var(--gold,#d4a64a);}

/* VT EOD: expandable history list */
.prod-eod-feed--list{display:flex;flex-direction:column;gap:10px;}
.prod-eod-card--exp{padding:0;overflow:hidden;}
.prod-eod-card--exp .prod-eod-head{display:flex;align-items:center;gap:12px;margin:0;padding:14px 16px;cursor:pointer;user-select:none;}
.prod-eod-card--exp .prod-eod-name{font-size:14px;}
.prod-eod-card--exp .prod-eod-chev{margin-left:auto;color:var(--gold-lt);transition:transform .2s ease;}
.prod-eod-card--exp.is-open .prod-eod-chev{transform:rotate(180deg);}
.prod-eod-card--exp .prod-eod-kpi{margin-left:0;}
.prod-eod-card--exp .prod-eod-body{display:none;padding:0 16px 16px;}
.prod-eod-card--exp.is-open .prod-eod-body{display:flex;}

@media (max-width:1100px){ .csm-wd-grid{grid-template-columns:repeat(2,1fr);} }
@media (max-width:760px){
  .prod-eod-feed{grid-template-columns:1fr;}
  .csm-wd-grid{grid-template-columns:1fr;}
  .csm-wd-vt-grid{grid-template-columns:1fr;}
  .csm-wd-modal{padding:14px;}
  .csm-wd-modal__dialog{padding:20px;}
}
</style>
<script>
(function(){
  var tabs = document.querySelectorAll('.prod-tab');
  var sections = document.querySelectorAll('.prod-section');
  function showTab(which){
    tabs.forEach(function(x){ x.classList.toggle('is-on', x.getAttribute('data-tab') === which); });
    sections.forEach(function(s){ s.style.display = s.getAttribute('data-section') === which ? '' : 'none'; });
    history.replaceState(null, '', '#prod-' + which);
  }
  tabs.forEach(function(t){
    t.addEventListener('click', function(e){ e.preventDefault(); showTab(t.getAttribute('data-tab')); });
  });
  if (location.hash === '#prod-eod') showTab('eod');

  /* Client card → per-client modal of VT cards */
  var openModal = null;
  function closeModal(){ if (openModal){ openModal.setAttribute('hidden',''); openModal = null; document.body.style.overflow = ''; } }
  function openModalById(id){ var m = document.getElementById(id); if (!m) return; closeModal(); m.removeAttribute('hidden'); openModal = m; document.body.style.overflow = 'hidden'; }
  document.querySelectorAll('[data-modal-target]').forEach(function(btn){
    btn.addEventListener('click', function(){ openModalById(btn.getAttribute('data-modal-target')); });
  });
  document.querySelectorAll('[data-modal-close]').forEach(function(el){ el.addEventListener('click', closeModal); });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });

  /* A VT's "EOD Reports" button jumps to the EOD tab, filtered to that teammate */
  var feed   = document.querySelector('.prod-eod-feed');
  var banner = document.querySelector('.prod-eod-filter');
  var bName  = document.querySelector('.prod-eod-filter-name');
  var noneMsg = null;
  function ensureNone(){
    if (noneMsg || !feed) return noneMsg;
    noneMsg = document.createElement('div');
    noneMsg.className = 'card prod-empty prod-eod-none';
    noneMsg.style.gridColumn = '1 / -1';
    noneMsg.innerHTML = '<i class="fa-solid fa-file-circle-xmark"></i><h3>No EOD reports yet</h3><p class="muted">This teammate hasn’t submitted any end-of-day reports.</p>';
    feed.appendChild(noneMsg);
    return noneMsg;
  }
  function filterEod(id, name){
    if (!feed) return;
    var shown = 0;
    feed.querySelectorAll('.prod-eod-card').forEach(function(c){
      var match = String(c.getAttribute('data-vt-id')) === String(id);
      c.style.display = match ? '' : 'none';
      if (match) shown++;
    });
    if (banner){ banner.removeAttribute('hidden'); if (bName) bName.textContent = name || 'this teammate'; }
    var nm = ensureNone(); if (nm) nm.style.display = shown === 0 ? '' : 'none';
  }
  function clearEod(){
    if (feed) feed.querySelectorAll('.prod-eod-card').forEach(function(c){ c.style.display = ''; });
    if (banner) banner.setAttribute('hidden','');
    if (noneMsg) noneMsg.style.display = 'none';
  }
  document.querySelectorAll('[data-vt-eod]').forEach(function(btn){
    btn.addEventListener('click', function(){
      closeModal();
      showTab('eod');
      filterEod(btn.getAttribute('data-vt-eod'), btn.getAttribute('data-vt-name'));
      var sec = document.getElementById('prod-eod');
      if (sec && sec.scrollIntoView) sec.scrollIntoView({behavior:'smooth', block:'start'});
    });
  });
  var clearBtn = document.querySelector('.prod-eod-clear');
  if (clearBtn) clearBtn.addEventListener('click', clearEod);

  /* VT: toggle the EOD submission form */
  var eodToggle = document.getElementById('prodEodToggle');
  var eodForm   = document.getElementById('prodEodForm');
  if (eodToggle && eodForm) {
    eodToggle.addEventListener('click', function(){
      var open = eodForm.hasAttribute('hidden');
      if (open) { eodForm.removeAttribute('hidden'); eodToggle.innerHTML = '<i class="fa-solid fa-minus"></i> Close'; }
      else { eodForm.setAttribute('hidden',''); eodToggle.innerHTML = '<i class="fa-solid fa-plus"></i> New report'; }
    });
  }

  /* VT: expandable EOD history cards */
  document.querySelectorAll('.prod-eod-card--exp .prod-eod-head').forEach(function(h){
    function toggle(){ h.parentNode.classList.toggle('is-open'); }
    h.addEventListener('click', toggle);
    h.addEventListener('keydown', function(e){ if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); } });
  });
})();
</script>

<?php include __DIR__ . '/_vt_profile_modal.php'; ?>
