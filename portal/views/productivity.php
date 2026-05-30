<?php
/** @var array $user @var array $vts @var array $eod */
$pageTitle = 'Productivity Reports';
$subtitle  = 'Workday tracking + End-of-Day reports in one place. Workday entries open in WorkdayTracker.com.';

$nameOrEmail = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($u['email'] ?? '—');
};
$initial = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    if ($n !== '') { return strtoupper(mb_substr($n, 0, 1)); }
    return strtoupper(mb_substr((string) ($u['email'] ?? '?'), 0, 1));
};

/** Resolve the workday URL for a VT row: prefer the client-engagement link, fall back to profile-level, finally build one from a tracker id. */
$resolveWorkday = static function (array $v): string {
    $cvLink = (string) ($v['cv_workday_link'] ?? '');
    if ($cvLink !== '' && preg_match('#^https?://#i', $cvLink)) { return $cvLink; }
    $pLink = (string) ($v['profile_workday_link'] ?? ($v['workday_link'] ?? ''));
    if ($pLink !== '' && preg_match('#^https?://#i', $pLink)) { return $pLink; }
    $tid = (string) ($v['cv_workday_tracker_id'] ?? ($v['profile_tracker_id'] ?? ($v['workday_tracker_id'] ?? '')));
    if ($tid !== '') { return 'https://workdaytracker.com/app/public-report/' . rawurlencode($tid) . '/'; }
    return '';
};
?>
<div class="prod-tabs" role="tablist">
  <a href="#prod-workday" class="prod-tab is-on" data-tab="workday"><i class="fa-solid fa-clock"></i> Workday Tracker <span class="muted small">(<?= count($vts) ?>)</span></a>
  <a href="#prod-eod"     class="prod-tab"       data-tab="eod"><i class="fa-solid fa-file-pen"></i> EOD Reports <span class="muted small">(<?= count($eod) ?>)</span></a>
</div>

<!-- WORKDAY -->
<section class="card prod-section" id="prod-workday" data-section="workday">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-clock"></i> Workday Tracker</h3>
    <span class="muted small">Each VT's daily tracker lives on <code>workdaytracker.com</code> — click to open in a new tab.</span>
  </div>
  <?php if (empty($vts)): ?>
    <p class="muted">No Virtual Teammates to track yet.</p>
  <?php else: ?>
    <div class="prod-grid">
      <?php foreach ($vts as $v):
        $vid  = (int) ($v['user_id'] ?? 0);
        $href = $resolveWorkday($v);
      ?>
        <div class="prod-card">
          <?php /* Initials only — photo_url is a PHP-served endpoint (p=avatar|media),
                   so one <img> per VT fired a full portal request and slowed the page. */ ?>
          <div class="prod-photo placeholder"><?= e($initial($v)) ?></div>
          <div class="prod-meta">
            <div class="prod-name"><?= e($nameOrEmail($v)) ?></div>
            <div class="prod-role"><?= e($v['role_title'] ?? ($v['department'] ?? 'Virtual Teammate')) ?></div>
            <?php if (!empty($v['company_name'])): ?>
              <div class="muted small"><i class="fa-solid fa-building"></i> <?= e($v['company_name']) ?></div>
            <?php endif; ?>
          </div>
          <div class="prod-actions">
            <?php if ($href !== ''): ?>
              <a class="btn-portal-primary btn-sm" href="<?= e($href) ?>" target="_blank" rel="noopener">
                <i class="fa-solid fa-up-right-from-square"></i> Open Tracker
              </a>
            <?php else: ?>
              <span class="btn-portal-secondary btn-sm" style="opacity:.55;cursor:not-allowed;" title="No workday link set"><i class="fa-solid fa-link-slash"></i> No tracker link</span>
            <?php endif; ?>
            <?php if ($vid > 0): ?>
              <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('vts.view', ['id' => $vid])) ?>"><i class="fa-solid fa-eye"></i> Profile</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- EOD -->
<section class="card prod-section" id="prod-eod" data-section="eod" style="margin-top:18px;display:none;">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-file-pen"></i> End-of-Day Reports</h3>
    <span class="muted small">Daily reflections from your team. Filed by VTs.</span>
  </div>
  <?php if (empty($eod)): ?>
    <p class="muted">No EOD reports yet.</p>
  <?php else: ?>
    <ul class="cd-eod-list">
      <?php foreach ($eod as $r): ?>
        <li>
          <div class="cd-eod-head">
            <strong><?= e(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?: e($r['email'] ?? '—') ?></strong>
            <span class="muted small">&middot; <?= e($r['report_date']) ?></span>
          </div>
          <?php if (!empty($r['best_work'])): ?>
            <div class="cd-eod-body"><span class="cd-eod-lbl">Best work:</span> <?= e($r['best_work']) ?></div>
          <?php endif; ?>
          <?php if (!empty($r['help_needed'])): ?>
            <div class="cd-eod-body"><span class="cd-eod-lbl">Help needed:</span> <?= e($r['help_needed']) ?></div>
          <?php endif; ?>
          <?php if (!empty($r['focus_next'])): ?>
            <div class="cd-eod-body"><span class="cd-eod-lbl">Next focus:</span> <?= e($r['focus_next']) ?></div>
          <?php endif; ?>
          <?php if (!empty($r['kpi_name'])): ?>
            <div class="cd-eod-body muted small"><i class="fa-solid fa-bullseye"></i> <?= e($r['kpi_name']) ?> &mdash; <?= e($r['kpi_achieved'] ?: '—') ?> / <?= e($r['kpi_target'] ?: '—') ?></div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<style>
.prod-tabs{display:flex;gap:4px;margin-bottom:14px;border-bottom:1px solid rgba(255,255,255,.08);}
.prod-tab{padding:10px 16px;color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px;font-weight:600;border-bottom:3px solid transparent;transition:all .15s ease;}
.prod-tab.is-on{color:var(--gold,#d4a64a);border-bottom-color:var(--gold,#d4a64a);}
.prod-tab:hover{color:#fff;}
.prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;margin-top:14px;}
.prod-card{display:flex;gap:14px;align-items:center;padding:14px;border:1px solid rgba(255,255,255,.08);border-radius:14px;background:rgba(255,255,255,.03);}
.prod-photo{width:52px;height:52px;border-radius:50%;object-fit:cover;flex:0 0 52px;background:#1a1535;}
.prod-photo.placeholder{display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;background:linear-gradient(135deg,#4a4178,#322a5a);}
.prod-meta{flex:1;min-width:0;}
.prod-name{font-size:14.5px;font-weight:700;color:#fff;}
.prod-role{font-size:12px;color:var(--gold,#d4a64a);margin:2px 0;}
.prod-actions{display:flex;flex-direction:column;gap:6px;align-items:flex-end;flex:0 0 auto;}
@media (max-width:520px){.prod-card{flex-wrap:wrap;}.prod-actions{flex-direction:row;width:100%;justify-content:flex-start;}}
</style>
<script>
(function(){
  var tabs = document.querySelectorAll('.prod-tab');
  var sections = document.querySelectorAll('.prod-section');
  tabs.forEach(function(t){
    t.addEventListener('click', function(e){
      e.preventDefault();
      var which = t.getAttribute('data-tab');
      tabs.forEach(function(x){ x.classList.toggle('is-on', x === t); });
      sections.forEach(function(s){ s.style.display = s.getAttribute('data-section') === which ? '' : 'none'; });
      history.replaceState(null, '', '#prod-' + which);
    });
  });
  // Honor URL hash on load.
  if (location.hash === '#prod-eod') {
    document.querySelector('.prod-tab[data-tab="eod"]').click();
  }
})();
</script>
