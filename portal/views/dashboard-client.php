<?php
/** @var array $user @var array $data */
$pageTitle = 'Client Dashboard';
$subtitle  = 'Your team at a glance — notifications, today\'s meetings, fresh messages.';
$client    = $data['client'] ?? null;

$nameOrEmail = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($u['email'] ?? '—');
};
$initial = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    if ($n !== '') { return strtoupper(mb_substr($n, 0, 1)); }
    return strtoupper(mb_substr((string) ($u['email'] ?? '?'), 0, 1));
};
$fmtHours = static function (int $minutes): string {
    if ($minutes <= 0) { return '0h'; }
    $h = intdiv($minutes, 60); $m = $minutes % 60;
    return $m === 0 ? "{$h}h" : "{$h}h {$m}m";
};
$relTime = static function (string $iso): string {
    $ts = strtotime($iso); if (!$ts) { return $iso; }
    $diff = time() - $ts;
    if ($diff < 60)     { return 'just now'; }
    if ($diff < 3600)   { return floor($diff / 60) . 'm ago'; }
    if ($diff < 86400)  { return floor($diff / 3600) . 'h ago'; }
    if ($diff < 604800) { return floor($diff / 86400) . 'd ago'; }
    return date('M j', $ts);
};

$activeTasks   = $data['tasks_active'] ?? [];
$weekByVt      = $data['workday_week'] ?? [];
$notes         = $data['notifications'] ?? [];
$todayMeetings = $data['meetings_today'] ?? [];
$recentMsgs    = $data['recent_messages'] ?? [];
$unreadNoti    = 0; foreach ($notes as $n) { if (empty($n['read_at'])) { $unreadNoti++; } }
$unreadMsg     = 0; foreach ($recentMsgs as $m) { if (empty($m['read_at'])) { $unreadMsg++; } }
$weekTotalMin  = 0; foreach ($weekByVt as $w) { $weekTotalMin += (int) $w['minutes']; }
?>
<?php if (!$client): ?>
  <div class="card">
    <h3>No client record linked yet</h3>
    <p>Your user account isn't linked to a client (company) record. Ask your super admin to create one and link it to your user.</p>
  </div>
<?php else:
  // ROI calc data — mirrors the staging mu-plugin's roi-savings-calculator
  // logic (roi-savings-calculator-shortcode.php). Whole-month diff using
  // year/month math (not 86400/30.4), capped at 240 months (20yr), and the
  // critical rule from staging line 1497: an ACTIVE engagement that hasn't
  // yet hit one month is bumped to 1 so the lifetime value isn't $0 the
  // day a VT is hired. End-of-service engagements keep their real month
  // count even when that's 0.
  $MONTHLY_FT     = 5250;
  $MAX_MONTHS_CAP = 240;
  $hiredHistory = $data['hired_history'] ?? [];

  $monthsBetween = static function (?string $start, ?string $end) use ($MAX_MONTHS_CAP): int {
      if (!$start) return 0;
      try {
          $a = new DateTimeImmutable(substr($start, 0, 10));
          $b = $end ? new DateTimeImmutable(substr($end, 0, 10)) : new DateTimeImmutable('today');
          if ($b < $a) return 0;
          $months = ((int) $b->format('Y') - (int) $a->format('Y')) * 12
                  + ((int) $b->format('n') - (int) $a->format('n'));
          if ((int) $b->format('j') < (int) $a->format('j')) { $months -= 1; }
          $months = max(0, $months);
          return min($months, $MAX_MONTHS_CAP);
      } catch (Throwable $_) { return 0; }
  };

  $hiredWithMonths = [];
  foreach ($hiredHistory as $h) {
      $start  = $h['started_at'] ?? null;
      $end    = !empty($h['ended_at']) ? $h['ended_at'] : null;
      $isEOS  = !empty($end);
      $months = $monthsBetween($start, $end);
      // Active VT with under-a-month tenure → credit 1 month so the gauge
      // reflects current relationship value. Ended engagements stay accurate.
      if (!$isEOS && $months === 0) { $months = 1; }
      $hiredWithMonths[] = [
          'name'    => trim(($h['first_name'] ?? '') . ' ' . ($h['last_name'] ?? '')) ?: ($h['email'] ?? '—'),
          'role'    => trim(($h['role_title'] ?? '') ?: ($h['department'] ?? '')),
          'months'  => $months,
          'active'  => !$isEOS,
          'started' => $start,
      ];
  }
  $activeHired = 0; foreach ($hiredWithMonths as $h) { if ($h['active']) { $activeHired++; } }
?>

<!-- HERO: cover photo + profile photo overlap -->
<?php
  $userPhoto = media_src($user['photo_url'] ?? '');
  $userCover = $user['cover_url'] ?? '';
?>
<div class="card cd-cover-card" style="padding:0;overflow:hidden;">
  <?php $coverBg = $userCover !== '' ? $userCover : 'assets/default-banner.webp'; ?>
  <div class="cd-cover" style="background-image:url('<?= e($coverBg) ?>');"></div>
  <div class="cd-cover-body">
    <div class="cd-cover-photo-wrap">
      <?php if ($userPhoto): ?>
        <img class="cd-cover-photo" src="<?= e($userPhoto) ?>" alt="" loading="lazy"
             onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
      <?php else: ?>
        <div class="cd-cover-photo placeholder"><?= e(strtoupper(mb_substr($user['first_name'] ?: $user['email'], 0, 1))) ?></div>
      <?php endif; ?>
    </div>
    <div class="cd-cover-meta">
      <div class="cd-hero-eyebrow"><i class="fa-solid fa-building"></i> Client &middot; <?= e($client['contract_status']) ?></div>
      <h2 class="cd-hero-h" style="margin:0 0 4px;"><?= e($client['company_name']) ?></h2>
      <div class="cd-hero-sub muted">
        <i class="fa-solid fa-user"></i> <?= e(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?: e($user['email']) ?>
        <?php if (!empty($client['company_email'])): ?> &middot; <i class="fa-solid fa-envelope"></i> <?= e($client['company_email']) ?><?php endif; ?>
        &middot; <i class="fa-solid fa-user-tie"></i> <?= count($data['csms']) ?> CSM
        &middot; <i class="fa-solid fa-user-doctor"></i> <?= count($data['vts']) ?> VT<?= count($data['vts']) === 1 ? '' : 's' ?>
      </div>
    </div>
    <div class="cd-cover-actions">
      <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('tasks.edit')) ?>"><i class="fa-solid fa-plus"></i> Assignment</a>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('meetings.edit')) ?>"><i class="fa-solid fa-calendar-plus"></i> Meeting</a>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('profile')) ?>"><i class="fa-solid fa-camera"></i> Edit photos</a>
    </div>
  </div>
</div>

<!-- ROI CALCULATOR (embedded — same look + features as staging) -->
<div class="roiX" id="roiX_main" data-monthly-ft="<?= (int) $MONTHLY_FT ?>" data-active-count="<?= (int) $activeHired ?>" style="margin:18px 0;">
  <div class="roiX__header">
    <div class="roiX__title-block">
      <div class="roiX__title"><i class="fa-solid fa-calculator" style="color:var(--gold,#d4a64a);margin-right:6px;"></i> Value Creation Calculator</div>
      <div class="roiX__sub"><strong>Actual</strong> uses your hired VTs and contract dates to compute lifetime value. <strong>Scenario</strong> models bi-weekly cost (US vs VT) for planning the next hire.</div>
    </div>
    <div class="roiX__client"><i class="fa-solid fa-building"></i> <?= e($client['company_name']) ?></div>
  </div>

  <div class="roiX__shell">

    <!-- LEFT: Actual -->
    <section class="roiX__panel roiX__panel--actual">
      <div class="roiX__badge">Actual VTs Hired</div>
      <div class="roiX__panelTitle">Value Creation <em>(Actual)</em></div>
      <div class="roiX__panelSub">Lifetime value created by your hired team.</div>

      <div class="roiX__pillRow">
        <span class="roiX__pill"><i class="fa-solid fa-user-doctor"></i> <?= (int) $activeHired ?> active &middot; <?= count($hiredWithMonths) ?> total</span>
      </div>

      <div class="roiX__gaugeWrap">
        <div class="roiX__gauge">
          <svg viewBox="0 0 200 200" role="img" aria-label="Lifetime value gauge">
            <defs>
              <linearGradient id="roiGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#b0791b"/>
                <stop offset="55%" stop-color="#f6b845"/>
                <stop offset="100%" stop-color="#ffe9b0"/>
              </linearGradient>
            </defs>
            <circle cx="100" cy="100" r="72" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="16"/>
            <circle id="roiGaugeProg" cx="100" cy="100" r="72" fill="none" stroke="url(#roiGrad)" stroke-width="16" stroke-linecap="round" stroke-dasharray="0 452.39" transform="rotate(-90 100 100)"/>
          </svg>
          <div class="roiX__gCenter">
            <div class="roiX__gVal" data-el="lifetimeVal">$0</div>
            <div class="roiX__gLbl">Lifetime value</div>
          </div>
        </div>
        <div class="roiX__gMeta">
          <div class="roiX__gMetaTitle">Lifetime value created</div>
          <div class="roiX__gMetaSub">Progress to next milestone: <strong data-el="milestone">$10,000</strong></div>
        </div>
      </div>

      <details class="roiX__details">
        <summary><i class="fa-solid fa-list"></i> Teammate breakdown</summary>
        <div class="roiX__detailsBody">
          <?php if (empty($hiredWithMonths)): ?>
            <p class="muted small">No VT engagements yet.</p>
          <?php else: ?>
            <table class="roiX__tbl">
              <thead><tr><th>VT</th><th>Role</th><th>Started</th><th>Tenure</th><th>Value</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach ($hiredWithMonths as $h):
                  $val = $h['months'] * $MONTHLY_FT;
                ?>
                  <tr>
                    <td><strong><?= e($h['name']) ?></strong></td>
                    <td class="muted small"><?= e($h['role'] ?: '—') ?></td>
                    <td class="muted small"><?= e($h['started'] ? substr($h['started'], 0, 10) : '—') ?></td>
                    <td><?= (int) $h['months'] ?> mo</td>
                    <td>$<?= number_format($val) ?></td>
                    <td><?php if ($h['active']): ?><span class="pill pill-active">Active</span><?php else: ?><span class="pill pill-paused">Ended</span><?php endif; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </details>
    </section>

    <!-- RIGHT: Scenario -->
    <section class="roiX__panel roiX__panel--scenario">
      <div class="roiX__badge roiX__badge--gold">Scenario Planner</div>
      <div class="roiX__panelTitle">Value Creation <em>(Scenario)</em></div>
      <div class="roiX__panelSub">Bi-weekly cost comparison — pick a tier, schedule, and headcount.</div>

      <div class="roiX__field">
        <label class="roiX__label">Job category</label>
        <select class="roiX__select" data-el="jobSelect">
          <optgroup label="Admin & Support">
            <option value="administrative_assistant" selected>Administrative Assistant</option>
            <option value="executive_assistant">Executive Assistant</option>
            <option value="client_services_rep">Client Services Rep</option>
            <option value="receptionist">Receptionist</option>
          </optgroup>
          <optgroup label="Finance">
            <option value="accountant">Accountant</option>
            <option value="billing_coordinator">Billing Coordinator</option>
            <option value="bookkeeper">Bookkeeper</option>
          </optgroup>
          <optgroup label="Healthcare">
            <option value="medical_receptionist">Medical Receptionist</option>
            <option value="medical_admin">Medical Admin</option>
            <option value="medical_biller">Medical Biller</option>
            <option value="medical_scribe">Medical Scribe</option>
            <option value="healthcare_referral_coordinator">Healthcare Referral Coordinator</option>
          </optgroup>
          <optgroup label="Dental">
            <option value="dental_admin">Dental Admin</option>
            <option value="dental_biller">Dental Biller</option>
            <option value="dental_scribe">Dental Scribe</option>
            <option value="dental_insurance_coordinator">Dental Insurance Coordinator</option>
          </optgroup>
          <optgroup label="Marketing &amp; Sales">
            <option value="marketing_coordinator">Marketing Coordinator</option>
            <option value="marketing_manager">Marketing Manager</option>
            <option value="copywriter">Copywriter</option>
            <option value="sales_rep">Sales Rep</option>
            <option value="account_manager">Account Manager</option>
          </optgroup>
          <optgroup label="Data &amp; Analytics">
            <option value="data_analyst">Data Analyst</option>
            <option value="bi_developer">BI Developer</option>
            <option value="quality_assurance_analyst">Quality Assurance Analyst</option>
          </optgroup>
        </select>
      </div>

      <div class="roiX__grid2">
        <div class="roiX__field">
          <label class="roiX__label">Tier</label>
          <select class="roiX__select" data-el="tierSelect">
            <option value="pro" selected>Pro</option>
            <option value="specialist">Specialist</option>
          </select>
        </div>
        <div class="roiX__field">
          <label class="roiX__label">Schedule</label>
          <select class="roiX__select" data-el="schedSelect">
            <option value="ft" selected>Full-time (FT)</option>
            <option value="pt">Part-time (PT)</option>
          </select>
        </div>
      </div>

      <div class="roiX__field">
        <label class="roiX__label">Number of Virtual Teammates</label>
        <div class="roiX__slider">
          <input data-el="vtCount" type="range" min="0" max="20" value="<?= max(0, (int) $activeHired) ?>" step="1">
          <div class="roiX__sliderMeta">
            <span>0</span><span>5</span><span>10</span><span>15</span><span>20</span>
            <span class="roiX__bubble"><span data-el="vtCountVal"><?= (int) $activeHired ?> VTs</span></span>
          </div>
        </div>
      </div>

      <div class="roiX__results">
        <div class="roiX__kpi">
          <div class="roiX__kpiLabel">Estimated Bi-weekly</div>
          <div class="roiX__kpiValue" data-el="biSavings">$0</div>
        </div>
        <div class="roiX__kpi">
          <div class="roiX__kpiLabel">Estimated Annual</div>
          <div class="roiX__kpiValue" data-el="annualSavings">$0</div>
        </div>
      </div>

      <details class="roiX__details">
        <summary><i class="fa-solid fa-receipt"></i> Cost breakdown</summary>
        <div class="roiX__detailsBody">
          <div class="roiX__break">
            <div class="roiX__breakRow"><span>Est. US bi-weekly cost</span><span data-el="usBi">$0</span></div>
            <div class="roiX__breakRow"><span>Est. VT bi-weekly cost</span><span data-el="vtBi">$0</span></div>
            <div class="roiX__breakRow"><span>Saved per pay period</span><strong data-el="savedBi">$0</strong></div>
          </div>
        </div>
      </details>
    </section>
  </div>
</div>
<script>
(function(){
  var BIWEEK = { pro: { vt: { ft: 750, pt: 400 }, us: { ft: 1800, pt: 960 } }, specialist: { vt: { ft: 1000, pt: 600 }, us: { ft: 2475, pt: 1320 } } };
  var JOB_DEFAULT_TIER = { accountant:'specialist', medical_biller:'specialist', medical_scribe:'specialist', dental_biller:'specialist', dental_scribe:'specialist', marketing_manager:'specialist', account_manager:'specialist', data_analyst:'specialist', bi_developer:'specialist' };
  var wrap = document.getElementById('roiX_main'); if (!wrap) return;
  var $ = function(s){ return wrap.querySelector(s); };
  var fmtUSD = function(n){ return '$' + Math.round(n).toLocaleString(); };
  var pickMilestone = function(v){ var m=[10000,25000,50000,100000,250000,500000,1000000,2000000,5000000,10000000]; for(var i=0;i<m.length;i++){ if(v<=m[i]) return m[i]; } return m[m.length-1]; };

  // ── Count-up animation. Tweens an integer dollar value from -> to over
  // `dur` ms with ease-out-cubic so big jumps feel smooth instead of jarring.
  // Each element gets its own cancellation token via _raf so rapid recalcs
  // (slider drag) don't stack overlapping animations.
  function animateNumber(el, to, dur){
    if (!el) return;
    if (el._raf) cancelAnimationFrame(el._raf);
    var from = parseFloat((el.textContent || '0').replace(/[^\d.-]/g,'')) || 0;
    to = Math.round(to); dur = dur || 600;
    if (from === to) { el.textContent = fmtUSD(to); return; }
    var start = performance.now();
    function step(now){
      var t = Math.min(1, (now - start) / dur);
      var e = 1 - Math.pow(1 - t, 3); // ease-out cubic
      var v = from + (to - from) * e;
      el.textContent = fmtUSD(v);
      if (t < 1) { el._raf = requestAnimationFrame(step); }
      else { el.textContent = fmtUSD(to); el._raf = null; }
    }
    el._raf = requestAnimationFrame(step);
  }

  // ── Lifetime gauge — animate stroke fill + value
  var monthly = parseInt(wrap.getAttribute('data-monthly-ft'),10) || 5250;
  var hired = <?= json_encode($hiredWithMonths) ?>;
  var lifetime = 0; for (var i=0;i<hired.length;i++){ lifetime += (parseFloat(hired[i].months)||0) * monthly; }
  var ms = pickMilestone(lifetime);
  $('[data-el="milestone"]').textContent = fmtUSD(ms);
  var pct = ms > 0 ? Math.max(0, Math.min(1, lifetime/ms)) : 0;
  var c = 2 * Math.PI * 72, dash = pct * c;
  var prog = document.getElementById('roiGaugeProg');

  // Trigger gauge + count-up when the calculator scrolls into view, so the
  // animation actually plays for the user instead of finishing before they
  // see it.
  function runActualAnimation(){
    animateNumber($('[data-el="lifetimeVal"]'), lifetime, 1200);
    if (prog) {
      prog.style.transition = 'stroke-dasharray 1.2s cubic-bezier(.16,1,.3,1)';
      // Start from empty so the fill visibly sweeps to its target.
      prog.setAttribute('stroke-dasharray', '0 ' + c);
      requestAnimationFrame(function(){
        requestAnimationFrame(function(){
          prog.setAttribute('stroke-dasharray', dash + ' ' + (c - dash));
        });
      });
    }
  }
  if ('IntersectionObserver' in window){
    var seen = false;
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(en){
        if (en.isIntersecting && !seen){ seen = true; runActualAnimation(); io.disconnect(); }
      });
    }, { threshold: 0.2 });
    io.observe(wrap);
  } else {
    setTimeout(runActualAnimation, 100);
  }

  // ── Scenario panel
  var jobSel = $('[data-el="jobSelect"]'), tierSel = $('[data-el="tierSelect"]'), schedSel = $('[data-el="schedSelect"]');
  var cntRng = $('[data-el="vtCount"]'), cntVal = $('[data-el="vtCountVal"]');
  var bubble = wrap.querySelector('.roiX__bubble');
  function recalc(animate){
    var t = BIWEEK[tierSel.value] || BIWEEK.pro;
    var sched = schedSel.value || 'ft';
    var n = parseInt(cntRng.value,10) || 0;
    var vtBi = (t.vt[sched]||0)*n, usBi = (t.us[sched]||0)*n, save = usBi - vtBi;
    var dur = animate ? 450 : 0;
    animateNumber($('[data-el="biSavings"]'),     save,      dur);
    animateNumber($('[data-el="annualSavings"]'), save * 26, dur);
    animateNumber($('[data-el="usBi"]'),          usBi,      dur);
    animateNumber($('[data-el="vtBi"]'),          vtBi,      dur);
    animateNumber($('[data-el="savedBi"]'),       save,      dur);
    cntVal.textContent = n + ' VT' + (n === 1 ? '' : 's');
    var min = parseInt(cntRng.min,10)||0, max = parseInt(cntRng.max,10)||20;
    var p = max > min ? (n - min) / (max - min) : 0;
    // Update the CSS variable so the gradient fill visually tracks the
    // thumb. Without this the track stays frozen at the hardcoded 50%.
    cntRng.style.setProperty('--p', (p * 100) + '%');
    if (bubble){
      bubble.style.left = (p * 100) + '%';
      bubble.style.right = 'auto';
      bubble.style.transform = 'translateX(-50%)';
    }
  }
  jobSel.addEventListener('change',  function(){ tierSel.value = JOB_DEFAULT_TIER[jobSel.value] || 'pro'; recalc(true); });
  tierSel.addEventListener('change', function(){ recalc(true); });
  schedSel.addEventListener('change',function(){ recalc(true); });
  // Slider scrub: snappier 0-duration so the numbers track the drag tightly;
  // a longer 'change' (mouse-up) animation then settles the final number.
  cntRng.addEventListener('input',   function(){ recalc(false); });
  cntRng.addEventListener('change',  function(){ recalc(true); });
  recalc(false);
})();
</script>

<!-- STATS STRIP -->
<div class="cd-stats">
  <div class="cd-stat">
    <div class="cd-stat-ico"><i class="fa-solid fa-user-doctor"></i></div>
    <div><div class="cd-stat-num"><?= count($data['vts']) ?></div><div class="cd-stat-lbl">VTs assigned</div></div>
  </div>
  <div class="cd-stat">
    <div class="cd-stat-ico" style="color:#f7b945;"><i class="fa-solid fa-list-check"></i></div>
    <div><div class="cd-stat-num"><?= count($activeTasks) ?></div><div class="cd-stat-lbl">Active assignments</div></div>
  </div>
  <div class="cd-stat">
    <div class="cd-stat-ico" style="color:#7ec27e;"><i class="fa-solid fa-clock"></i></div>
    <div><div class="cd-stat-num"><?= e($fmtHours($weekTotalMin)) ?></div><div class="cd-stat-lbl">Team hours this week</div></div>
  </div>
  <div class="cd-stat">
    <div class="cd-stat-ico" style="color:#bba7fa;"><i class="fa-solid fa-calendar-day"></i></div>
    <div><div class="cd-stat-num"><?= count($todayMeetings) ?></div><div class="cd-stat-lbl">Meetings today</div></div>
  </div>
</div>

<!-- THREE SMALL WIDGETS: notifications · today's meetings · messages -->
<div class="cd-trio">
  <!-- Notifications -->
  <div class="card cd-trio-card">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-bell"></i> Notifications <?php if ($unreadNoti > 0): ?><span class="cd-badge"><?= (int) $unreadNoti ?></span><?php endif; ?></h3>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('notifications')) ?>">All &rarr;</a>
    </div>
    <?php if (empty($notes)): ?>
      <p class="muted small" style="padding:18px 4px;text-align:center;">No notifications yet.</p>
    <?php else: ?>
      <ul class="cd-mini-list">
        <?php foreach ($notes as $n): ?>
          <li class="cd-mini-item <?= empty($n['read_at']) ? 'unread' : '' ?>">
            <div class="cd-mini-ico cd-noti-<?= e($n['kind']) ?>"><i class="fa-solid fa-<?= e($n['kind'] === 'task' ? 'list-check' : ($n['kind'] === 'message' ? 'envelope' : 'info-circle')) ?>"></i></div>
            <div class="cd-mini-body">
              <div class="cd-mini-title"><?php if (!empty($n['link'])): ?><a href="<?= e($n['link']) ?>"><?= e($n['title']) ?></a><?php else: ?><?= e($n['title']) ?><?php endif; ?></div>
              <div class="muted small"><?= e($relTime($n['created_at'])) ?></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <!-- Today's meetings -->
  <div class="card cd-trio-card">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-calendar-day"></i> Today's meetings <span class="muted small">(<?= count($todayMeetings) ?>)</span></h3>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('meetings')) ?>">All &rarr;</a>
    </div>
    <?php if (empty($todayMeetings)): ?>
      <p class="muted small" style="padding:18px 4px;text-align:center;">Nothing scheduled today.</p>
      <div style="text-align:center;"><a class="btn-portal-primary btn-sm" href="<?= e(portal_url('meetings.edit')) ?>"><i class="fa-solid fa-plus"></i> Schedule one</a></div>
    <?php else: ?>
      <ul class="cd-mini-list">
        <?php foreach ($todayMeetings as $m): ?>
          <li class="cd-mini-item">
            <div class="cd-mini-ico cd-noti-task"><i class="fa-solid fa-video"></i></div>
            <div class="cd-mini-body">
              <div class="cd-mini-title"><?= e($m['topic'] ?: 'Meeting') ?></div>
              <div class="muted small"><i class="fa-solid fa-clock"></i> <?= e(date('g:i a', strtotime($m['scheduled_at']))) ?> &middot; with <?= e($m['attendee_name'] ?: ucfirst($m['meeting_with_role'])) ?></div>
            </div>
            <span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <!-- Recent messages -->
  <div class="card cd-trio-card">
    <div class="card-h">
      <h3 style="margin:0;"><i class="fa-solid fa-comments"></i> Recent messages <?php if ($unreadMsg > 0): ?><span class="cd-badge"><?= (int) $unreadMsg ?></span><?php endif; ?></h3>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('messages')) ?>">Open &rarr;</a>
    </div>
    <?php if (empty($recentMsgs)): ?>
      <p class="muted small" style="padding:18px 4px;text-align:center;">No messages yet.</p>
    <?php else: ?>
      <ul class="cd-mini-list">
        <?php foreach ($recentMsgs as $m):
          $sName  = trim(($m['s_fn'] ?? '') . ' ' . ($m['s_ln'] ?? '')) ?: ($m['s_email'] ?? '—');
          $sPhoto = media_thumb_src($m['s_photo'] ?? '') ?: media_src($m['s_photo'] ?? '');
        ?>
          <li class="cd-mini-item <?= empty($m['read_at']) ? 'unread' : '' ?>">
            <?php if ($sPhoto !== ''): ?>
              <img class="cd-mini-photo" src="<?= e($sPhoto) ?>" alt="" loading="lazy"
                   onerror="this.onerror=null;this.src='assets/placeholder-avatar.svg';">
            <?php else: ?>
              <div class="cd-mini-photo placeholder"><?= e(strtoupper(mb_substr($sName, 0, 1))) ?></div>
            <?php endif; ?>
            <div class="cd-mini-body">
              <div class="cd-mini-title"><a href="<?= e(portal_url('messages', ['with' => (int) $m['sender_user_id']])) ?>"><?= e($sName) ?></a></div>
              <div class="muted small" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;"><?= e(mb_substr($m['body'], 0, 80)) ?></div>
              <div class="muted small"><?= e($relTime($m['created_at'])) ?></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>
