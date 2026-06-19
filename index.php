<?php
$page_title       = 'HIPAA-Compliant Medical & Dental Virtual Assistants | Virtual Teammate';
$page_description = 'Hire HIPAA-compliant medical & dental virtual assistants from a global talent network. Billing, scribing, scheduling, insurance verification. Save up to 73%.';
$og_title         = 'HIPAA-Compliant Medical & Dental Virtual Assistants';
$og_description   = 'Specialized virtual staffing for medical practices, dental clinics & RCM teams: sourced globally, delivered in your time zone.';
$canonical        = 'https://virtualteammate.com/';
$is_homepage      = true;
include 'includes/head.php';
include 'includes/nav.php';
?>
<main>
<!-- HERO -->
<header class="hero">
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>

  <!-- Subtle animated rising graph, behind the hero photo (shows through its transparency) -->
  <svg class="hero-graph" viewBox="0 0 1200 520" preserveAspectRatio="none" aria-hidden="true">
    <defs>
      <linearGradient id="hgLine" x1="0" y1="1" x2="1" y2="0">
        <stop offset="0%" stop-color="#7c3aed"/><stop offset="58%" stop-color="#dfa949"/><stop offset="100%" stop-color="#f5e4b8"/>
      </linearGradient>
      <linearGradient id="hgBar" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#dfa949" stop-opacity="0.50"/><stop offset="100%" stop-color="#7c3aed" stop-opacity="0.06"/>
      </linearGradient>
      <linearGradient id="hgArea" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#dfa949" stop-opacity="0.16"/><stop offset="100%" stop-color="#dfa949" stop-opacity="0"/>
      </linearGradient>
    </defs>
    <?php
      $hgBars = [70, 108, 90, 152, 134, 205, 182, 256, 238, 308];
      $hgN = count($hgBars); $hgSlot = 1200 / $hgN; $hgBw = $hgSlot * 0.52; $hgBase = 470;
      $hgPts = [[0, $hgBase - $hgBars[0]]];
      foreach ($hgBars as $i => $h) { $hgPts[] = [round($i * $hgSlot + $hgSlot / 2, 1), $hgBase - $h]; }
      $hgPts[] = [1200, $hgBase - $hgBars[$hgN - 1]];
      $hgLineD = 'M' . $hgPts[0][0] . ',' . $hgPts[0][1];
      foreach (array_slice($hgPts, 1) as $p) { $hgLineD .= ' L' . $p[0] . ',' . $p[1]; }
      $hgEnd = $hgPts[count($hgPts) - 1];
    ?>
    <g class="hg-grid" stroke="rgba(255,255,255,0.08)" stroke-width="1">
      <?php foreach ([110, 210, 310, 410] as $gy): ?><line x1="0" y1="<?= $gy ?>" x2="1200" y2="<?= $gy ?>"/><?php endforeach; ?>
      <?php for ($g = 1; $g < $hgN; $g++): $gx = round($g * $hgSlot, 1); ?><line x1="<?= $gx ?>" y1="64" x2="<?= $gx ?>" y2="470"/><?php endfor; ?>
      <line x1="0" y1="470" x2="1200" y2="470" stroke="rgba(255,255,255,0.16)"/>
    </g>
    <g class="hg-bars" fill="url(#hgBar)">
      <?php foreach ($hgBars as $i => $h): $x = round($i * $hgSlot + ($hgSlot - $hgBw) / 2, 1); ?>
        <rect class="hg-bar" x="<?= $x ?>" y="<?= $hgBase - $h ?>" width="<?= round($hgBw, 1) ?>" height="<?= $h ?>" rx="4" data-h="<?= $h ?>"/>
      <?php endforeach; ?>
    </g>
    <path class="hg-area" d="<?= $hgLineD ?> L1200,520 L0,520 Z" fill="url(#hgArea)"/>
    <path class="hg-line" d="<?= $hgLineD ?>" fill="none" stroke="url(#hgLine)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
    <circle class="hg-dot" cx="<?= $hgEnd[0] ?>" cy="<?= $hgEnd[1] ?>" r="6" fill="#f5e4b8"/>
  </svg>
  <div class="hero-inner">
    <div class="hero-eyebrow reveal"><span class="dot"></span> 30-Day Right-Fit Promise &middot; HIPAA-Compliant</div>
    <h1 class="hero-h1 reveal d1">Short-staffed and overworked?<br><span class="hero-h1-sub">Fully staff your practice <em>in weeks</em>, not months, for <em>73% less</em>.</span></h1>
    <ul class="hero-sub hero-sub-list hero-sub-cols reveal d2">
      <li>HIPAA-compliant medical &amp; dental virtual assistants trained on Epic, Athena, Dentrix and Open Dental.</li>
      <li>We own billing, scribing, scheduling and prior auth.</li>
      <li>Matched to your US time zone.</li>
      <li>Flat-rate pricing from <strong>$750 bi-weekly</strong>, backed by our <strong>30-Day Right-Fit Promise</strong>.</li>
    </ul>
    <p class="hero-guarantee reveal d2"><strong>Not the right fit in month one?</strong> We replace them at no cost, or refund every billed day.</p>
    <div class="hero-btns reveal d3">
      <a href="#cta-practice-audit" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#calculator" class="btn-glass">Calculate my savings <i class="fa-solid fa-calculator"></i></a>
    </div>
    <div class="cta-note reveal d3"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
    <a href="#cta-buyers-checklist" class="hero-textlink reveal d3" data-cta-intent="buyers-checklist">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-arrow-right"></i></a>
    <p class="avail-note reveal d3"><i class="fa-solid fa-hourglass-half"></i> New-practice onboarding is capped monthly so every match gets a proper search. Booking your audit now reserves your place in the next intake.</p>
  </div>

  <!-- Stats card — sits beside the pitch on the right (2×2), stacks below on mobile -->
  <div class="hero-stats reveal d5">
    <div class="hstat"><div class="hstat-num" data-count="73" data-suffix="%">73%</div><div class="hstat-lbl">Lower Staffing Cost</div></div>
    <div class="hstat"><div class="hstat-num" data-count="95" data-suffix="%">95%</div><div class="hstat-lbl">Clean-Claim Rate</div></div>
    <div class="hstat"><div class="hstat-num hstat-rating"><span data-count="4.9" data-decimals="1">4.9</span><i class="fa-solid fa-star"></i></div><div class="hstat-lbl"><i class="fa-brands fa-google" aria-hidden="true"></i> Avg Google Rating</div></div>
    <div class="hstat"><div class="hstat-num" data-count="30" data-suffix="-Day">30-Day</div><div class="hstat-lbl">Right-Fit Promise</div></div>
  </div>

  <!-- Trust row — full-width strip spanning the whole hero -->
  <div class="trust-row reveal d4">
    <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Compliant</div>
    <div class="trust-item"><i class="fa-solid fa-brain"></i> EHR &amp; Dental Software Trained</div>
    <div class="trust-item"><i class="fa-solid fa-user-shield"></i> Background Checked</div>
    <div class="trust-item"><i class="fa-solid fa-rotate"></i> 30-Day Right-Fit Promise</div>
    <div class="trust-item"><i class="fa-solid fa-globe"></i> Your Time Zone</div>
  </div>

</header>

<!-- CLIENT MARQUEE (sits directly under the hero for trust + recognition) -->
<?php
// Auto-scan the marquee logo folder so the track always matches what's on disk
// — drop/remove logos in images/clients/marquee/ with no code change needed.
$mq_files = glob(__DIR__ . '/images/clients/marquee/*.{png,jpg,jpeg,webp,svg,PNG,JPG,JPEG,WEBP,SVG}', GLOB_BRACE) ?: [];
sort($mq_files);
$mq_srcs = array_values(array_map(static function ($p) {
    return 'images/clients/marquee/' . rawurlencode(basename($p));
}, $mq_files));
?>
<div class="marquee" aria-label="Companies served by Virtual Teammate">
  <div class="marquee-lbl"><i class="fa-solid fa-handshake-angle"></i> Trusted by medical &amp; dental practices across the U.S.</div>
  <div class="marquee-track-wrap">
    <div class="marquee-track" id="mqTrack"></div>
  </div>
</div>
<script>window.VT_MARQUEE = <?= json_encode($mq_srcs, JSON_UNESCAPED_SLASHES) ?>;</script>

<?php
/**
 * Pull up to 6 live VT profiles from the portal SQLite DB for the
 * "Meet the Team" section. Filtered to medical + dental (matched against
 * department OR role_title) and only included if a downloaded photo exists
 * on disk. Falls back silently to an empty array if the DB isn't installed
 * yet (so the marketing page still renders even on environments without
 * the portal).
 */
function vtnew_homepage_profiles(int $limit = 6): array
{
    $dbPath = __DIR__ . '/data/portal.sqlite';
    if (!file_exists($dbPath)) { return []; }
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query(
            "SELECT u.id, u.first_name, u.last_name, u.country, p.role_title, p.department,
                    p.ehr_software, p.experience_years, p.status,
                    p.hipaa_certified, p.primary_skills, p.summary
             FROM vt_profiles p JOIN users u ON u.id = p.user_id
             WHERE u.active = 1
               AND (p.department LIKE '%medical%' OR p.department LIKE '%dental%'
                    OR p.role_title LIKE '%medical%' OR p.role_title LIKE '%dental%')
             ORDER BY RANDOM() LIMIT 24"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $_) { return []; }

    $out = [];
    foreach ($rows as $r) {
        $rid = (int) $r['id'];
        // Include only VTs with a real photo. Photos now live in the public
        // vtmedia folder (thumbnail or full-size); the legacy data/media path
        // is kept as a fallback. Mirrors talent-photo.php's resolution order.
        $thumbGlob = glob(__DIR__ . '/vtmedia/vt_thumbs/' . $rid . '.*');
        $hasPhoto = $thumbGlob
                 || glob(__DIR__ . '/vtmedia/vt/' . $rid . '/photo.*')
                 || glob(__DIR__ . '/data/media/vt/' . $rid . '/photo.*');
        if (!$hasPhoto) { continue; }
        // Prefer the lightweight 150x150 static thumbnail file; fall back to the
        // (thumb-preferring) PHP endpoint when no static thumb exists yet.
        $r['_thumb'] = $thumbGlob ? 'vtmedia/vt_thumbs/' . basename($thumbGlob[0]) : '';
        // Classify Medical vs Dental for the tag.
        $hay = strtolower(($r['department'] ?? '') . ' ' . ($r['role_title'] ?? ''));
        $r['_tag'] = str_contains($hay, 'dental') ? 'Dental VA' : 'Medical VA';
        $r['_tag_cls'] = str_contains($hay, 'dental') ? 'dent' : 'med';
        $out[] = $r;
        if (count($out) >= $limit) { break; }
    }
    return $out;
}
?>

<div class="divider"></div>

<!-- CASE STUDIES — Client KPI Results · Latest Audit (moved directly under the hero marquee) -->
<section class="sec" id="testimonials" aria-labelledby="cs-h">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-chart-column"></i> Proven Client Impact</div>
    <h2 class="sec-h2" id="cs-h">Targets set. <em>Targets beaten.</em></h2>
    <p class="sec-sub">Real numbers from our latest client audit: our teammates beat the targets that matter most to your practice.</p>
  </div>
  <div class="case-grid case-grid-4">
    <article class="case-card reveal d1">
      <div class="case-metric">
        <div class="case-metric-h">Insurance Verifications </div>
        <div class="case-metric-row">

          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+30%</span></div>
        </div>
        <div class="case-metric-foot">Payment posting also beat target: <strong>+48%</strong> of goal</div>
      </div>
      <p class="case-q">Our virtual teammate cleared insurance verifications <strong>30% above goal</strong> and payment posting <strong>48% over target</strong>: turning a challenging AR into one of the practice&rsquo;s strongest on record.</p>
      <div class="case-auth">
        <span class="ico-circle case-ico"><i class="fa-solid fa-ribbon"></i></span>
        <div>
          <div class="case-name">Cancer Center</div>
          <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d2">
      <div class="case-metric">
        <div class="case-metric-h">Pre-Certifications</div>
        <div class="case-metric-row">
      
          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+60%</span></div>
        </div>
        <div class="case-metric-foot">Claims volume also beat target: <strong>+47%</strong> of goal</div>
      </div>
      <p class="case-q">A dedicated billing teammate achieved pre-certs <strong>60% over target</strong> and claims volume <strong>47% above plan</strong>: keeping authorizations ahead of schedule and clean claims moving.</p>
      <div class="case-auth">
        <span class="ico-circle case-ico"><i class="fa-solid fa-hospital"></i></span>
        <div>
          <div class="case-name">Multi-Specialty Clinic</div>
          <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d3">
      <div class="case-metric">
        <div class="case-metric-h">Payment Posting</div>
        <div class="case-metric-row">
    
          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+40%</span></div>
        </div>
        <div class="case-metric-foot">Insurance verifications also beat target: <strong>+44%</strong> of goal</div>
      </div>
      <p class="case-q">Payment posting landed <strong>+40% over target</strong> and insurance verifications <strong>+44% over</strong>: streamlining the revenue cycle so claims go out clean and cash comes in faster.</p>
      <div class="case-auth">
        <span class="ico-circle case-ico"><i class="fa-solid fa-heart-pulse"></i></span>
        <div>
          <div class="case-name">Primary Care Group</div>
          <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d4">
      <div class="case-metric">
        <div class="case-metric-h">Claims Processed </div>
        <div class="case-metric-row">
   
          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+33%</span></div>
        </div>
        <div class="case-metric-foot"><strong>97%</strong> daily payment posting driving stronger financial performance.</div>
      </div>
      <p class="case-q">A specialty-billing teammate cleared claims <strong>33% above target</strong> for an endodontics &amp; oral-surgery group: a full surgical schedule billed on time.</p>
      <div class="case-auth">
        <span class="ico-circle case-ico"><i class="fa-solid fa-tooth"></i></span>
        <div>
          <div class="case-name">Endodontics &amp; Oral Surgery Group</div>
          <div class="case-svc"><i class="fa-solid fa-tooth"></i> Specialty Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>
  </div>
</section>

<div class="divider"></div>

<!-- ROI CALCULATOR -->
<section class="calc-wrap" id="calculator" aria-labelledby="calc-h">
  <div class="calc-head reveal">
    <div class="calc-badge"><i class="fa-solid fa-calculator"></i> Live ROI Calculator</div>
    <h2 class="sec-h2" id="calc-h">See your annual savings: <em>in real time</em></h2>
    <p class="sec-sub">See exactly what you&rsquo;d save against a US in-house hire: salary, benefits and payroll tax included.</p>
  </div>

  <div class="calc-glow">
  <div class="calc reveal d1" id="roiCalc">
    <!-- Live calculator (single column) -->
    <div class="calc-main">
    <!-- Slider on top — drag to set team size -->
    <div class="calc-top">
      <div class="calc-top-head">
        <h3>Estimate your savings</h3>
        <p class="calc-cap">Drag the slider to set your team size: your numbers update instantly.</p>
      </div>
      <div class="calc-slider-wrap">
        <div class="calc-slider-row">
          <div class="calc-slider-val" id="calcCountVal">2</div>
          <div class="calc-slider-cap">Virtual Teammates</div>
        </div>
        <input id="calcCount" class="calc-slider" type="range" min="1" max="25" value="2" step="1" aria-label="Number of Virtual Teammates"/>
        <div class="calc-ticks"><span>1</span><span>5</span><span>10</span><span>15</span><span>20</span><span>25</span></div>
      </div>
    </div>

    <!-- Values below are pre-rendered for the default state (2 full-time teammates)
         so crawlers/snippets see real numbers, not $0. js/main.js animates from
         these on load and recalculates live. Keep in sync with RATE in main.js
         (vt.ft $750 / us.ft $2,770 biweekly × 26): 2× FT → $144,040 vs $39,000,
         $105,040 saved / yr ($8,753/mo), 73%, 3-yr $315,120, per-teammate $52,520. -->
    <div class="calc-results">
      <div class="calc-results-top">
        <div class="calc-hero-num">
          <div class="calc-hero-lbl">Estimated Annual Savings</div>
          <div class="calc-hero-val" id="calcAnnual">$105,040</div>
          <div class="calc-hero-sub"><span id="calcMonthly">$8,753</span> per month saved</div>
        </div>
        <div class="calc-gauge" aria-hidden="true">
          <svg viewBox="0 0 200 200">
            <defs>
              <linearGradient id="gaugeGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%"  stop-color="#3919ba"/>
                <stop offset="100%" stop-color="#dfa949"/>
              </linearGradient>
            </defs>
            <circle class="gauge-bg" cx="100" cy="100" r="84"/>
            <circle class="gauge-fg" id="calcGaugeFg" cx="100" cy="100" r="84" stroke-dasharray="385 143"/>
          </svg>
          <div class="calc-gauge-center">
            <div class="calc-gauge-pct" id="calcPct">73%</div>
            <div class="calc-gauge-lbl">You Save</div>
          </div>
        </div>
      </div>

      <div class="calc-bars">
        <div class="calc-bar-row">
          <div class="calc-bar-head">
            <span class="calc-bar-name us"><span class="swatch"></span>US In-House Cost</span>
            <span class="calc-bar-amt us" id="calcUsAmt">$144,040 / yr</span>
          </div>
          <div class="calc-bar-track"><div class="calc-bar-fill us" id="calcUsBar" style="width:100%;"></div></div>
        </div>
        <div class="calc-bar-row">
          <div class="calc-bar-head">
            <span class="calc-bar-name vt"><span class="swatch"></span>Virtual Teammate Cost</span>
            <span class="calc-bar-amt vt" id="calcVtAmt">$39,000 / yr</span>
          </div>
          <div class="calc-bar-track"><div class="calc-bar-fill vt" id="calcVtBar" style="width:27%;"></div></div>
        </div>
      </div>

      <details class="calc-more">
        <summary class="calc-more-sum"><span><i class="fa-solid fa-circle-info"></i> See the full breakdown</span><i class="fa-solid fa-chevron-down calc-more-chev"></i></summary>
        <div class="calc-kpis">
          <div class="calc-kpi">
            <div class="calc-kpi-lbl"><i class="fa-solid fa-chart-line"></i> 3-Year Value</div>
            <div class="calc-kpi-val" id="calc3yr">$315,120</div>
          </div>
          <div class="calc-kpi">
            <div class="calc-kpi-lbl"><i class="fa-solid fa-user-tie"></i> Per-Teammate / Year</div>
            <div class="calc-kpi-val" id="calcPerVa">$52,520</div>
          </div>
          <div class="calc-kpi">
            <div class="calc-kpi-lbl"><i class="fa-solid fa-bolt"></i> Payback Period</div>
            <div class="calc-kpi-val" id="calcPayback">&lt; 1<span class="unit">mo</span></div>
          </div>
        </div>
      </details>

      <div class="calc-rate-band" aria-label="Published VT rate">
        <div class="calc-rate-h"><i class="fa-solid fa-tag"></i> Published Rate, No Quote Required</div>
        <div class="calc-rate-single">
          <div class="calc-rate-amt">$750<span>/bi-weekly</span></div>
          <div class="calc-rate-sub">Full-time, flat rate &middot; from $400 bi-weekly part-time</div>
        </div>
        <div class="calc-rate-foot">All-in. No payroll tax, benefits, recruiter fees or PTO billed on top.</div>
      </div>

      <p class="calc-foot">Rates based on live VT placement data. US comparison uses median fully-loaded in-house cost (salary + benefits + payroll burden) for equivalent healthcare admin roles.</p>
    </div>
    </div>
  </div>
  </div>
</section>

<div class="divider"></div>

<!-- SPECIALTIES -->
<section class="sec" id="specialties">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-stethoscope"></i> Our Specializations</div>
    <h2 class="sec-h2">Trained for your specialty, measured on your <em>numbers</em></h2>
    <p class="sec-sub">Every teammate is trained for your specialty before day one, so your A/R keeps moving, your schedule stays full, and your clinical team stays on patients.</p>
  </div>

  <div class="spec-grid">
    <article class="spec-card reveal d1">
      <div class="spec-aside spec-aside--med">
        <span class="spec-aside-eyebrow">For Medical Practices</span>
        <span class="spec-medallion"><i class="fa-solid fa-user-doctor"></i></span>
        <div class="spec-aside-cap">Focused on Outcomes.<br>Measured in Results.</div>
        <div class="spec-proof">
          <div class="spec-proof-h"><i class="fa-solid fa-chart-line"></i> Proof in the numbers: medical practices</div>
          <ul>
            <li><strong>AR days 52 &rarr; 23</strong>, Family Practice, Austin TX. $68k stalled claims recovered in 12 weeks.</li>
            <li><strong>+18 hrs/week reclaimed</strong>, Internal Medicine, Denver CO. Scribe ends after-hours charting.</li>
            <li><strong>95%+ clean claim rate</strong>: average across our specialist-tier medical billers.</li>
          </ul>
        </div>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow med"><span class="dot"></span> HIPAA Compliant &middot; Epic / Athena Trained</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span>
          <h3 class="spec-title">Medical virtual teammates</h3>
        </div>
        <p class="spec-desc">HIPAA-compliant medical teammates work inside your EHR to own billing, scribing, prior auth, scheduling and patient calls: so providers stop charting after hours and your AR keeps moving.</p>

        <div class="spec-pills">
          <a class="pill" href="services/medical-administrative-support/">Medical Admin Support <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-receptionist/">Medical Receptionist <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-biller/">Medical Biller <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-scribe/">Medical Scribe <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-assistant/">Medical Assistant <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <a href="#cta-practice-audit" class="spec-link" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
        <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      </div>
    </article>

    <article class="spec-card alt reveal d2">
      <div class="spec-aside spec-aside--dent">
        <span class="spec-aside-eyebrow">For Dental Practices</span>
        <span class="spec-medallion"><i class="fa-solid fa-tooth"></i></span>
        <div class="spec-aside-cap">Chairs Full.<br>Claims Clean.</div>
        <div class="spec-proof">
          <div class="spec-proof-h"><i class="fa-solid fa-chart-line"></i> Outcomes our dental teammates deliver</div>
          <ul>
            <li><strong>No-shows 22% &rarr; 9%</strong>, Pediatric Dental, Tampa FL. +14 visits/week recovered from confirmations &amp; rebooks.</li>
            <li><strong>30%+ no-show reduction</strong>, Phoenix AZ dental practice with virtual receptionist on recall.</li>
            <li><strong>CDT-coded claims with narratives</strong>: first-pass clean-claim rate above 95%.</li>
          </ul>
        </div>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow med"><span class="dot"></span> HIPAA Compliant &middot; Dentrix / Open Dental Trained</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-tooth"></i></span>
          <h3 class="spec-title">Dental virtual teammates</h3>
        </div>
        <p class="spec-desc">Teammates fluent in your dental software keep chairs full, recall lists worked, and claims clean.</p>

        <div class="spec-pills">
          <a class="pill" href="services/dental-admin/">Dental Admin Support <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-receptionist/">Dental Receptionist <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-biller/">Dental Biller <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-scribe/">Dental Scribe <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-coordinator/">Dental Coordinator <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <a href="#cta-practice-audit" class="spec-link" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
        <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      </div>
    </article>
  </div>

  <!-- HIPAA STRIP -->
  <div class="hipaa reveal">
    <div class="hbadges">
      <div class="hbadge">
        <span class="ico-circle"><i class="fa-solid fa-shield-halved"></i></span>
        <div class="hbadge-txt"><strong>HIPAA Compliant</strong><span>Every healthcare VA</span></div>
      </div>
      <div class="hbadge">
        <span class="ico-circle"><i class="fa-solid fa-brain"></i></span>
        <div class="hbadge-txt"><strong>EHR Trained</strong><span>Epic, Athena, Dentrix &amp; more</span></div>
      </div>
      <div class="hbadge">
        <span class="ico-circle"><i class="fa-solid fa-circle-check"></i></span>
        <div class="hbadge-txt"><strong>Multi-Stage Vetting</strong><span>Background + skills verified</span></div>
      </div>
      <div class="hbadge">
        <span class="ico-circle"><i class="fa-solid fa-clock"></i></span>
        <div class="hbadge-txt"><strong>Your Time Zone</strong><span>Real-time collaboration</span></div>
      </div>
    </div>
    <div class="hipaa-row">
    <div class="hipaa-main">
      <img class="hipaa-seal" src="<?= $home_base ?>images/hipaa-compliant-logo.webp" alt="HIPAA Compliant" width="792" height="748" loading="lazy">
      <div class="hipaa-cta">
        <a href="#cta-practice-audit" class="btn-gold" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
        <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      </div>
    </div>
    <div class="g-audit">
      <div class="g-audit-h"><i class="fa-solid fa-clipboard-check"></i> What the Practice Staffing Audit covers</div>
      <ul>
        <li><strong>Workflow inventory.</strong> We map the 8&ndash;12 admin and clinical workflows that drain the most provider time in your practice: intake, charts, refills, billing, scheduling, recall, prior auth.</li>
        <li><strong>Outsourcing priority list.</strong> You leave the call with a ranked list of what to delegate <em>first</em> for fastest ROI, and what to keep in-house.</li>
        <li><strong>Tier &amp; headcount recommendation.</strong> Specific call on Pro vs Specialist tier, full-time vs part-time, and how many teammates to start with for your specialty and patient volume.</li>
        <li><strong>Honest no-fit answer.</strong> If outsourcing isn&rsquo;t right for your practice, we&rsquo;ll tell you on the call. No follow-up sales sequence.</li>
      </ul>
    </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- GUARANTEE -->
<section class="sec guarantee" id="guarantee" aria-labelledby="g-h">
  <div class="g-wrap reveal">
    <div class="g-copy">
      <div class="sec-lbl"><i class="fa-solid fa-shield-halved"></i> The 30-Day Right-Fit Promise</div>
      <h2 class="sec-h2" id="g-h">If it&rsquo;s not working in month one, <em>we make it right</em></h2>
      <div class="g-cards">
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-arrows-rotate"></i></span>
          <h3>No-cost replacement</h3>
          <p>Decide a teammate isn&rsquo;t the right fit inside the first 30 days? Tell us why. We deliver a curated re-shortlist within <strong>2 business days</strong>, onboard the new teammate at no charge, and <strong>pause your billing</strong> until the replacement is live and producing.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-rotate-left"></i></span>
          <h3>30-day money-back window</h3>
          <p>Not sure outsourcing fits your practice? Cancel any time in the first 30 days: we refund every billed day in full. <strong>No clawbacks, no termination fees, no minimum-term lock-in.</strong> A staffing partner, not a contract trap.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
          <h3>Backup coverage built in</h3>
          <p>Sick day, PTO, family emergency? Your Client Success Manager (CSM) arranges a <strong>trained backup teammate within hours</strong>, at no extra cost, briefed on your workflows and EHR access. Coverage ships with every placement: it&rsquo;s not an upsell.</p>
        </div>
      </div>

      <div class="g-foot">
        <a href="#cta-practice-audit" data-cta-intent="practice-audit" class="btn-glass">Book my practice staffing audit <i class="fa-solid fa-clipboard-check"></i></a>
        <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- PROCESS -->
<section class="sec">
  <div style="text-align:center;max-width:600px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> Our Process</div>
    <h2 class="sec-h2">Hire in weeks, <em>not months</em></h2>
    <p class="sec-sub" style="margin:0 auto;">Three steps. Built for busy practices.</p>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1">
      <div class="pstep-head">
        <div class="pstep-num">01</div>
        <i class="fa-solid fa-calendar-check pstep-ico"></i>
      </div>
      <div class="pstep-eta"><i class="fa-solid fa-clock"></i> Within 24 hours</div>
      <h3 class="pstep-title">Book a practice staffing audit</h3>
      <p class="pstep-desc">Submit the form and we&rsquo;ll confirm your audit slot <strong>within one business day</strong>. The 20-minute diagnostic call maps your practice, workflows, and the exact clinical or admin support to delegate first.</p>
    </div>
    <div class="pstep reveal d2">
      <div class="pstep-head">
        <div class="pstep-num">02</div>
        <i class="fa-solid fa-users-viewfinder pstep-ico"></i>
      </div>
      <div class="pstep-eta"><i class="fa-solid fa-clock"></i> 1&ndash;2 business days</div>
      <h3 class="pstep-title">Meet &amp; interview candidates</h3>
      <p class="pstep-desc">Curated shortlist of HIPAA-compliant teammates delivered in <strong>1&ndash;2 business days</strong>: matched to your specialty, EHR, accent and time-zone preferences. You interview, we coordinate, you choose the fit.</p>
    </div>
    <div class="pstep reveal d3">
      <div class="pstep-head">
        <div class="pstep-num">03</div>
        <i class="fa-solid fa-rocket pstep-ico"></i>
      </div>
      <div class="pstep-eta"><i class="fa-solid fa-clock"></i> Live in 1&ndash;2 weeks</div>
      <h3 class="pstep-title">Launch &amp; go live</h3>
      <p class="pstep-desc">Agreement, billing, EHR access and SOP handoff, all wrapped in <strong>1&ndash;2 weeks</strong>. Your teammate starts producing in week one, with a dedicated Client Success Manager (CSM) and the 30-Day Right-Fit Promise behind every placement.</p>
    </div>
  </div>
  <div class="proc-cta reveal">
    <a href="#cta-practice-audit" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
    <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
  </div>
</section>

<div class="divider"></div>

<?php $homepage_profiles = vtnew_homepage_profiles(8); ?>
<!-- PROFILES (live from the VT portal — medical + dental only) -->
<section class="sec" id="profiles">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;" class="reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-id-badge"></i> Meet the Team</div>
      <h2 class="sec-h2" style="margin-bottom:0;">Your future <em>healthcare teammates</em></h2>
    </div>
    <div style="text-align:right;">
      <a href="#cta-practice-audit" data-cta-intent="practice-audit" class="btn-primary" style="font-size:15px;padding:14px 28px;">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
    </div>
  </div>
  <p class="sec-sub reveal" style="margin-top:6px;">Vetted specialists, HIPAA-compliant and trained on the systems your practice already runs.</p>

  <?php if (!empty($homepage_profiles)): ?>
    <div class="prof-grid">
      <?php
        // Singularize plural role/specialty tags coming from portal data
        // (e.g. "Dental Assistants" -> "Dental Assistant").
        $vt_singularize = static fn (string $s): string => preg_replace('/\bAssistants\b/i', 'Assistant', $s);
        foreach ($homepage_profiles as $i => $p):
        $first   = trim((string) ($p['first_name'] ?? ''));
        $lastIni = ($ln = trim((string) ($p['last_name'] ?? ''))) !== '' ? mb_strtoupper(mb_substr($ln, 0, 1)) . '.' : '';
        $name  = trim($first . ' ' . $lastIni);
        $role  = $vt_singularize($p['role_title'] ?: ($p['department'] ?: 'Healthcare VA'));
        $tagCls = $p['_tag_cls'] ?? 'med';
        $isDental = ($tagCls === 'dent');
        $tag   = $vt_singularize((string) ($p['_tag'] ?? ($isDental ? 'Dental VA' : 'Medical VA')));
        $years = (int) $p['experience_years'];
        $ehr   = trim((string) $p['ehr_software']);
        // Fallbacks so no card ships without Systems + Experience + a credential.
        $systems = $ehr !== '' ? $ehr : 'On request';
        $expLbl  = $years > 0 ? $years . '+ yrs' : 'Experienced';
        // 1-line capability: first sentence of summary, else primary skills, else a role-based default.
        $cap = trim((string) ($p['summary'] ?? ''));
        if ($cap !== '') {
            $cap = preg_split('/(?<=[.!?])\s+/', $cap)[0];
        } elseif (trim((string) ($p['primary_skills'] ?? '')) !== '') {
            $cap = trim((string) $p['primary_skills']);
        } else {
            $cap = 'Ready to support your ' . ($isDental ? 'dental' : 'medical') . ' workflows.';
        }
        $cap = function_exists('mb_strimwidth')
            ? mb_strimwidth($cap, 0, 96, '…')
            : (strlen($cap) > 96 ? substr($cap, 0, 95) . '…' : $cap);
        $delay = 'd' . (($i % 4) + 1);
        $photoSrc = !empty($p['_thumb']) ? $p['_thumb'] : ('talent-photo.php?id=' . (int) $p['id'] . '&thumb=1');
      ?>
        <a class="prof-card reveal <?= htmlspecialchars($delay) ?>" href="#cta-request" data-cta-intent="request" data-vt-id="<?= (int) $p['id'] ?>" data-vt-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>" aria-label="Request <?= htmlspecialchars($name !== '' ? $name : 'this teammate', ENT_QUOTES) ?>">
          <div class="prof-photo"><img src="<?= htmlspecialchars($photoSrc, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($name, ENT_QUOTES) ?>" width="96" height="96" decoding="async" loading="lazy"/></div>
          <div class="prof-name"><?= htmlspecialchars($name) ?></div>
          <span class="prof-tag <?= $isDental ? 'dent' : 'med' ?>"><?= htmlspecialchars($tag) ?></span>
          <div class="prof-role"><?= htmlspecialchars($role) ?></div>
          <div class="prof-meta">
            <span class="prof-meta-pill"><i class="fa-solid fa-laptop-medical"></i> <?= htmlspecialchars($systems) ?></span>
            <span class="prof-meta-pill"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($expLbl) ?></span>
            <span class="prof-meta-pill"><i class="fa-solid fa-shield-halved"></i> HIPAA-Compliant</span>
          </div>
          <div class="prof-cap"><?= htmlspecialchars($cap) ?></div>
          <span class="prof-cta">Request this teammate <i class="fa-solid fa-arrow-right"></i></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="prof-empty card">
      <i class="fa-solid fa-user-doctor" style="color:var(--gold);font-size:24px;margin-bottom:10px;"></i>
      <p>Our medical &amp; dental bench is reviewed and matched manually for every engagement. <a href="#cta-practice-audit" data-cta-intent="practice-audit">Book my practice staffing audit</a> to see candidates tailored to your specialty, EHR and time-zone preferences.</p>
    </div>
  <?php endif; ?>
</section>

<div class="divider"></div>

<!-- SECURITY & COMPLIANCE — every claim translated into plain language: what the
     safeguard is, and what it actually means for the practice. -->
<section class="sec comp-section" id="security" aria-labelledby="comp-h">
  <div style="text-align:center;max-width:680px;margin:0 auto;" class="reveal">
    <img class="hipaa-seal" src="<?= $home_base ?>images/hipaa-compliant-logo.webp" alt="HIPAA Compliant" width="792" height="748" loading="lazy" style="margin:0 auto 20px;">
    <div class="sec-lbl"><i class="fa-solid fa-lock"></i> Security &amp; Compliance</div>
    <h2 class="sec-h2">Patient data, <em>protected by design</em></h2>
    <p class="sec-sub" style="margin:0 auto;">Compliance here isn&rsquo;t a checkbox: here&rsquo;s what each safeguard is, and what it actually means for your practice, in plain English.</p>
  </div>
  <div class="comp-grid">
    <div class="comp-card reveal d1">
      <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
      <h3>HIPAA training &amp; certification</h3>
      <p class="comp-what"><strong>What it is:</strong> every healthcare and dental teammate completes formal HIPAA training and certification <em>before</em> they touch patient data.</p>
      <p class="comp-means"><strong>What it means for you:</strong> the person handling your charts knows exactly what counts as protected health information, and the rules for keeping it private, held to the same standard as your in-house staff.</p>
    </div>
    <div class="comp-card reveal d2">
      <span class="ico-circle lg"><i class="fa-solid fa-file-signature"></i></span>
      <h3>BAA-compatible agreement</h3>
      <p class="comp-what"><strong>What it is:</strong> a Business Associate Agreement (BAA) is the contract HIPAA requires whenever an outside party handles patient data for you. Every VT teammate signs a BAA-compatible confidentiality agreement.</p>
      <p class="comp-means"><strong>What it means for you:</strong> our data-handling duties are in writing and legally binding, not a verbal promise you have to take on trust.</p>
    </div>
    <div class="comp-card reveal d3">
      <span class="ico-circle lg"><i class="fa-solid fa-building-shield"></i></span>
      <h3>Aligned controls</h3>
      <p class="comp-what"><strong>What it is:</strong> the VT infrastructure that touches your data runs on a documented set of security controls, access management, device hardening, logging and monitoring, aligned to recognized industry standards.</p>
      <p class="comp-means"><strong>What it means for you:</strong> the laptops, logins and networks your teammate works from are locked down to a recognized industry standard, not left to whatever each person happens to have at home.</p>
    </div>
    <div class="comp-card reveal d1">
      <span class="ico-circle lg"><i class="fa-solid fa-clipboard-list"></i></span>
      <h3>Full audit trail</h3>
      <p class="comp-what"><strong>What it is:</strong> every time a teammate opens, views or changes something in your systems it&rsquo;s logged, who, what, when, from where, and retained for 12 months.</p>
      <p class="comp-means"><strong>What it means for you:</strong> if you ever need to know exactly who accessed a record and when, there&rsquo;s a reviewable paper trail, available from your CSM on request.</p>
    </div>
    <div class="comp-card reveal d2">
      <span class="ico-circle lg"><i class="fa-solid fa-laptop-medical"></i></span>
      <h3>Device &amp; access security</h3>
      <p class="comp-what"><strong>What it is:</strong> encrypted laptops, hardware multi-factor authentication, a mandatory password manager, controlled network access and least-privilege EHR permissions.</p>
      <p class="comp-means"><strong>What it means for you:</strong> your teammate can reach only the systems they need, only from a secured device: so a lost laptop or stolen password can&rsquo;t expose your records.</p>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- GLOBAL COVERAGE (client-benefit framing — single map, one anchor per continent) -->
<section class="global global-compact" id="global" aria-labelledby="global-h">
  <div class="global-grid">
    <div class="global-l reveal">
      <div class="sec-lbl"><i class="fa-solid fa-earth-americas"></i> Global Reach &middot; Your Staffing Problem, Solved</div>
      <h2 class="sec-h2" id="global-h">A bigger bench, a <em>better match</em>, on your time zone</h2>
      <p class="sec-sub">Done with the hiring grind? We fill your exact gap fast: teammates matched to your specialty, software and US hours, fully managed by your CSM.</p>

      <ul class="global-benefits">
        <li><span class="ico-circle"><i class="fa-solid fa-bolt"></i></span><span><strong>Fully staffed in weeks, not months.</strong> Stop carrying open seats: a curated shortlist lands in 1&ndash;2 business days and your teammate is live in 1&ndash;2 weeks.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-clock"></i></span><span><strong>Covered in your US time zone.</strong> Morning, afternoon, evening or overnight: your teammate works your hours, not a call-center queue.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-shield-halved"></i></span><span><strong>Never short-handed again.</strong> Sick day, PTO or turnover? Trained backup coverage steps in within hours, at no extra cost: the work never goes dark.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-check"></i></span><span><strong>Best-fit talent, not least-cost talent.</strong> A bigger bench lets us recruit for your specialty, EHR, language and accent, not just whoever&rsquo;s available.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-sack-dollar"></i></span><span><strong>Keep up to 73% of your staffing budget</strong> to reinvest in patient care and growth. One flat, all-in rate covers everything: no benefits, payroll tax, recruiter fees or PTO to manage.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-tie"></i></span><span><strong>One US point of contact from day one.</strong> Your named CSM owns onboarding, quality, backups and quarterly reviews: it&rsquo;s how we hold 95%+ client retention.</span></li>
      </ul>
    </div>

    <div class="reveal d2">
      <div class="world" aria-label="Virtual Teammate global coverage, one anchor per continent">
        <div class="world-grid"></div>
        <img class="world-map-img" src="images/world-map.svg" alt="World map showing Virtual Teammate global talent coverage" width="1010" height="666" loading="lazy">
        <svg class="world-arc" viewBox="0 0 1000 500" preserveAspectRatio="none" aria-hidden="true">
          <defs>
            <linearGradient id="arcGrad" x1="0" x2="1" y1="0" y2="0">
              <stop offset="0%" stop-color="rgba(223,169,73,0.7)"/>
              <stop offset="100%" stop-color="rgba(57,25,186,0.7)"/>
            </linearGradient>
          </defs>
          <!-- One connection from US HQ (Arizona) to each populated continent -->
          <path d="M160,270 Q210,330 300,380"/>
          <path d="M160,270 Q300,150 460,205"/>
          <path d="M160,270 Q360,370 525,345"/>
          <path d="M160,270 Q470,150 810,320"/>
        </svg>
        <!-- One anchor pin per continent (calibrated to the map projection) -->
        <div class="world-pin" style="top:54%;left:16%;"><div class="pin-lbl">North America &middot; HQ</div></div>
        <div class="world-pin" style="top:76%;left:30%;"><div class="pin-lbl">South America</div></div>
        <div class="world-pin" style="top:41%;left:46%;"><div class="pin-lbl">Europe</div></div>
        <div class="world-pin" style="top:69%;left:52%;"><div class="pin-lbl">Africa</div></div>
        <div class="world-pin" style="top:64%;left:81%;"><div class="pin-lbl">Asia</div></div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- IN THE NEWS & PRESS RELEASES (logo marquee, toned-down → full color on hover) -->
<div class="news" aria-label="Virtual Teammate in the news and press releases">
  <div class="news-lbl"><i class="fa-solid fa-newspaper"></i> Recognized in the Press</div>
  <div class="news-track-wrap">
    <div class="news-track" id="newsTrack"></div>
  </div>
</div>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div>
    <h2 class="sec-h2">Frequently asked questions</h2>
  </div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a medical or dental teammate cost?</div><div class="faq-a">Published flat-rate pricing, no quote required. <strong>Full-time teammates start at $750 bi-weekly</strong> ($400 part-time). <strong>Specialist tier</strong> (medical billing, scribing, advanced coding, dental billing) starts at <strong>$1,000 bi-weekly full-time</strong> ($600 part-time). All-in flat rate: no benefits, payroll tax, recruiter fees or PTO billed on top. Typically 60&ndash;73% less than an equivalent US in-house hire: use the calculator above for your exact savings.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-rotate"></i> What happens if my teammate isn&rsquo;t the right fit?</div><div class="faq-a">The 30-Day Right-Fit Promise covers two scenarios: <strong>(1)</strong> the teammate isn&rsquo;t the right fit &rarr; we replace them at no cost with a re-shortlist inside 2 business days; <strong>(2)</strong> outsourcing isn&rsquo;t working for your practice &rarr; cancel within the first 30 days and we refund every billed day, no clawback. The guarantee is published in writing, not hidden in a sales call.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> What security &amp; compliance do you carry?</div><div class="faq-a">Five layers: <strong>HIPAA training &amp; certification</strong> for every teammate, a <strong>BAA-compatible</strong> confidentiality agreement, <strong>industry-aligned security controls</strong> on the infrastructure that touches your data, a 12-month <strong>audit trail</strong> of every access event, and locked-down <strong>device &amp; access security</strong> (encrypted laptops, hardware MFA, least-privilege EHR access). Our <a href="#security">Security &amp; Compliance section</a> spells out exactly what each one means for your practice.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-bolt"></i> How quickly can I get a teammate started?</div><div class="faq-a">Curated shortlist in <strong>1&ndash;2 business days</strong>. After you pick your teammate, onboarding wraps in <strong>1&ndash;2 weeks</strong>: agreement, EHR access, SOP handoff, shadow week, then live work. Your dedicated Client Success Manager (CSM) runs the timeline so it lands when you need it.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-brain"></i> What EHR and dental software do your teammates know?</div><div class="faq-a">Medical teammates are trained on Epic, eClinicalWorks, Athenahealth, NextGen, Practice Fusion and Kareo. Dental teammates are proficient in Dentrix, Dentrix Ascend, Open Dental and Carestream. Plus all major clearinghouses (Availity, Office Ally, Waystar, DentalXChange).</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your teammates based?</div><div class="faq-a">Wherever the best fit lives. Our global recruiting reach lets us match for your specialty, EHR, accent, language, and US time-zone shift, not just whoever happens to be on the bench. You hire for skill set; we handle the sourcing.</div></div>
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> Will I have a dedicated point of contact?</div><div class="faq-a">Yes. From day one, a named Client Success Manager (CSM) is on your account: reachable on email, Slack/Teams, and a direct line during your business hours. They own performance, backup coverage and quarterly check-ins so you&rsquo;re never managing the placement alone.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-handshake-slash"></i> Will I be locked into a long-term contract?</div><div class="faq-a">No. Most healthcare VA agencies require 6&ndash;12 month commitments: we don&rsquo;t. Pause, scale up, scale down or cancel month-to-month, with no early-termination fees and no recapture clauses. The 30-Day Right-Fit Promise covers your first month on top of that.</div></div>
  </div>
</section>

<div class="divider"></div>

<!-- CONSOLIDATED OFFER — "Here's Exactly What You Get" -->
<section class="sec offer" id="offer" aria-labelledby="offer-h">
  <div class="reveal" style="text-align:center;max-width:760px;margin:0 auto;">
    <div class="sec-lbl"><i class="fa-solid fa-box-open"></i> Here&rsquo;s the Deal</div>
    <h2 class="sec-h2" id="offer-h">Here&rsquo;s exactly what you get</h2>
    <p class="sec-sub" style="margin:0 auto;">HIPAA-compliant medical &amp; dental virtual assistants, trained on Epic, Athena, Dentrix and Open Dental, fully staffed in weeks, not months, for up to 73% less than local hiring.</p>
  </div>

  <div class="offer-grid reveal d1">
    <div class="offer-item">
      <span class="ico-circle"><i class="fa-solid fa-tag"></i></span>
      <p><strong>Flat-rate pricing, billed bi-weekly.</strong> Starting at $750 bi-weekly, with highly trained, dedicated Healthcare VTs at $1,000 bi-weekly. All-in: no markups, no surprises.</p>
    </div>
    <div class="offer-item">
      <span class="ico-circle"><i class="fa-solid fa-bolt"></i></span>
      <p><strong>Matched in days.</strong> Shortlist of 3 in 2 business days. Live in 1&ndash;2 weeks.</p>
    </div>
    <div class="offer-item">
      <span class="ico-circle"><i class="fa-solid fa-user-tie"></i></span>
      <p><strong>Your dedicated Client Success Manager (CSM)</strong> ensures you hit your goals, partnering closely with you and your Virtual Teammates. Beyond weekly and monthly check-ins, your CSM is your dedicated point of contact across the communication platform that your practice already uses. We go where your team talks.</p>
    </div>
    <div class="offer-item">
      <span class="ico-circle"><i class="fa-solid fa-shield-halved"></i></span>
      <p><strong>30-Day Right-Fit Promise.</strong> Free replacement, 30-day money-back, and backup coverage, plus a written billing-outcome commitment for your practice.</p>
    </div>
    <div class="offer-item">
      <span class="ico-circle"><i class="fa-solid fa-lock-open"></i></span>
      <p><strong>No long-term lock-in.</strong> Bi-weekly from day one, starting at $750 with highly trained at $1,000 bi-weekly for dedicated Healthcare VTs.</p>
    </div>
    <div class="offer-item">
      <span class="ico-circle"><i class="fa-solid fa-robot"></i></span>
      <p><strong>AI productivity, human accountability.</strong> A person reviews and signs off on every AI-assisted output before it reaches your practice.</p>
    </div>
  </div>

  <div class="offer-cta reveal">
    <a href="#cta-practice-audit" class="btn-gold" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
    <p class="offer-cta-fine">20 minutes. No obligation. Covered by the 30-Day Right-Fit Promise.</p>
    <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
  </div>
</section>

<div class="divider"></div>

<!-- CTA FORM -->
<section class="sec cta-stages-section" id="cta" style="padding-top:80px;padding-bottom:110px;">
  <div class="cta-stages-h reveal">
    <div class="sec-lbl"><i class="fa-solid fa-paper-plane"></i> Three Ways to Start</div>
    <h2 class="cta-h2" style="font-size:36px;">Pick the entry point<br>that fits where you are</h2>
    <p class="cta-sub">Just exploring, ready to diagnose, or ready to scope: three ways in, same team on the other side.</p>
    <p class="avail-note avail-note-center"><i class="fa-solid fa-hourglass-half"></i> New-practice onboarding is capped monthly so every match gets a proper search. Booking your audit now reserves your place in the next intake.</p>
  </div>

  <div class="cta-stages-grid reveal d1">
    <article class="cta-stage" data-cta-intent="buyers-checklist">
      <div class="cta-stage-tag">Just exploring</div>
      <span class="ico-circle lg"><i class="fa-solid fa-file-lines"></i></span>
      <h3>HIPAA VA buyer&rsquo;s checklist</h3>
      <p class="cta-stage-lead">Key questions to ask any healthcare VA agency before you sign. Drop your email: we&rsquo;ll send the PDF.</p>
      <ul class="cta-stage-list">
        <li>Compliance, BAA, audit-trail questions</li>
        <li>Pricing-model traps to watch for</li>
        <li>Performance &amp; quality SLAs to demand</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-buyers-checklist" data-cta-intent="buyers-checklist">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-mid" data-cta-intent="practice-audit">
      <div class="cta-stage-tag">Ready to diagnose</div>
      <span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span>
      <h3>20-min practice staffing audit</h3>
      <p class="cta-stage-lead">Diagnostic-only call. We map your top admin and clinical workflows and tell you what to delegate first.</p>
      <ul class="cta-stage-list">
        <li>Workflow inventory (8&ndash;12 mapped)</li>
        <li>Ranked outsourcing-priority list</li>
        <li>Tier + headcount recommendation</li>
      </ul>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      <a class="btn-cta-stage" href="#cta-practice-audit" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-high" data-cta-intent="strategy-call">
      <div class="cta-stage-tag">Ready to start</div>
      <span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span>
      <h3>Jumpstart call</h3>
      <p class="cta-stage-lead">30-min call. Map your needs, define the role, get a curated shortlist in 1&ndash;2 business days.</p>
      <ul class="cta-stage-list">
        <li>Role scope + EHR / specialty match</li>
        <li>Tailored candidate shortlist</li>
        <li>Onboarding plan + CSM intro</li>
      </ul>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      <a class="btn-cta-stage" href="#cta-strategy-call" data-cta-intent="strategy-call">Get started in 24 hours <i class="fa-solid fa-arrow-right"></i></a>
    </article>
  </div>

  <p class="cta-stages-foot reveal">Prefer to start with a diagnostic? <a href="#cta-practice-audit" data-cta-intent="practice-audit">Book my practice staffing audit</a> and a Client Success Manager (CSM) will map it out with you.</p>
</section>


<!-- ENTRY-POINT MODALS — one tailored form per funnel stage. Opened via the
     #cta-<intent> hash (CSS :target, so they work with JS off too); the script
     near the footer adds scroll-lock, autofocus and ESC-to-close. Each posts to
     lead.php and creates a lead. -->
<div class="cta-modal" id="cta-buyers-checklist" role="dialog" aria-modal="true" aria-labelledby="ccm-bc-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-file-lines"></i> Just exploring</div>
    <h2 class="cta-modal-h" id="ccm-bc-h">Grab the HIPAA VA buyer&rsquo;s checklist</h2>
    <p class="cta-modal-sub">Unlock new levels of productivity and patient care. Enter your email to receive our HIPAA VA buyer&rsquo;s checklist and learn how to choose the right virtual staffing partner for long-term success.</p>
    <form class="cta-modal-form" id="ctaChecklistForm" method="post" action="<?= $home_base ?>lead.php"
          data-lead-thanks="Check your inbox: your checklist is on the way.">
      <input type="hidden" name="intent" value="buyers-checklist">
      <input type="hidden" name="form" value="homepage-checklist">
      <input type="hidden" name="source" value="HIPAA VA Buyer&rsquo;s Checklist">
      <div class="cf-row" style="grid-template-columns:1fr;margin-bottom:16px;">
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Send me the checklist <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>No spam &middot; Just the checklist and the occasional helpful tip</div>
    </form>
  </div>
</div>

<div class="cta-modal" id="cta-practice-audit" role="dialog" aria-modal="true" aria-labelledby="ccm-pa-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-clipboard-check"></i> 20-Min Practice Staffing Audit</div>
    <h2 class="cta-modal-h" id="ccm-pa-h">Book my practice staffing audit</h2>
    <p class="cta-modal-sub">Pick a time that works for you: a Dedicated Client Success Manager will map your busiest workflows and show you which roles to delegate first. Diagnostic only, no obligation.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>

<div class="cta-modal" id="cta-strategy-call" role="dialog" aria-modal="true" aria-labelledby="ccm-sc-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-calendar-check"></i> Strategy Call &amp; Jumpstart</div>
    <h2 class="cta-modal-h" id="ccm-sc-h">Book my strategy call</h2>
    <p class="cta-modal-sub">Pick a time below and we&rsquo;ll scope your needs, define the role, and map your first 30 days: so your teammate is productive fast. No commitment, covered by the 30-Day Right-Fit Promise.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>

<!-- Candidate-request modal. Opened from a teammate card; the script near the
     footer copies the clicked profile's name + id into vt_interest / vt_id (and
     the visible heading) before it shows, so the lead records exactly who the
     visitor asked for. Posts to lead.php like the checklist form. -->
<div class="cta-modal" id="cta-request" role="dialog" aria-modal="true" aria-labelledby="ccm-rq-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-user-plus"></i> Request a teammate</div>
    <h2 class="cta-modal-h" id="ccm-rq-h">Request <span data-vt-name-target>this teammate</span></h2>
    <p class="cta-modal-sub">Tell us where to reach you and your dedicated Client Success Manager will check <span data-vt-name-target>this teammate</span>&rsquo;s availability, then line up similar HIPAA-compliant matches for your specialty, EHR and time zone.</p>
    <form class="cta-modal-form" id="ctaRequestForm" method="post" action="<?= $home_base ?>lead.php"
          data-lead-thanks="Request received: your Client Success Manager will be in touch within one business day.">
      <input type="hidden" name="intent" value="request">
      <input type="hidden" name="form" value="homepage-request">
      <input type="hidden" name="source" value="Teammate Request">
      <input type="hidden" name="vt_id" value="">
      <input type="hidden" name="vt_interest" value="">
      <div class="cf-row" style="margin-bottom:16px;">
        <input class="cf-field" name="first_name" type="text" placeholder="First Name" required>
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Request this teammate <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>Diagnostic-first &middot; No obligation, covered by the 30-Day Right-Fit Promise</div>
    </form>
  </div>
</div>

<!-- HubSpot Meetings embed loader — powers the scheduler inside the booking modals.
     A resize nudge on open makes the iframe size correctly after the modal shows. -->
<script type="text/javascript" src="https://static.hsappstatic.net/MeetingsEmbed/ex/MeetingsEmbedCode.js"></script>
<script>
window.addEventListener('hashchange', function () {
  var h = location.hash;
  if (!h || h.length < 2) { return; }
  try {
    var m = document.querySelector(h);
    if (m && m.querySelector('.meetings-iframe-container')) {
      setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 80);
    }
  } catch (e) {}
});
</script>

</main>
<?php $hide_lead_band = true; /* homepage already has the #cta + ROI forms */ ?>
<?php include 'includes/footer.php'; ?>

<!-- Dedicated lead handlers for EVERY homepage form. Each form (the CSM callback
     + the three entry-point modals) is bound by its own unique id to its OWN
     submit handler — no class-wide loop and no shared data-lead-form handler, so
     one form's flow can never be reused for, or interfere with, another. None of
     these forms carry data-lead-form, so the generic handler in js/main.js never
     touches them. This self-contained block is their ONLY handler (no race, no
     double-submit, and immune to any error elsewhere in the main.js bundle). It
     posts to each form's action (lead.php) and swaps in a thank-you on success. -->
<script>
(function () {
  // Shared low-level transport only — NOT a handler. Posts one form to its
  // action and swaps in a thank-you. Each form gets its own handler below.
  function postLead(form) {
    var url  = form.getAttribute('action') || 'lead.php';
    var btn  = form.querySelector('[type=submit]');
    var note = form.querySelector('[data-lead-note]');
    if (note) { note.textContent = ''; note.classList.remove('is-err'); }
    function resetBtn() {
      if (btn) {
        btn.disabled = false;
        btn.classList.remove('is-loading');
        if (btn.dataset.orig !== undefined) { btn.innerHTML = btn.dataset.orig; }
      }
    }
    if (btn) {
      btn.dataset.orig = btn.innerHTML;
      btn.disabled = true;
      btn.classList.add('is-loading');
      btn.innerHTML = '<span class="vtd-spinner" aria-hidden="true"></span> Sending…';
    }
    fetch(url, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res && res.ok) {
          var msg = form.getAttribute('data-lead-thanks') || 'Thank you! We’ll be in touch within 1 business day.';
          form.innerHTML = '<div class="lead-thanks"><i class="fa-solid fa-circle-check"></i><p>' + msg + '</p></div>';
        } else {
          if (note) { note.textContent = (res && res.error) ? res.error : 'Something went wrong — please try again.'; note.classList.add('is-err'); }
          resetBtn();
        }
      })
      .catch(function () {
        if (note) { note.textContent = 'Network error — please try again.'; note.classList.add('is-err'); }
        resetBtn();
      });
  }

  // One dedicated handler per form. `this` is always the form that fired the
  // event, so each handler owns exactly one form and is never reused.
  function handleCsmCallback(e)   { e.preventDefault(); postLead(this); }
  function handleChecklist(e)     { e.preventDefault(); postLead(this); }
  function handlePracticeAudit(e) { e.preventDefault(); postLead(this); }
  function handleStrategyCall(e)  { e.preventDefault(); postLead(this); }
  function handleRequest(e)       { e.preventDefault(); postLead(this); }

  function attach(id, handler) {
    var form = document.getElementById(id);
    if (!form || form.dataset.leadBound) { return; }
    form.dataset.leadBound = '1';
    form.addEventListener('submit', handler);
  }
  function init() {
    attach('csmCallback',      handleCsmCallback);
    attach('ctaChecklistForm', handleChecklist);
    attach('ctaAuditForm',     handlePracticeAudit);
    attach('ctaStrategyForm',  handleStrategyCall);
    attach('ctaRequestForm',   handleRequest);
  }
  // Bind now (this script sits after every form in the DOM) and again on
  // DOMContentLoaded as a safety net. attach() is idempotent per form.
  init();
  document.addEventListener('DOMContentLoaded', init);
})();
</script>

<!-- Entry-point modal behavior. Open/closed state is driven by the URL hash
     (#cta-<intent>) so the CSS :target rule shows the right form even with JS
     off. This layer adds: scroll-lock (page freezes behind the modal), autofocus
     on the first field, ESC to close, and capturing scroll position BEFORE the
     hash changes so opening never makes the page jump. -->
<script>
(function () {
  var modals = {};
  document.querySelectorAll('.cta-modal').forEach(function (m) { modals['#' + m.id] = m; });
  if (!Object.keys(modals).length) { return; }
  var docEl = document.documentElement, body = document.body;
  var savedY = 0, locked = false;

  // Snapshot each modal form's pristine markup once, so closing a modal can
  // fully reset it — clears typed values AND drops any thank-you / error state
  // the submit handler swapped in. The form's submit listener lives on the
  // <form> element itself (not its children), so restoring innerHTML keeps it
  // wired; every reopen starts clean.
  var pristine = [];
  document.querySelectorAll('.cta-modal .cta-modal-form').forEach(function (f) {
    pristine.push({ form: f, html: f.innerHTML });
  });
  function resetForms() {
    pristine.forEach(function (p) {
      if (p.form.innerHTML !== p.html) { p.form.innerHTML = p.html; }
      try { p.form.reset(); } catch (e) {}
    });
  }

  function lock() {
    if (locked) { return; }
    savedY = window.scrollY || window.pageYOffset || 0;
    body.style.top = (-savedY) + 'px';
    docEl.classList.add('cta-locked');
    locked = true;
  }
  function unlock() {
    if (!locked) { return; }
    docEl.classList.remove('cta-locked');
    body.style.top = '';
    window.scrollTo(0, savedY);
    locked = false;
  }
  function sync() {
    var m = modals[location.hash];
    if (m) {
      lock();
      var f = m.querySelector('input:not([type=hidden]):not(.vtd-hp), select, textarea');
      if (f) { try { f.focus({ preventScroll: true }); } catch (e) { f.focus(); } }
    } else {
      unlock();
      resetForms();
    }
  }
  // Carry the clicked teammate card's identity into the request modal, so the
  // lead records exactly who the visitor asked for. Runs before navigation, so
  // the fields/heading are set by the time the :target modal paints.
  function fillRequest(a) {
    var rm = modals['#cta-request'];
    if (!rm || a.getAttribute('href') !== '#cta-request') { return; }
    var vname = a.getAttribute('data-vt-name') || '';
    var idEl = rm.querySelector('input[name="vt_id"]');
    var nmEl = rm.querySelector('input[name="vt_interest"]');
    if (idEl) { idEl.value = a.getAttribute('data-vt-id') || ''; }
    if (nmEl) { nmEl.value = vname; }
    rm.querySelectorAll('[data-vt-name-target]').forEach(function (t) {
      t.textContent = vname || 'this teammate';
    });
  }
  // Capture scroll position before the hash flips so the open is jump-free.
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href^="#cta-"]');
    if (!a || !modals[a.getAttribute('href')]) { return; }
    fillRequest(a);
    lock();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modals[location.hash]) { location.hash = '#cta'; }
  });
  window.addEventListener('hashchange', sync);
  sync(); // honor a deep-link / cross-page #cta-<intent> on load
})();
</script>
