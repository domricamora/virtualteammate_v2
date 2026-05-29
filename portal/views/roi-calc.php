<?php
/** @var array $user @var array $hired @var string $client_company */
$pageTitle = 'ROI Calculator';
$subtitle  = 'Value Creation Calculator — what your Virtual Teammates are worth, today and as you scale.';

$nameOrEmail = static function (array $u): string {
    $n = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
    return $n !== '' ? $n : (string) ($u['email'] ?? '—');
};

// Per-VT lifetime months engaged. ended_at empty = still active = up to now.
$hiredWithMonths = [];
foreach ($hired as $h) {
    $start = $h['started_at'] ?? null;
    $end   = !empty($h['ended_at']) ? $h['ended_at'] : null;
    $months = 0;
    if ($start) {
        $startTs = strtotime($start);
        $endTs   = $end ? strtotime($end) : time();
        if ($startTs && $endTs && $endTs >= $startTs) {
            $months = max(0, (int) floor(($endTs - $startTs) / (86400 * 30.4375)));
        }
    }
    $hiredWithMonths[] = [
        'name'    => $nameOrEmail($h),
        'role'    => trim(($h['role_title'] ?? '') ?: ($h['department'] ?? '')),
        'months'  => $months,
        'active'  => empty($end),
        'started' => $start,
        'ended'   => $end,
    ];
}
$activeCount = 0; foreach ($hiredWithMonths as $h) { if ($h['active']) { $activeCount++; } }

// Monthly value-creation constants (mirror the staging [roi_savings_calculator] defaults).
$MONTHLY_FT = 5250;
$MONTHLY_PT = 2625;
?>
<div class="roiX" id="roiX_main"
     data-monthly-ft="<?= (int) $MONTHLY_FT ?>"
     data-monthly-pt="<?= (int) $MONTHLY_PT ?>"
     data-active-count="<?= (int) $activeCount ?>">

  <!-- Global header -->
  <div class="roiX__header">
    <div class="roiX__title-block">
      <div class="roiX__title">Value Creation Calculator</div>
      <div class="roiX__sub">
        Two views: <strong>Actual</strong> uses your hired Virtual Teammates to compute lifetime value created. <strong>Scenario</strong> models bi-weekly cost (US vs VT) so you can plan the next hire.
      </div>
    </div>
    <?php if ($client_company !== ''): ?>
      <div class="roiX__client"><i class="fa-solid fa-building"></i> <?= e($client_company) ?></div>
    <?php endif; ?>
  </div>

  <div class="roiX__shell">

    <!-- LEFT: Actual -->
    <section class="roiX__panel roiX__panel--actual">
      <div class="roiX__badge">Actual VTs Hired</div>
      <div class="roiX__panelTitle">Value Creation <em>(Actual)</em></div>
      <div class="roiX__panelSub">Lifetime value created by your hired team — based on months engaged × monthly value.</div>

      <div class="roiX__pillRow">
        <span class="roiX__pill"><i class="fa-solid fa-user-doctor"></i> Hired Virtual Teammates: <?= (int) $activeCount ?> active &middot; <?= count($hiredWithMonths) ?> total</span>
      </div>

      <div class="roiX__gaugeWrap" data-el="gaugeWrap">
        <div class="roiX__gauge" id="roiGauge">
          <svg viewBox="0 0 200 200" role="img" aria-label="Lifetime value gauge">
            <defs>
              <linearGradient id="roiGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#3919BA"/>
                <stop offset="100%" stop-color="#F6B845"/>
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
            <p class="muted small">No VT engagements yet. Once your CSM links a hired VT to your account, your lifetime value will track here automatically.</p>
          <?php else: ?>
            <table class="roiX__tbl">
              <thead><tr><th>VT</th><th>Role</th><th>Started</th><th>Months</th><th>Value</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach ($hiredWithMonths as $h):
                  $val = $h['months'] * $MONTHLY_FT;
                ?>
                  <tr>
                    <td><strong><?= e($h['name']) ?></strong></td>
                    <td class="muted small"><?= e($h['role'] ?: '—') ?></td>
                    <td class="muted small"><?= e($h['started'] ? substr($h['started'], 0, 10) : '—') ?></td>
                    <td><?= (int) $h['months'] ?></td>
                    <td>$<?= number_format($val) ?></td>
                    <td><?php if ($h['active']): ?><span class="pill pill-active">Active</span><?php else: ?><span class="pill pill-paused">Ended</span><?php endif; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </details>

      <div class="roiX__foot">Calculated from your hired VTs and engagement dates. Monthly value baseline: $<?= number_format($MONTHLY_FT) ?> FT / $<?= number_format($MONTHLY_PT) ?> PT.</div>
    </section>

    <!-- RIGHT: Scenario -->
    <section class="roiX__panel roiX__panel--scenario">
      <div class="roiX__badge roiX__badge--gold">Scenario Planner</div>
      <div class="roiX__panelTitle">Value Creation <em>(Scenario)</em></div>
      <div class="roiX__panelSub">Bi-weekly cost comparison — US in-house vs Virtual Teammate. Pick a role, tier, schedule and headcount.</div>

      <div class="roiX__field">
        <label class="roiX__label">Job category</label>
        <select class="roiX__select" data-el="jobSelect" aria-label="Job category">
          <optgroup label="Admin & Support">
            <option value="personal_assistant">Personal Assistant</option>
            <option value="administrative_assistant" selected>Administrative Assistant</option>
            <option value="executive_assistant">Executive Assistant</option>
            <option value="client_services_rep">Client Services Rep</option>
            <option value="client_services_specialist">Client Services Specialist</option>
            <option value="receptionist">Receptionist</option>
          </optgroup>
          <optgroup label="Finance">
            <option value="accountant">Accountant</option>
            <option value="billing_coordinator">Billing Coordinator</option>
            <option value="bookkeeper">Bookkeeper</option>
          </optgroup>
          <optgroup label="Marketing">
            <option value="copywriter">Copywriter</option>
            <option value="marketing_manager">Marketing Manager</option>
            <option value="marketing_coordinator">Marketing Coordinator</option>
            <option value="graphic_designer">Graphic Designer</option>
            <option value="social_media_coordinator">Social Media Coordinator</option>
            <option value="social_media_manager">Social Media Manager</option>
          </optgroup>
          <optgroup label="Sales">
            <option value="business_development">Business Development</option>
            <option value="account_manager">Account Manager</option>
            <option value="sales_manager">Sales Manager</option>
            <option value="sales_rep">Sales Rep</option>
          </optgroup>
          <optgroup label="Healthcare">
            <option value="medical_scheduling_coordinator">Medical Scheduling Coordinator</option>
            <option value="medical_receptionist">Medical Receptionist</option>
            <option value="medical_admin">Medical Admin</option>
            <option value="medical_insurance_verification_pre_cert">Medical Insurance Verification &amp; Pre-Cert</option>
            <option value="medical_biller">Medical Biller</option>
            <option value="medical_scribe">Medical Scribe</option>
            <option value="healthcare_referral_coordinator">Healthcare Referral Coordinator</option>
            <option value="telemedicine_services_assistant">Telemedicine Services Assistant</option>
          </optgroup>
          <optgroup label="Dental">
            <option value="dental_biller">Dental Biller</option>
            <option value="dental_admin">Dental Admin</option>
            <option value="dental_scribe">Dental Scribe</option>
            <option value="dental_referral_coordinator">Dental Referral Coordinator</option>
            <option value="dental_billing_specialist">Dental Billing Specialist</option>
            <option value="dental_insurance_coordinator">Dental Insurance Coordinator</option>
          </optgroup>
          <optgroup label="Data &amp; Analytics">
            <option value="data_analyst">Data Analyst</option>
            <option value="database_administrator">Database Administrator</option>
            <option value="bi_developer">BI Developer</option>
            <option value="quality_control_inspector">Quality Control Inspector</option>
            <option value="quality_assurance_analyst">Quality Assurance Analyst</option>
            <option value="quality_assurance_manager">Quality Assurance Manager</option>
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
          <input data-el="vtCount" type="range" min="0" max="20" value="<?= max(0, (int) $activeCount) ?>" step="1">
          <div class="roiX__sliderMeta">
            <span>0</span><span>5</span><span>10</span><span>15</span><span>20</span>
            <span class="roiX__bubble"><span data-el="vtCountVal"><?= (int) $activeCount ?> VTs</span></span>
          </div>
        </div>
      </div>

      <div class="roiX__results">
        <div class="roiX__kpi">
          <div class="roiX__kpiLabel">Estimated Bi-weekly Value Creation</div>
          <div class="roiX__kpiValue" data-el="biSavings">$0</div>
        </div>
        <div class="roiX__kpi">
          <div class="roiX__kpiLabel">Estimated Annual Value Creation</div>
          <div class="roiX__kpiValue" data-el="annualSavings">$0</div>
        </div>
      </div>

      <details class="roiX__details">
        <summary><i class="fa-solid fa-table"></i> Bi-weekly model (US vs VT)</summary>
        <div class="roiX__detailsBody">
          <div class="roiX__biGrid">
            <div class="roiX__biCard">
              <div class="roiX__biTitle">Pro</div>
              <div class="roiX__biRow"><span>US FT</span><strong>$1,800</strong></div>
              <div class="roiX__biRow"><span>VT FT</span><strong>$750</strong></div>
              <div class="roiX__biRow"><span>US PT</span><strong>$960</strong></div>
              <div class="roiX__biRow"><span>VT PT</span><strong>$400</strong></div>
            </div>
            <div class="roiX__biCard">
              <div class="roiX__biTitle">Specialist</div>
              <div class="roiX__biRow"><span>US FT</span><strong>$2,475</strong></div>
              <div class="roiX__biRow"><span>VT FT</span><strong>$1,000</strong></div>
              <div class="roiX__biRow"><span>US PT</span><strong>$1,320</strong></div>
              <div class="roiX__biRow"><span>VT PT</span><strong>$600</strong></div>
            </div>
          </div>
        </div>
      </details>

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

      <div class="roiX__foot">Bi-weekly clarity. Faster decisions. Rates match the live Virtual Teammate plugin.</div>
    </section>

  </div>
</div>

<style>
.roiX{max-width:1180px;margin:0 auto;}
.roiX__header{display:flex;justify-content:space-between;align-items:flex-end;gap:18px;flex-wrap:wrap;margin-bottom:18px;padding:0 6px;}
.roiX__title{font-size:24px;font-weight:800;color:#fff;letter-spacing:-.3px;}
.roiX__sub{font-size:13.5px;color:rgba(255,255,255,.65);max-width:780px;margin-top:6px;line-height:1.55;}
.roiX__client{font-size:13px;color:var(--gold,#d4a64a);font-weight:700;padding:8px 14px;border:1px solid rgba(247,185,69,.3);border-radius:30px;background:rgba(247,185,69,.08);}
.roiX__shell{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
@media (max-width:980px){.roiX__shell{grid-template-columns:1fr;}}
.roiX__panel{position:relative;padding:24px;border:1px solid rgba(255,255,255,.08);border-radius:18px;background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02));overflow:hidden;}
.roiX__panel--actual::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#3919BA,#7c3aed);}
.roiX__panel--scenario::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#F6B845,#fbd97a);}
.roiX__badge{display:inline-block;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:1.2px;padding:5px 12px;border-radius:30px;background:rgba(57,25,186,.18);color:#bba7fa;border:1px solid rgba(187,167,250,.3);margin-bottom:14px;}
.roiX__badge--gold{background:rgba(247,185,69,.16);color:var(--gold,#d4a64a);border-color:rgba(247,185,69,.3);}
.roiX__panelTitle{font-size:22px;font-weight:800;color:#fff;}
.roiX__panelTitle em{font-style:normal;background:linear-gradient(120deg,#dfa949,#f5e4b8,#dfa949);-webkit-background-clip:text;background-clip:text;color:transparent;}
.roiX__panelSub{font-size:13px;color:rgba(255,255,255,.6);margin:6px 0 14px;line-height:1.55;}
.roiX__pillRow{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;}
.roiX__pill{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:30px;background:rgba(255,255,255,.06);font-size:12.5px;color:rgba(255,255,255,.85);}
.roiX__pill i{color:var(--gold,#d4a64a);}
.roiX__gaugeWrap{display:flex;gap:18px;align-items:center;flex-wrap:wrap;margin:18px 0;}
.roiX__gauge{position:relative;width:180px;height:180px;flex:0 0 180px;}
.roiX__gauge svg{width:100%;height:100%;}
.roiX__gCenter{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;}
.roiX__gVal{font-size:24px;font-weight:800;color:#fff;letter-spacing:-.5px;line-height:1;}
.roiX__gLbl{font-size:11px;color:rgba(255,255,255,.5);margin-top:4px;text-transform:uppercase;letter-spacing:1px;}
.roiX__gMeta{flex:1;min-width:200px;}
.roiX__gMetaTitle{font-size:14px;font-weight:700;color:#fff;}
.roiX__gMetaSub{font-size:12.5px;color:rgba(255,255,255,.6);margin-top:4px;}
.roiX__gMetaSub strong{color:var(--gold,#d4a64a);}
.roiX__field{margin-top:14px;}
.roiX__label{display:block;font-size:11.5px;font-weight:700;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;}
.roiX__select{width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:10px 12px;font-size:13.5px;font-family:inherit;}
.roiX__select:focus{outline:none;border-color:var(--gold,#d4a64a);}
.roiX__grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px;}
.roiX__slider input[type=range]{width:100%;}
.roiX__sliderMeta{display:flex;justify-content:space-between;align-items:center;margin-top:6px;font-size:11px;color:rgba(255,255,255,.5);position:relative;}
.roiX__bubble{position:absolute;right:0;top:-26px;background:var(--gold,#d4a64a);color:#1a1535;font-size:11px;font-weight:800;padding:3px 10px;border-radius:30px;}
.roiX__results{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:18px;}
@media (max-width:520px){.roiX__results,.roiX__grid2{grid-template-columns:1fr;}}
.roiX__kpi{padding:18px;border-radius:14px;background:linear-gradient(135deg,rgba(247,185,69,.1),rgba(57,25,186,.18));border:1px solid rgba(247,185,69,.25);}
.roiX__kpiLabel{font-size:10.5px;font-weight:700;color:var(--gold,#d4a64a);text-transform:uppercase;letter-spacing:1px;}
.roiX__kpiValue{font-size:26px;font-weight:800;color:#fff;letter-spacing:-.5px;margin-top:4px;}
.roiX__details{margin-top:14px;border:1px solid rgba(255,255,255,.08);border-radius:12px;background:rgba(255,255,255,.02);}
.roiX__details summary{cursor:pointer;padding:12px 14px;font-size:13px;font-weight:600;color:rgba(255,255,255,.85);list-style:none;display:flex;align-items:center;gap:8px;}
.roiX__details summary::-webkit-details-marker{display:none;}
.roiX__details summary i{color:var(--gold,#d4a64a);}
.roiX__details[open] summary{border-bottom:1px solid rgba(255,255,255,.06);}
.roiX__detailsBody{padding:14px;}
.roiX__tbl{width:100%;border-collapse:collapse;font-size:12.5px;}
.roiX__tbl th,.roiX__tbl td{padding:8px 6px;text-align:left;border-bottom:1px solid rgba(255,255,255,.06);}
.roiX__tbl th{font-size:11px;color:rgba(255,255,255,.55);font-weight:700;text-transform:uppercase;letter-spacing:.6px;}
.roiX__break{display:flex;flex-direction:column;gap:6px;}
.roiX__breakRow{display:flex;justify-content:space-between;font-size:13px;color:rgba(255,255,255,.7);padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);}
.roiX__breakRow strong{color:var(--gold,#d4a64a);font-weight:800;}
.roiX__biGrid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media (max-width:520px){.roiX__biGrid{grid-template-columns:1fr;}}
.roiX__biCard{padding:14px;border:1px solid rgba(255,255,255,.08);border-radius:10px;background:rgba(255,255,255,.02);}
.roiX__biTitle{font-size:13px;font-weight:700;color:var(--gold,#d4a64a);margin-bottom:8px;text-transform:uppercase;letter-spacing:.6px;}
.roiX__biRow{display:flex;justify-content:space-between;font-size:12.5px;color:rgba(255,255,255,.7);padding:4px 0;}
.roiX__biRow strong{color:#fff;font-weight:700;}
.roiX__foot{margin-top:14px;font-size:11.5px;color:rgba(255,255,255,.45);text-align:center;font-style:italic;}
</style>

<script>
(function(){
  // Bi-weekly cost table — matches the staging [roi_savings_calculator] BIWEEK constants.
  var BIWEEK = {
    pro:        { vt: { ft: 750,  pt: 400 }, us: { ft: 1800, pt: 960  } },
    specialist: { vt: { ft: 1000, pt: 600 }, us: { ft: 2475, pt: 1320 } }
  };
  // Per-job suggestions (defaults to 'pro' tier). Specialist-heavy roles flip to specialist.
  var JOB_DEFAULT_TIER = {
    accountant: 'specialist', medical_biller: 'specialist', medical_scribe: 'specialist',
    dental_biller: 'specialist', dental_scribe: 'specialist',
    marketing_manager: 'specialist', sales_manager: 'specialist',
    business_development: 'specialist', account_manager: 'specialist',
    data_analyst: 'specialist', database_administrator: 'specialist', bi_developer: 'specialist',
    quality_assurance_manager: 'specialist'
  };

  var wrap = document.getElementById('roiX_main');
  if (!wrap) return;
  var monthlyFT = parseInt(wrap.getAttribute('data-monthly-ft'), 10) || 5250;
  var monthlyPT = parseInt(wrap.getAttribute('data-monthly-pt'), 10) || 2625;
  var activeCount = parseInt(wrap.getAttribute('data-active-count'), 10) || 0;

  function $(sel, ctx){ return (ctx||wrap).querySelector(sel); }
  function fmtUSD(n){ return '$' + Math.round(n).toLocaleString(); }
  function clamp(v, a, b){ return Math.max(a, Math.min(b, v)); }
  function pickMilestone(v){
    var m = [10000, 25000, 50000, 100000, 250000, 500000, 1000000, 2000000, 5000000, 10000000];
    for (var i = 0; i < m.length; i++) { if (v <= m[i]) return m[i]; }
    return m[m.length - 1];
  }

  // ── ACTUAL panel: lifetime value (server-supplied hired list + months)
  var hired = <?= json_encode($hiredWithMonths) ?>;
  var lifetimeValue = 0;
  for (var i = 0; i < hired.length; i++) { lifetimeValue += (hired[i].months || 0) * monthlyFT; }
  $('[data-el="lifetimeVal"]').textContent = fmtUSD(lifetimeValue);
  var milestone = pickMilestone(lifetimeValue);
  $('[data-el="milestone"]').textContent = fmtUSD(milestone);
  var pct = milestone > 0 ? clamp(lifetimeValue / milestone, 0, 1) : 0;
  var c = 2 * Math.PI * 72;
  var dash = pct * c;
  var prog = document.getElementById('roiGaugeProg');
  if (prog) { setTimeout(function(){ prog.setAttribute('stroke-dasharray', dash + ' ' + (c - dash)); prog.style.transition = 'stroke-dasharray .9s ease'; }, 100); }

  // ── SCENARIO panel: bi-weekly cost comparison
  var jobSel  = $('[data-el="jobSelect"]');
  var tierSel = $('[data-el="tierSelect"]');
  var schedSel= $('[data-el="schedSelect"]');
  var cntRng  = $('[data-el="vtCount"]');
  var cntVal  = $('[data-el="vtCountVal"]');
  var bubble  = wrap.querySelector('.roiX__bubble');

  function recalc(){
    var tier  = tierSel.value || 'pro';
    var sched = schedSel.value || 'ft';
    var n     = parseInt(cntRng.value, 10) || 0;
    var t     = BIWEEK[tier] || BIWEEK.pro;
    var vtBi  = (t.vt[sched] || 0) * n;
    var usBi  = (t.us[sched] || 0) * n;
    var save  = usBi - vtBi;
    var annual = save * 26;
    $('[data-el="biSavings"]').textContent     = fmtUSD(save);
    $('[data-el="annualSavings"]').textContent = fmtUSD(annual);
    $('[data-el="usBi"]').textContent          = fmtUSD(usBi);
    $('[data-el="vtBi"]').textContent          = fmtUSD(vtBi);
    $('[data-el="savedBi"]').textContent       = fmtUSD(save);
    cntVal.textContent = n + ' VT' + (n === 1 ? '' : 's');
    // Re-position the slider bubble proportionally
    if (bubble) {
      var min = parseInt(cntRng.min, 10) || 0;
      var max = parseInt(cntRng.max, 10) || 20;
      var pctR = max > min ? (n - min) / (max - min) : 0;
      bubble.style.left = (pctR * 100) + '%';
      bubble.style.right = 'auto';
      bubble.style.transform = 'translateX(-50%)';
    }
  }

  if (jobSel) {
    jobSel.addEventListener('change', function(){
      var suggested = JOB_DEFAULT_TIER[jobSel.value] || 'pro';
      tierSel.value = suggested;
      recalc();
    });
  }
  tierSel.addEventListener('change', recalc);
  schedSel.addEventListener('change', recalc);
  cntRng.addEventListener('input', recalc);

  recalc();
})();
</script>
