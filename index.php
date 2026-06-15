<?php
$page_title       = 'HIPAA-Certified Medical & Dental Virtual Assistants | Virtual Teammate';
$page_description = 'Hire HIPAA-certified medical & dental virtual assistants from a global talent network. Billing, scribing, scheduling, insurance verification. Save up to 73%.';
$og_title         = 'HIPAA-Certified Medical & Dental Virtual Assistants';
$og_description   = 'Specialized virtual staffing for medical practices, dental clinics & RCM teams — sourced globally, delivered in your time zone.';
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
    <div class="hero-eyebrow reveal"><span class="dot"></span> Backed by the 30-Day Right-Fit Promise &middot; HIPAA-Certified</div>
    <h1 class="hero-h1 reveal d1">Short-Staffed and Overworked?<br><span class="hero-h1-sub">Fully Staff Your Practice <em>in Weeks, Not Months</em> &mdash; for <em>73% Less</em>.</span></h1>
    <p class="hero-sub reveal d2">Published flat-rate pricing from <strong>$975/mo</strong>, backed by our <strong>30-Day Right-Fit Promise</strong> &mdash; HIPAA-certified medical &amp; dental virtual assistants trained on Epic, Cerner, Dentrix and Eaglesoft and matched to your US time zone, ready to own billing, scribing, scheduling and prior auth.</p>
    <p class="hero-guarantee reveal d2"><strong>Not the right fit in month one?</strong> We replace them at no cost &mdash; or refund every billed day.</p>
    <div class="hero-btns reveal d3">
      <a href="#calculator" class="btn-primary">Calculate My Savings <i class="fa-solid fa-calculator"></i></a>
      <a href="#cta-practice-audit" class="btn-glass" data-cta-intent="practice-audit">Book My Practice Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <a href="#cta-buyers-checklist" class="hero-textlink reveal d3" data-cta-intent="buyers-checklist">Or get the HIPAA VA Buyer&rsquo;s Checklist &mdash; just your email <i class="fa-solid fa-arrow-right"></i></a>
  </div>

  <!-- Stats card — sits beside the pitch on the right (2×2), stacks below on mobile -->
  <div class="hero-stats reveal d5">
    <div class="hstat"><div class="hstat-num" data-count="73" data-suffix="%">0%</div><div class="hstat-lbl">Lower Staffing Cost</div></div>
    <div class="hstat"><div class="hstat-num" data-count="95" data-suffix="%">0%</div><div class="hstat-lbl">Clean-Claim Rate</div></div>
    <div class="hstat"><div class="hstat-num hstat-rating">4.9<i class="fa-solid fa-star"></i></div><div class="hstat-lbl"><i class="fa-brands fa-google" aria-hidden="true"></i> Avg Google Rating</div></div>
    <div class="hstat"><div class="hstat-num" data-count="30" data-suffix="-Day">0-Day</div><div class="hstat-lbl">Right-Fit Promise</div></div>
  </div>

  <!-- Trust row — full-width strip spanning the whole hero -->
  <div class="trust-row reveal d4">
    <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Certified</div>
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
  <div class="marquee-lbl"><i class="fa-solid fa-handshake-angle"></i> Trusted By Practices &amp; Businesses Across The U.S.</div>
  <div class="marquee-track-wrap">
    <div class="marquee-track" id="mqTrack"></div>
  </div>
</div>
<script>window.VT_MARQUEE = <?= json_encode($mq_srcs, JSON_UNESCAPED_SLASHES) ?>;</script>

<div class="divider"></div>

<!-- CASE STUDIES — Client KPI Results · Latest Audit (moved directly under the hero marquee) -->
<section class="sec" id="testimonials" aria-labelledby="cs-h">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-chart-column"></i> Proven Client Impact</div>
    <h2 class="sec-h2" id="cs-h">The Results Speak for <em>Themselves.</em></h2>
    <p class="sec-sub">No projections, no spin &mdash; just real numbers from our latest client audit, where Virtual Teammates consistently beat the targets that matter most to your practice.</p>
  </div>
  <div class="case-grid case-grid-4">
    <article class="case-card reveal d1">
      <div class="case-metric">
        <div class="case-metric-h">Insurance Verifications </div>
        <div class="case-metric-row">

          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+30%</span></div>
        </div>
        <div class="case-metric-foot">Payment posting also beat target &mdash; <strong>+48%</strong> of goal</div>
      </div>
      <p class="case-q">Our virtual teammate cleared insurance verifications <strong>30% above goal</strong> and payment posting <strong>48% over target</strong> &mdash; turning a challenging AR into one of the practice&rsquo;s strongest on record.</p>
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
        <div class="case-metric-foot">Claims volume also beat target &mdash; <strong>+47%</strong> of goal</div>
      </div>
      <p class="case-q">A dedicated billing teammate achieved pre-certs <strong>60% over target</strong> and claims volume <strong>47% above plan</strong> &mdash; keeping authorizations ahead of schedule and clean claims moving.</p>
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
        <div class="case-metric-foot">Insurance verifications also beat target &mdash; <strong>+44%</strong> of goal</div>
      </div>
      <p class="case-q">Payment posting landed <strong>+40% over target</strong> and insurance verifications <strong>+44% over</strong> &mdash; streamlining the revenue cycle so claims go out clean and cash comes in faster.</p>
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
        <div class="case-metric-foot">Kept a high-volume surgical schedule billed on time</div>
      </div>
      <p class="case-q">A specialty-billing teammate cleared claims <strong>33% above target</strong> for a busy endodontics &amp; oral-surgery group &mdash; keeping a high-volume surgical schedule billed and out the door on time.</p>
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

<!-- SPECIALTIES -->
<section class="sec" id="specialties">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-stethoscope"></i> Our Specializations</div>
    <h2 class="sec-h2">Healthcare Focused on <em>Targeted Outcomes</em></h2>
    <p class="sec-sub">From day one, our virtual teammates are trained to support and improve clinical operations, cash flow, and patient communication &mdash; so your team can stay focused on care and results.</p>
  </div>

  <div class="spec-grid">
    <article class="spec-card reveal d1">
      <div class="spec-photo">
        <img src="images/photos/medical-section.webp" alt="Bright, modern medical clinic corridor with waiting-area seating" loading="lazy"/>
        <div class="spec-photo-cap"><span class="spec-photo-eyebrow">For Medical Practices</span>Focused on Outcomes.<br>Measured in Results.</div>
        <div class="spec-proof">
          <div class="spec-proof-h"><i class="fa-solid fa-chart-line"></i> Proof in the numbers &mdash; medical practices</div>
          <ul>
            <li><strong>AR days 52 &rarr; 23</strong> &mdash; Family Practice, Austin TX. $68k stalled claims recovered in 12 weeks.</li>
            <li><strong>+18 hrs/week reclaimed</strong> &mdash; Internal Medicine, Denver CO. Scribe ends after-hours charting.</li>
            <li><strong>95%+ clean claim rate</strong> &mdash; average across our specialist-tier medical billers.</li>
          </ul>
        </div>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow med"><span class="dot"></span> HIPAA Certified &middot; Epic / Cerner / Athena Trained</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span>
          <h3 class="spec-title">Medical Virtual Teammates</h3>
        </div>
        <p class="spec-desc">HIPAA-certified medical teammates work inside your EHR to own billing, scribing, prior auth, scheduling and patient calls &mdash; so providers stop charting after hours and your AR keeps moving.</p>

        <div class="spec-pills">
          <a class="pill" href="services/medical-administrative-support/">Medical Admin Support <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-receptionist/">Medical Receptionist <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-biller/">Medical Biller <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-scribe/">Medical Scribe <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-assistant/">Medical Assistant <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <a href="#calculator" class="spec-link">Calculate Medical VA ROI <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </article>

    <article class="spec-card alt reveal d2">
      <div class="spec-photo">
        <img src="images/photos/dental-section.webp" alt="Treatment chair in a clean modern dental clinic" loading="lazy"/>
        <div class="spec-photo-cap"><span class="spec-photo-eyebrow">For Dental Practices</span>Chairs Full.<br>Claims Clean.</div>
        <div class="spec-proof">
          <div class="spec-proof-h"><i class="fa-solid fa-chart-line"></i> Outcomes our dental VAs deliver</div>
          <ul>
            <li><strong>No-shows 22% &rarr; 9%</strong> &mdash; Pediatric Dental, Tampa FL. +14 visits/week recovered from confirmations &amp; rebooks.</li>
            <li><strong>30%+ no-show reduction</strong> &mdash; Phoenix AZ dental practice with virtual receptionist on recall.</li>
            <li><strong>CDT-coded claims with narratives</strong> &mdash; first-pass clean-claim rate above 95%.</li>
          </ul>
        </div>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow med"><span class="dot"></span> HIPAA Certified &middot; Dentrix / Eaglesoft / Open Dental Trained</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-tooth"></i></span>
          <h3 class="spec-title">Dental Virtual Assistants That Protect Clinic Productivity</h3>
        </div>
        <p class="spec-desc">Keep chairs full, claims clean, and your team focused on patients with virtual teammates fluent in dental EMRs.</p>

        <div class="spec-pills">
          <a class="pill" href="services/dental-admin/">Dental Admin Support <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-receptionist/">Dental Receptionist <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-biller/">Dental Biller <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-scribe/">Dental Scribe <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-coordinator/">Dental Coordinator <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <a href="#calculator" class="spec-link">Calculate Dental VA ROI <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </article>
  </div>

  <!-- HIPAA STRIP -->
  <div class="hipaa reveal">
    <div class="hbadges">
      <div class="hbadge">
        <span class="ico-circle"><i class="fa-solid fa-shield-halved"></i></span>
        <div class="hbadge-txt"><strong>HIPAA Certified</strong><span>Every healthcare VA</span></div>
      </div>
      <div class="hbadge">
        <span class="ico-circle"><i class="fa-solid fa-brain"></i></span>
        <div class="hbadge-txt"><strong>EHR Trained</strong><span>Epic, Cerner, Dentrix &amp; more</span></div>
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
    <a href="#cta-practice-audit" class="btn-gold" data-cta-intent="practice-audit">Book My Practice Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<div class="divider"></div>

<!-- PROCESS -->
<section class="sec">
  <div style="text-align:center;max-width:600px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> Our Process</div>
    <h2 class="sec-h2">Hire in Days, <em>Not Months</em></h2>
    <p class="sec-sub" style="margin:0 auto;">A lightning-fast 3-step process built for busy doctors, dentists, and practice managers.</p>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1">
      <div class="pstep-head">
        <div class="pstep-num">01</div>
        <i class="fa-solid fa-calendar-check pstep-ico"></i>
      </div>
      <div class="pstep-eta"><i class="fa-solid fa-clock"></i> Within 24 hours</div>
      <h3 class="pstep-title">Book a Practice Staffing Audit</h3>
      <p class="pstep-desc">Submit the form and we&rsquo;ll confirm your audit slot <strong>within one business day</strong>. The 20-minute diagnostic call maps your practice, workflows, and the exact clinical or admin support to delegate first.</p>
    </div>
    <div class="pstep reveal d2">
      <div class="pstep-head">
        <div class="pstep-num">02</div>
        <i class="fa-solid fa-users-viewfinder pstep-ico"></i>
      </div>
      <div class="pstep-eta"><i class="fa-solid fa-clock"></i> 5&ndash;7 business days</div>
      <h3 class="pstep-title">Meet &amp; Interview Candidates</h3>
      <p class="pstep-desc">Curated shortlist of HIPAA-certified VAs delivered in <strong>5&ndash;7 business days</strong> &mdash; matched to your specialty, EHR, accent and time-zone preferences. You interview, we coordinate, you choose the fit.</p>
    </div>
    <div class="pstep reveal d3">
      <div class="pstep-head">
        <div class="pstep-num">03</div>
        <i class="fa-solid fa-rocket pstep-ico"></i>
      </div>
      <div class="pstep-eta"><i class="fa-solid fa-clock"></i> Live in 1&ndash;2 weeks</div>
      <h3 class="pstep-title">Launch &amp; Onboard Seamlessly</h3>
      <p class="pstep-desc">Agreement, billing, EHR access and SOP handoff all wrapped in <strong>1&ndash;2 weeks</strong>. Your VA hits the ground running with a dedicated Client Success Manager (CSM) and the 30-Day Right-Fit Promise behind every placement.</p>
    </div>
  </div>
  <div class="proc-cta reveal">
    <a href="#cta-practice-audit" class="btn-primary" data-cta-intent="practice-audit">Book My Practice Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<div class="divider"></div>

<!-- ROI CALCULATOR -->
<section class="calc-wrap" id="calculator" aria-labelledby="calc-h">
  <div class="calc-head reveal">
    <div class="calc-badge"><i class="fa-solid fa-calculator"></i> Live ROI Calculator</div>
    <h2 class="sec-h2" id="calc-h">See Your Annual Savings <em>&mdash; In Real Time</em></h2>
    <p class="sec-sub">Measured against an equivalent US in-house hire, a Virtual Teammate returns a multiple of what you put in &mdash; and that gap is value added straight back into your practice.</p>
  </div>

  <div class="calc-glow">
  <div class="calc reveal d1" id="roiCalc">
    <!-- Live calculator (single column) -->
    <div class="calc-main">
    <!-- Slider on top — drag to set team size -->
    <div class="calc-top">
      <div class="calc-top-head">
        <h3>Estimate Your Savings</h3>
        <p class="calc-cap">Drag the slider to set your team size &mdash; your numbers update instantly.</p>
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

    <div class="calc-results">
      <div class="calc-results-top">
        <div class="calc-hero-num">
          <div class="calc-hero-lbl">Estimated Annual Savings</div>
          <div class="calc-hero-val" id="calcAnnual">$0</div>
          <div class="calc-hero-sub"><span id="calcMonthly">$0</span> per month saved</div>
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
            <circle class="gauge-fg" id="calcGaugeFg" cx="100" cy="100" r="84" stroke-dasharray="0 528"/>
          </svg>
          <div class="calc-gauge-center">
            <div class="calc-gauge-pct" id="calcPct">0%</div>
            <div class="calc-gauge-lbl">You Save</div>
          </div>
        </div>
      </div>

      <div class="calc-bars">
        <div class="calc-bar-row">
          <div class="calc-bar-head">
            <span class="calc-bar-name us"><span class="swatch"></span>US In-House Cost</span>
            <span class="calc-bar-amt us" id="calcUsAmt">$0 / yr</span>
          </div>
          <div class="calc-bar-track"><div class="calc-bar-fill us" id="calcUsBar" style="width:0%;"></div></div>
        </div>
        <div class="calc-bar-row">
          <div class="calc-bar-head">
            <span class="calc-bar-name vt"><span class="swatch"></span>Virtual Teammate Cost</span>
            <span class="calc-bar-amt vt" id="calcVtAmt">$0 / yr</span>
          </div>
          <div class="calc-bar-track"><div class="calc-bar-fill vt" id="calcVtBar" style="width:0%;"></div></div>
        </div>
      </div>

      <details class="calc-more">
        <summary class="calc-more-sum"><span><i class="fa-solid fa-circle-info"></i> See the full breakdown</span><i class="fa-solid fa-chevron-down calc-more-chev"></i></summary>
        <div class="calc-kpis">
          <div class="calc-kpi">
            <div class="calc-kpi-lbl"><i class="fa-solid fa-chart-line"></i> 3-Year Value</div>
            <div class="calc-kpi-val" id="calc3yr">$0</div>
          </div>
          <div class="calc-kpi">
            <div class="calc-kpi-lbl"><i class="fa-solid fa-user-tie"></i> Per-Teammate / Year</div>
            <div class="calc-kpi-val" id="calcPerVa">$0</div>
          </div>
          <div class="calc-kpi">
            <div class="calc-kpi-lbl"><i class="fa-solid fa-bolt"></i> Payback Period</div>
            <div class="calc-kpi-val" id="calcPayback">&lt; 1<span class="unit">mo</span></div>
          </div>
        </div>
      </details>

      <div class="calc-rate-band" aria-label="Published VT rate">
        <div class="calc-rate-h"><i class="fa-solid fa-tag"></i> Published Rate &mdash; No Quote Required</div>
        <div class="calc-rate-single">
          <div class="calc-rate-amt">$1,625<span>/mo</span></div>
          <div class="calc-rate-sub">Full-time, flat rate &middot; from $975/mo part-time</div>
        </div>
        <div class="calc-rate-foot">All-in. No payroll tax, benefits, recruiter fees or PTO billed on top.</div>
      </div>

      <p class="calc-foot">Rates based on live VT placement data. US comparison uses median fully-loaded in-house cost (salary + benefits + payroll burden) for equivalent healthcare admin roles.</p>
    </div>
    </div>
  </div>
  </div>
</section>

<!-- (GLOBAL section moved below — see after the Differentiators block) -->

<!-- IN THE NEWS & PRESS RELEASES (logo marquee, toned-down → full color on hover) -->
<div class="news" aria-label="Virtual Teammate in the news and press releases">
  <div class="news-lbl"><i class="fa-solid fa-newspaper"></i> In The News &amp; Press Releases</div>
  <div class="news-track-wrap">
    <div class="news-track" id="newsTrack"></div>
  </div>
</div>

<div class="divider"></div>

<?php $homepage_profiles = vtnew_homepage_profiles(8); ?>
<!-- PROFILES (live from the VT portal — medical + dental only) -->
<section class="sec" id="profiles">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;" class="reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-id-badge"></i> Meet the Team</div>
      <h2 class="sec-h2" style="margin-bottom:0;">Your Future Healthcare Teammates</h2>
    </div>
    <a href="#cta-practice-audit" data-cta-intent="practice-audit" class="btn-primary" style="font-size:15px;padding:14px 28px;">Book My Practice Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
  </div>

  <?php if (!empty($homepage_profiles)): ?>
    <div class="prof-grid">
      <?php foreach ($homepage_profiles as $i => $p):
        $name = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
        $role = $p['role_title'] ?: ($p['department'] ?: 'Healthcare VA');
        $years = (int) $p['experience_years'];
        $ehr   = trim((string) $p['ehr_software']);
        $delay = 'd' . (($i % 4) + 1);
        $photoSrc = !empty($p['_thumb']) ? $p['_thumb'] : ('talent-photo.php?id=' . (int) $p['id'] . '&thumb=1');
      ?>
        <a class="prof-card reveal <?= htmlspecialchars($delay) ?>" href="#cta-request-teammate" data-cta-intent="request-teammate" data-teammate="<?= htmlspecialchars($name, ENT_QUOTES) ?>" data-role="<?= htmlspecialchars($role, ENT_QUOTES) ?>" aria-label="Request a teammate like <?= htmlspecialchars($name, ENT_QUOTES) ?>">
          <div class="prof-photo"><img src="<?= htmlspecialchars($photoSrc, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($name, ENT_QUOTES) ?>" decoding="async"/></div>
          <div class="prof-name"><?= htmlspecialchars($name) ?></div>
          <div class="prof-role"><?= htmlspecialchars($role) ?></div>
          <?php if (!empty($p['department']) && $p['department'] !== $role): ?><div class="prof-dept"><?= htmlspecialchars($p['department']) ?></div><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="prof-empty card">
      <i class="fa-solid fa-user-doctor" style="color:var(--gold);font-size:24px;margin-bottom:10px;"></i>
      <p>Our medical &amp; dental bench is reviewed and matched manually for every engagement. <a href="#cta-practice-audit" data-cta-intent="practice-audit">Book My Practice Staffing Audit</a> to see candidates tailored to your specialty, EHR and time-zone preferences.</p>
    </div>
  <?php endif; ?>
</section>

<div class="divider"></div>

<!-- GUARANTEE -->
<section class="sec guarantee" id="guarantee" aria-labelledby="g-h">
  <div class="g-wrap reveal">
    <div class="g-copy">
      <div class="sec-lbl"><i class="fa-solid fa-shield-halved"></i> The VT 30-Day Right-Fit Promise</div>
      <h2 class="sec-h2" id="g-h">If It&rsquo;s Not Working in Month One, <em>We Make It Right</em></h2>
      <div class="g-cards">
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-arrows-rotate"></i></span>
          <h3>No-Cost Replacement</h3>
          <p>Decide a VA isn&rsquo;t the right fit inside the first 30 days? Tell us why. We deliver a curated re-shortlist within <strong>5 business days</strong>, onboard the new VA at no charge, and <strong>pause your billing</strong> until the replacement is live and producing.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-rotate-left"></i></span>
          <h3>30-Day Money-Back Window</h3>
          <p>Not sure outsourcing fits your practice? Cancel any time in the first 30 days &mdash; we refund every billed day in full. <strong>No clawbacks, no termination fees, no minimum-term lock-in.</strong> A staffing partner, not a contract trap.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
          <h3>Backup Coverage Built In</h3>
          <p>Sick day, PTO, family emergency? Your Client Success Manager (CSM) arranges a <strong>trained backup VA within hours</strong>, at no extra cost, briefed on your workflows and EHR access. Coverage ships with every placement &mdash; it&rsquo;s not an upsell.</p>
        </div>
      </div>

      <div class="g-audit">
        <div class="g-audit-h"><i class="fa-solid fa-clipboard-check"></i> What the Practice Staffing Audit covers</div>
        <ul>
          <li><strong>Workflow inventory.</strong> We map the 8&ndash;12 admin and clinical workflows that drain the most provider time in your practice &mdash; intake, charts, refills, billing, scheduling, recall, prior auth.</li>
          <li><strong>Outsourcing priority list.</strong> You leave the call with a ranked list of what to delegate <em>first</em> for fastest ROI, and what to keep in-house.</li>
          <li><strong>Tier &amp; headcount recommendation.</strong> Specific call on Pro vs Specialist tier, full-time vs part-time, and how many VAs to start with for your specialty and patient volume.</li>
          <li><strong>Honest no-fit answer.</strong> If outsourcing isn&rsquo;t right for your practice, we&rsquo;ll tell you on the call. No follow-up sales sequence.</li>
        </ul>
      </div>

      <div class="g-foot">
        <a href="#cta-practice-audit" data-cta-intent="practice-audit" class="btn-glass">Book My Practice Staffing Audit <i class="fa-solid fa-clipboard-check"></i></a>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- SECURITY & COMPLIANCE — every claim translated into plain language: what the
     safeguard is, and what it actually means for the practice. -->
<section class="sec comp-section" id="security" aria-labelledby="comp-h">
  <div style="text-align:center;max-width:680px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-lock"></i> Security &amp; Compliance</div>
    <h2 class="sec-h2">Patient Data, <em>Protected by Design</em></h2>
    <p class="sec-sub" style="margin:0 auto;">Compliance here isn&rsquo;t a checkbox &mdash; here&rsquo;s what each safeguard is, and what it actually means for your practice, in plain English.</p>
  </div>
  <div class="comp-grid">
    <div class="comp-card reveal d1">
      <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
      <h3>HIPAA Training &amp; Certification</h3>
      <p class="comp-what"><strong>What it is:</strong> every healthcare and dental VA completes formal HIPAA training and certification <em>before</em> they touch patient data.</p>
      <p class="comp-means"><strong>What it means for you:</strong> the person handling your charts knows exactly what counts as protected health information &mdash; and the rules for keeping it private &mdash; held to the same standard as your in-house staff.</p>
    </div>
    <div class="comp-card reveal d2">
      <span class="ico-circle lg"><i class="fa-solid fa-file-signature"></i></span>
      <h3>BAA-Compatible Agreement</h3>
      <p class="comp-what"><strong>What it is:</strong> a Business Associate Agreement (BAA) is the contract HIPAA requires whenever an outside party handles patient data for you. Every VT VA signs a BAA-compatible confidentiality agreement.</p>
      <p class="comp-means"><strong>What it means for you:</strong> our data-handling duties are in writing and legally binding &mdash; not a verbal promise you have to take on trust.</p>
    </div>
    <div class="comp-card reveal d3">
      <span class="ico-circle lg"><i class="fa-solid fa-building-shield"></i></span>
      <h3>SOC 2 Type 2-Aligned Controls</h3>
      <p class="comp-what"><strong>What it is:</strong> SOC 2 is the security framework IT and software vendors are measured against. We run aligned controls on the VT infrastructure that touches your data.</p>
      <p class="comp-means"><strong>What it means for you:</strong> the laptops, logins and networks your VA works from are locked down to a recognized industry standard &mdash; not left to whatever each person happens to have at home.</p>
    </div>
    <div class="comp-card reveal d1">
      <span class="ico-circle lg"><i class="fa-solid fa-clipboard-list"></i></span>
      <h3>Full Audit Trail</h3>
      <p class="comp-what"><strong>What it is:</strong> every time a VA opens, views or changes something in your systems it&rsquo;s logged &mdash; who, what, when, from where &mdash; and retained for 12 months.</p>
      <p class="comp-means"><strong>What it means for you:</strong> if you ever need to know exactly who accessed a record and when, there&rsquo;s a reviewable paper trail, available from your CSM on request.</p>
    </div>
    <div class="comp-card reveal d2">
      <span class="ico-circle lg"><i class="fa-solid fa-laptop-medical"></i></span>
      <h3>Device &amp; Access Security</h3>
      <p class="comp-what"><strong>What it is:</strong> encrypted laptops, hardware multi-factor authentication, a mandatory password manager, controlled network access and least-privilege EHR permissions.</p>
      <p class="comp-means"><strong>What it means for you:</strong> your VA can reach only the systems they need, only from a secured device &mdash; so a lost laptop or stolen password can&rsquo;t expose your records.</p>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- GLOBAL COVERAGE (client-benefit framing — single map, one anchor per continent) -->
<section class="global global-compact" id="global" aria-labelledby="global-h">
  <div class="global-grid">
    <div class="global-l reveal">
      <div class="sec-lbl"><i class="fa-solid fa-earth-americas"></i> Global Reach &middot; Your Staffing Problem, Solved</div>
      <h2 class="sec-h2" id="global-h">Short-Staffed No More &mdash; A <em>Global Bench</em> of HIPAA-Certified Medical &amp; Dental Talent, On Your Time Zone</h2>
      <p class="sec-sub">Short-staffed and tired of the hiring grind? We fill your exact gap fast &mdash; vetted VAs matched to your specialty, software and US hours, fully managed by your dedicated CSM.</p>

      <ul class="global-benefits">
        <li><span class="ico-circle"><i class="fa-solid fa-bolt"></i></span><span><strong>Fully staffed in weeks, not months.</strong> Stop carrying open seats &mdash; a curated shortlist lands in 5&ndash;7 business days and your VA is live in 1&ndash;2 weeks.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-clock"></i></span><span><strong>Covered in your US time zone.</strong> Morning, afternoon, evening or overnight &mdash; your VA works your hours, not a call-center queue.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-shield-halved"></i></span><span><strong>Never short-handed again.</strong> Sick day, PTO or turnover? Trained backup coverage steps in within hours, at no extra cost &mdash; the work never goes dark.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-check"></i></span><span><strong>Best-fit talent, not least-cost talent.</strong> A bigger bench lets us recruit for your specialty, EHR, language and accent &mdash; not just whoever&rsquo;s available.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-sack-dollar"></i></span><span><strong>Keep up to 73% of your staffing budget</strong> to reinvest in patient care and growth. One flat, all-in rate covers everything &mdash; no benefits, payroll tax, recruiter fees or PTO to manage.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-tie"></i></span><span><strong>One US point of contact from day one.</strong> Your named CSM owns onboarding, quality, backups and quarterly reviews &mdash; it&rsquo;s how we hold 95%+ client retention.</span></li>
      </ul>
    </div>

    <div class="reveal d2">
      <div class="world" aria-label="Virtual Teammate global coverage — one anchor per continent">
        <div class="world-grid"></div>
        <img class="world-map-img" src="images/world-map.svg" alt="World map showing Virtual Teammate global talent coverage" loading="lazy">
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
                    p.ehr_software, p.experience_years, p.status
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
$homepage_profiles = vtnew_homepage_profiles(8);
?>
<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div>
    <h2 class="sec-h2">Frequently Asked Questions</h2>
  </div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a medical or dental VA cost?</div><div class="faq-a">Published flat-rate pricing, no quote required. <strong>Pro tier starts at $1,625/mo for a full-time VA</strong> ($867/mo part-time). <strong>Specialist tier</strong> (medical billing, scribing, advanced coding, dental billing) starts at <strong>$2,167/mo full-time</strong> ($1,300/mo part-time). All-in flat rate &mdash; no benefits, payroll tax, recruiter fees or PTO billed on top. Typically 60&ndash;73% less than an equivalent US in-house hire &mdash; use the calculator above for your exact savings.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-rotate"></i> What happens if my VA isn&rsquo;t the right fit?</div><div class="faq-a">The VT 30-Day Right-Fit Promise covers two scenarios: <strong>(1)</strong> the VA isn&rsquo;t the right fit &rarr; we replace them at no cost with a re-shortlist inside 5 business days; <strong>(2)</strong> outsourcing isn&rsquo;t working for your practice &rarr; cancel within the first 30 days and we refund every billed day, no clawback. The guarantee is published in writing, not hidden in a sales call.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> What security &amp; compliance do you carry?</div><div class="faq-a">Five layers: <strong>HIPAA training &amp; certification</strong> for every VA, a <strong>BAA-compatible</strong> confidentiality agreement, <strong>SOC 2 Type 2-aligned controls</strong> on the infrastructure that touches your data, a 12-month <strong>audit trail</strong> of every access event, and locked-down <strong>device &amp; access security</strong> (encrypted laptops, hardware MFA, least-privilege EHR access). Our <a href="#security">Security &amp; Compliance section</a> spells out exactly what each one means for your practice.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-bolt"></i> How quickly can I get a VA started?</div><div class="faq-a">Curated shortlist in <strong>5&ndash;7 business days</strong>. After you pick your VA, onboarding wraps in <strong>1&ndash;2 weeks</strong> — agreement, EHR access, SOP handoff, shadow week, then live work. Your dedicated Client Success Manager (CSM) runs the timeline so it lands when you need it.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-brain"></i> What EHR and dental software do your VAs know?</div><div class="faq-a">Medical VAs are trained on Epic, Cerner, eClinicalWorks, Athenahealth, NextGen, Practice Fusion and Kareo. Dental VAs are proficient in Dentrix, Dentrix Ascend, Eaglesoft, Open Dental and Carestream. Plus all major clearinghouses (Availity, Office Ally, Waystar, DentalXChange).</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your VAs based?</div><div class="faq-a">Wherever the best fit lives. Our global recruiting reach lets us match for your specialty, EHR, accent, language, and US time-zone shift &mdash; not just whoever happens to be on the bench. You hire for skill set; we handle the sourcing.</div></div>
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> Will I have a dedicated point of contact?</div><div class="faq-a">Yes. From day one, a named Client Success Manager (CSM) is on your account &mdash; reachable on email, Slack/Teams, and a direct line during your business hours. They own performance, backup coverage and quarterly check-ins so you&rsquo;re never managing the placement alone.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-handshake-slash"></i> Will I be locked into a long-term contract?</div><div class="faq-a">No. Most healthcare VA agencies require 6&ndash;12 month commitments &mdash; we don&rsquo;t. Pause, scale up, scale down or cancel month-to-month, with no early-termination fees and no recapture clauses. The 30-Day Right-Fit Promise covers your first month on top of that.</div></div>
  </div>
</section>

<!-- CTA FORM -->
<section class="sec cta-stages-section" id="cta" style="padding-top:80px;padding-bottom:110px;">
  <div class="cta-stages-h reveal">
    <div class="sec-lbl"><i class="fa-solid fa-paper-plane"></i> Three Ways to Start</div>
    <h2 class="cta-h2" style="font-size:36px;">Pick the Entry Point<br>That Fits Where You Are</h2>
    <p class="cta-sub">Top of funnel, mid-funnel, or ready to scope &mdash; same team, three different first steps. Same color, same SLA, same Client Success Manager (CSM) waiting on the other side.</p>
  </div>

  <div class="cta-stages-grid reveal d1">
    <article class="cta-stage" data-cta-intent="buyers-checklist">
      <div class="cta-stage-tag">Just exploring</div>
      <span class="ico-circle lg"><i class="fa-solid fa-file-lines"></i></span>
      <h3>HIPAA VA Buyer&rsquo;s Checklist</h3>
      <p class="cta-stage-lead">Key questions to ask any healthcare VA agency before you sign. Drop your email &mdash; we&rsquo;ll send the PDF.</p>
      <ul class="cta-stage-list">
        <li>Compliance, BAA, audit-trail questions</li>
        <li>Pricing-model traps to watch for</li>
        <li>Performance &amp; quality SLAs to demand</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-buyers-checklist" data-cta-intent="buyers-checklist">Get the HIPAA VA Buyer&rsquo;s Checklist <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-mid" data-cta-intent="practice-audit">
      <div class="cta-stage-tag">Ready to diagnose</div>
      <span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span>
      <h3>20-min Practice Staffing Audit</h3>
      <p class="cta-stage-lead">Diagnostic-only call. We map your top admin and clinical workflows and tell you what to delegate first.</p>
      <ul class="cta-stage-list">
        <li>Workflow inventory (8&ndash;12 mapped)</li>
        <li>Ranked outsourcing-priority list</li>
        <li>Tier + headcount recommendation</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-practice-audit" data-cta-intent="practice-audit">Book My Practice Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-high" data-cta-intent="strategy-call">
      <div class="cta-stage-tag">Ready to talk</div>
      <span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span>
      <h3>Strategy Call &amp; Jumpstart</h3>
      <p class="cta-stage-lead">30-min call. Map your needs, define the role, get a curated shortlist in 5&ndash;7 business days.</p>
      <ul class="cta-stage-list">
        <li>Role scope + EHR / specialty match</li>
        <li>Tailored candidate shortlist</li>
        <li>Onboarding plan + CSM intro</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-strategy-call" data-cta-intent="strategy-call">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
    </article>
  </div>

  <p class="cta-stages-foot reveal">Prefer to start with a diagnostic? <a href="#cta-practice-audit" data-cta-intent="practice-audit">Book My Practice Staffing Audit</a> and a Client Success Manager (CSM) will map it out with you.</p>
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
    <h2 class="cta-modal-h" id="ccm-bc-h">Grab the HIPAA VA Buyer&rsquo;s Checklist</h2>
    <p class="cta-modal-sub">Unlock new levels of productivity and patient care. Enter your email to receive our HIPAA VA Buyer&rsquo;s Checklist and learn how to choose the right virtual staffing partner for long-term success.</p>
    <form class="cta-modal-form" id="ctaChecklistForm" method="post" action="<?= $home_base ?>lead.php"
          data-lead-thanks="Check your inbox &mdash; your checklist is on the way.">
      <input type="hidden" name="intent" value="buyers-checklist">
      <input type="hidden" name="form" value="homepage-checklist">
      <input type="hidden" name="source" value="HIPAA VA Buyer&rsquo;s Checklist">
      <div class="cf-row" style="grid-template-columns:1fr;margin-bottom:16px;">
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Send Me the Checklist <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>No spam &middot; Just the checklist and the occasional helpful tip</div>
    </form>
  </div>
</div>

<div class="cta-modal" id="cta-practice-audit" role="dialog" aria-modal="true" aria-labelledby="ccm-pa-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-clipboard-check"></i> 20-Min Practice Staffing Audit</div>
    <h2 class="cta-modal-h" id="ccm-pa-h">Book My Practice Staffing Audit</h2>
    <p class="cta-modal-sub">Pick a time that works for you &mdash; a US-based Client Success Manager will map your busiest workflows and show you which roles to delegate first. Diagnostic only, no obligation.</p>
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
    <h2 class="cta-modal-h" id="ccm-sc-h">Book Your Strategy Call &amp; Jumpstart</h2>
    <p class="cta-modal-sub">Pick a time below and we&rsquo;ll scope your needs, define the role, and map your first 30 days &mdash; so your teammate is productive fast. No commitment, covered by the 30-Day Right-Fit Promise.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
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

<!-- Candidate-request modal — opened by the teammate profile cards. The clicked
     card's name (data-teammate) is injected into the heading + the hidden
     vt_interest field by the modal script, so the lead records who they asked
     for. Works with JS off too (opens via #cta-request-teammate); the name just
     stays generic. -->
<div class="cta-modal" id="cta-request-teammate" role="dialog" aria-modal="true" aria-labelledby="ccm-rt-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-id-badge"></i> Request this teammate</div>
    <h2 class="cta-modal-h" id="ccm-rt-h">Request a Teammate <span data-teammate-name></span></h2>
    <p class="cta-modal-sub">Drop your name and work email and your Client Success Manager will check this teammate&rsquo;s availability &mdash; or line up the closest match on our bench, matched to your specialty, EHR and time zone.</p>
    <form class="cta-modal-form" id="ctaRequestForm" method="post" action="<?= $home_base ?>lead.php"
          data-lead-thanks="Thanks! Your Client Success Manager will be in touch within one business day about this teammate.">
      <input type="hidden" name="intent" value="request-teammate">
      <input type="hidden" name="form" value="homepage-request-teammate">
      <input type="hidden" name="source" value="Request a Teammate">
      <input type="hidden" name="vt_interest" data-requested-teammate value="">
      <div class="cf-row" style="margin-bottom:16px;">
        <input class="cf-field" name="first_name" placeholder="First Name" required>
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Request This Teammate <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>No commitment &middot; We respond within 1 business day &middot; Covered by the 30-Day Right-Fit Promise</div>
    </form>
  </div>
</div>

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
  // Capture scroll position before the hash flips so the open is jump-free.
  // For the candidate-request modal, also inject the clicked teammate's name
  // (from data-teammate) into the heading + the hidden vt_interest field so the
  // lead records exactly who was requested.
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href^="#cta-"]');
    if (!a || !modals[a.getAttribute('href')]) { return; }
    var team = a.getAttribute('data-teammate');
    if (team) {
      var m = modals['#cta-request-teammate'];
      if (m) {
        var nameEl = m.querySelector('[data-teammate-name]');
        if (nameEl) { nameEl.textContent = 'Like ' + team; }
        var hidden = m.querySelector('[data-requested-teammate]');
        if (hidden) {
          var role = a.getAttribute('data-role');
          hidden.value = team + (role ? ' (' + role + ')' : '');
        }
      }
    }
    lock();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modals[location.hash]) { location.hash = '#cta'; }
  });
  window.addEventListener('hashchange', sync);
  sync(); // honor a deep-link / cross-page #cta-<intent> on load
})();
</script>
