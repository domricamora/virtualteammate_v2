<?php
$page_title       = 'HIPAA-Certified Medical & Dental Virtual Assistants | Virtual Teammate';
$page_description = 'Hire HIPAA-certified medical & dental virtual assistants from a global talent network. Billing, scribing, scheduling, insurance verification. Save up to 78%.';
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

  <div class="hero-inner">
    <div class="hero-eyebrow reveal"><span class="dot"></span> Backed by the 30-Day Right-Fit Promise &middot; HIPAA-Certified</div>
    <h1 class="hero-h1 reveal d1">Reclaim <em>15 Hours</em> a Week.<br>Cut Staffing Costs <em>by 78%</em>.</h1>
    <p class="hero-sub reveal d2">Plug a HIPAA-certified medical or dental virtual assistant into your practice in <strong>14 days</strong> &mdash; from <strong>$1,625/mo full-time</strong>, trained on Epic, Cerner, Dentrix and Eaglesoft, matched to your US time zone. <strong>Not the right fit in month one?</strong> We replace them at no cost &mdash; or refund every billed day.</p>
    <div class="hero-btns reveal d3">
      <a href="#cta" class="btn-primary" data-cta-intent="strategy-call">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#calculator" class="btn-glass">Calculate My Savings <i class="fa-solid fa-calculator"></i></a>
      <a href="#cta" class="btn-soft-link" data-cta-intent="buyers-checklist">Just exploring? Grab the HIPAA VA Buyer&rsquo;s Checklist <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="trust-row reveal d4">
      <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Certified</div>
      <div class="trust-item"><i class="fa-solid fa-brain"></i> EHR &amp; Dental Software Trained</div>
      <div class="trust-item"><i class="fa-solid fa-user-shield"></i> Background Checked</div>
      <div class="trust-item"><i class="fa-solid fa-rotate"></i> 30-Day Right-Fit Promise</div>
      <div class="trust-item"><i class="fa-solid fa-globe"></i> Your Time Zone</div>
    </div>
    <div class="hero-stats reveal d5">
      <div class="hstat"><div class="hstat-num" data-count="78" data-suffix="%">0%</div><div class="hstat-lbl">Cost Savings</div></div>
      <div class="hstat"><div class="hstat-num" data-count="14" data-suffix="d">0d</div><div class="hstat-lbl">Avg. Time to Live</div></div>
      <div class="hstat"><div class="hstat-num" data-count="12" data-suffix="+">0+</div><div class="hstat-lbl">Countries Sourced</div></div>
      <div class="hstat"><div class="hstat-num" data-count="95" data-suffix="%">0%</div><div class="hstat-lbl">Client Retention</div></div>
    </div>
  </div>

  <!-- HERO VISUAL: photo collage with floating chips -->
  <div class="hero-visual reveal d2" aria-hidden="true">
    <div class="hv-chip t1"><i class="fa-solid fa-circle-check"></i> HIPAA Certified</div>
    <div class="hv-chip t2"><i class="fa-solid fa-earth-americas"></i> Global Bench</div>
    <div class="hv-chip t3"><i class="fa-solid fa-clock"></i> Your Time Zone</div>
    <div class="hv-card hv-main">
      <img src="images/photos/hero-medical-va.png" alt="Medical virtual assistant in headset working on computer" loading="eager" fetchpriority="high"/>
    </div>
    <div class="hv-card hv-side">
      <img src="images/photos/hero-dental-va.png" alt="Dental virtual assistant at reception desk working on computer" loading="eager"/>
    </div>
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

<!-- ROI CALCULATOR -->
<section class="calc-wrap" id="calculator" aria-labelledby="calc-h">
  <div class="calc-head reveal">
    <div class="calc-head-l">
      <div class="calc-badge"><i class="fa-solid fa-calculator"></i> Live ROI Calculator</div>
      <h2 class="sec-h2" id="calc-h">See Your Annual Savings <em>&mdash; In Real Time</em></h2>
      <p class="sec-sub">Built on monthly placement data from 200+ live healthcare clients. Adjust your role and team size below to see what a Virtual Teammate replaces against an equivalent US in-house hire.</p>
    </div>
  </div>

  <div class="calc reveal d1" id="roiCalc">
    <div class="calc-controls">
      <h3>Build Your Team</h3>
      <p class="calc-cap">Pick a role, tier and schedule. Numbers update instantly.</p>

      <div class="calc-field">
        <label class="calc-label" for="calcRole">Role</label>
        <select class="calc-select" id="calcRole" aria-label="Role">
          <optgroup label="Medical">
            <option value="med_admin"          data-tier="pro">Medical Admin</option>
            <option value="med_receptionist"   data-tier="pro">Medical Receptionist</option>
            <option value="med_scheduling"     data-tier="pro">Medical Scheduling Coordinator</option>
            <option value="med_referral"       data-tier="pro">Healthcare Referral Coordinator</option>
            <option value="med_insurance"      data-tier="specialist">Insurance Verification &amp; Pre-Cert</option>
            <option value="med_biller"         data-tier="specialist" selected>Medical Biller</option>
            <option value="med_scribe"         data-tier="specialist">Medical Scribe</option>
            <option value="med_telemedicine"   data-tier="specialist">Telemedicine Services Assistant</option>
          </optgroup>
          <optgroup label="Dental">
            <option value="dental_admin"        data-tier="pro">Dental Admin</option>
            <option value="dental_recall"       data-tier="pro">Dental Patient Recall</option>
            <option value="dental_referral"     data-tier="pro">Dental Referral Coordinator</option>
            <option value="dental_biller"       data-tier="specialist">Dental Biller</option>
            <option value="dental_scribe"       data-tier="specialist">Dental Scribe</option>
            <option value="dental_billing_spec" data-tier="specialist">Dental Billing Specialist</option>
            <option value="dental_insurance"    data-tier="specialist">Dental Insurance Coordinator</option>
          </optgroup>
        </select>
      </div>

      <div class="calc-field">
        <label class="calc-label">Tier</label>
        <div class="calc-seg" role="tablist" aria-label="Tier">
          <button type="button" class="on" data-tier="pro">Pro</button>
          <button type="button" data-tier="specialist">Specialist</button>
        </div>
      </div>

      <div class="calc-field">
        <label class="calc-label">Schedule</label>
        <div class="calc-seg" role="tablist" aria-label="Schedule">
          <button type="button" class="on" data-sched="ft">Full-Time</button>
          <button type="button" data-sched="pt">Part-Time</button>
        </div>
      </div>

      <div class="calc-field" style="margin-bottom:10px;">
        <label class="calc-label" for="calcCount">Number of Virtual Teammates</label>
        <div class="calc-slider-wrap">
          <div class="calc-slider-row">
            <div class="calc-slider-val" id="calcCountVal">2</div>
            <div class="calc-slider-cap">Teammates</div>
          </div>
          <input id="calcCount" class="calc-slider" type="range" min="1" max="25" value="2" step="1" aria-label="Number of Virtual Teammates"/>
          <div class="calc-ticks"><span>1</span><span>5</span><span>10</span><span>15</span><span>20</span><span>25</span></div>
        </div>
      </div>

      <p class="calc-foot">Rates based on live VT placement data. US comparison uses median fully-loaded in-house cost (salary + benefits + payroll burden) for equivalent healthcare admin roles.</p>
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

      <div class="calc-rate-band" aria-label="Published VT rates">
        <div class="calc-rate-h"><i class="fa-solid fa-tag"></i> Published VT Rates &mdash; No Quote Required</div>
        <div class="calc-rate-row">
          <div class="calc-rate-tier">
            <div class="calc-rate-tier-name">Pro Tier</div>
            <div class="calc-rate-tier-amt">$1,625<span>/mo</span></div>
            <div class="calc-rate-tier-sub">Full-time &middot; from $867/mo part-time</div>
          </div>
          <div class="calc-rate-tier specialist">
            <div class="calc-rate-tier-name">Specialist Tier</div>
            <div class="calc-rate-tier-amt">$2,167<span>/mo</span></div>
            <div class="calc-rate-tier-sub">Full-time &middot; from $1,300/mo part-time</div>
          </div>
        </div>
        <div class="calc-rate-foot">Flat rate, all-in. No payroll tax, benefits, recruiter fees or PTO billed on top.</div>
      </div>

      <div class="calc-cta">
        <div class="calc-cta-l">Ready to capture <strong id="calcCtaAmt">these savings</strong>?<br>Book a strategy call or drop your details and we&rsquo;ll reach out.</div>
        <div class="calc-cta-btns">
          <a href="#cta" data-cta-intent="strategy-call" class="calc-cta-primary">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>

      <form class="calc-reachout" id="calcReachout" method="post" action="<?= $home_base ?>lead.php"
            data-lead-form data-lead-thanks="Thanks! We’ll reach out within 1 business day.">
        <div class="calc-reachout-h"><i class="fa-solid fa-headset"></i> Have a VT team member reach out</div>
        <div class="calc-reachout-row">
          <input class="calc-reachout-field" type="email" name="email" placeholder="Email address" required autocomplete="email">
          <input class="calc-reachout-field" type="tel"   name="phone" placeholder="Phone (optional)" autocomplete="tel">
          <button class="calc-reachout-btn" type="submit">Get a callback <i class="fa-solid fa-arrow-right"></i></button>
        </div>
        <input type="hidden" name="source" value="roi-calculator">
        <input type="hidden" name="form" value="roi-callback">
        <input type="text" name="company_site" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
        <div class="calc-reachout-foot" data-lead-note>No spam. We respond within 1 business day &middot; covered by the 30-Day Right-Fit Promise.</div>
      </form>
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

<!-- SPECIALTIES -->
<section class="sec" id="specialties">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-stethoscope"></i> Our Specializations</div>
    <h2 class="sec-h2">Virtual Assistants Built for <em>Healthcare</em></h2>
    <p class="sec-sub">Whether you run a medical practice, dental clinic, or RCM operation &mdash; our VAs understand clinical workflows, compliance, and patient communication from day one.</p>
  </div>

  <div class="spec-grid">
    <article class="spec-card reveal d1">
      <div class="spec-photo">
        <img src="images/photos/Medical-Virtual-Assistant.png" alt="Medical virtual assistant working on a computer" loading="lazy"/>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow med"><span class="dot"></span> HIPAA Certified &middot; Epic / Cerner / Athena Trained</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span>
          <h3 class="spec-title">Medical Virtual Assistants</h3>
        </div>
        <p class="spec-desc">HIPAA-certified medical VAs trained in clinical workflows, EHR systems, and patient communication. From billing and scribing to prior auth &mdash; plug into your practice in days, not weeks.</p>

        <div class="spec-proof">
          <div class="spec-proof-h"><i class="fa-solid fa-chart-line"></i> What our medical VAs ship in 90 days</div>
          <ul>
            <li><strong>AR days 52 &rarr; 23</strong> &mdash; Family Practice, Austin TX. $68k stalled claims recovered in 12 weeks.</li>
            <li><strong>+18 hrs/week reclaimed</strong> &mdash; Internal Medicine, Denver CO. Scribe ends after-hours charting.</li>
            <li><strong>95%+ clean claim rate</strong> &mdash; average across our specialist-tier medical billers.</li>
          </ul>
        </div>

        <div class="spec-pills">
          <a class="pill" href="services/medical-administrative-support/">Medical Admin Support <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-receptionist/">Medical Receptionist <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-biller/">Medical Biller <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-scribe/">Medical Scribe <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/medical-assistant/">Medical Assistant <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <a href="#calculator" class="spec-link">Calculate Medical VA Savings <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </article>

    <article class="spec-card alt reveal d2">
      <div class="spec-photo">
        <img src="images/photos/Dental-Virtual-Assistant.png" alt="Dental virtual assistant at reception desk on a computer" loading="lazy"/>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow med"><span class="dot"></span> HIPAA Certified &middot; Dentrix / Eaglesoft / Open Dental Trained</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-tooth"></i></span>
          <h3 class="spec-title">Dental Virtual Assistants</h3>
        </div>
        <p class="spec-desc">Dental-specific VAs fluent in Dentrix, Eaglesoft, Open Dental and Carestream. Patient recall, insurance billing, treatment coordination &mdash; your remote front desk, fully covered.</p>

        <div class="spec-proof">
          <div class="spec-proof-h"><i class="fa-solid fa-chart-line"></i> What our dental VAs ship in 60 days</div>
          <ul>
            <li><strong>No-shows 22% &rarr; 9%</strong> &mdash; Pediatric Dental, Tampa FL. +14 visits/week recovered from confirmations &amp; rebooks.</li>
            <li><strong>30%+ no-show reduction</strong> &mdash; Phoenix AZ dental practice with virtual receptionist on recall.</li>
            <li><strong>CDT-coded claims with narratives</strong> &mdash; first-pass clean-claim rate above 95%.</li>
          </ul>
        </div>

        <div class="spec-pills">
          <a class="pill" href="services/dental-admin/">Dental Admin Support <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-receptionist/">Dental Receptionist <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-biller/">Dental Biller <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-scribe/">Dental Scribe <i class="fa-solid fa-arrow-right"></i></a>
          <a class="pill" href="services/dental-coordinator/">Dental Coordinator <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <a href="#calculator" class="spec-link">Calculate Dental VA Savings <i class="fa-solid fa-arrow-right"></i></a>
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
    <a href="#cta" class="btn-gold">Get HIPAA-Ready VAs <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<div class="divider"></div>

<!-- GUARANTEE -->
<section class="sec guarantee" id="guarantee" aria-labelledby="g-h">
  <div class="g-wrap reveal">
    <div class="g-seal" aria-hidden="true">
      <div class="g-seal-ring">
        <div class="g-seal-h">VT</div>
        <div class="g-seal-s">30-Day<br>Right-Fit<br>Promise</div>
      </div>
    </div>
    <div class="g-copy">
      <div class="sec-lbl"><i class="fa-solid fa-shield-halved"></i> The VT 30-Day Right-Fit Promise</div>
      <h2 class="sec-h2" id="g-h">If It&rsquo;s Not Working in Month One, <em>We Make It Right</em></h2>
      <p class="sec-sub">Three commitments, published in writing &mdash; no fine print, no sales-call gotchas, no &ldquo;but you signed&rdquo; emails.</p>
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
          <p>Sick day, PTO, family emergency? Your Client Success Manager arranges a <strong>trained backup VA within hours</strong>, at no extra cost, briefed on your workflows and EHR access. Coverage ships with every placement &mdash; it&rsquo;s not an upsell.</p>
        </div>
      </div>

      <div class="g-audit">
        <div class="g-audit-h"><i class="fa-solid fa-clipboard-check"></i> What the Free 20-min Practice Staffing Audit covers</div>
        <ul>
          <li><strong>Workflow inventory.</strong> We map the 8&ndash;12 admin and clinical workflows that drain the most provider time in your practice &mdash; intake, charts, refills, billing, scheduling, recall, prior auth.</li>
          <li><strong>Outsourcing priority list.</strong> You leave the call with a ranked list of what to delegate <em>first</em> for fastest ROI, and what to keep in-house.</li>
          <li><strong>Tier &amp; headcount recommendation.</strong> Specific call on Pro vs Specialist tier, full-time vs part-time, and how many VAs to start with for your specialty and patient volume.</li>
          <li><strong>Honest no-fit answer.</strong> If outsourcing isn&rsquo;t right for your practice, we&rsquo;ll tell you on the call. No follow-up sales sequence.</li>
        </ul>
      </div>

      <div class="g-foot">
        <a href="#cta" data-cta-intent="strategy-call" class="btn-primary">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
        <a href="#cta" data-cta-intent="practice-audit" class="btn-glass">Or: Free 20-min Practice Staffing Audit <i class="fa-solid fa-clipboard-check"></i></a>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ROI STATS -->
<section class="sec">
  <div style="text-align:center;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-chart-line"></i> Proven Results</div>
    <h2 class="sec-h2">Real Numbers from Real Healthcare Clients</h2>
    <p class="sec-sub" style="margin:0 auto;">Pulled straight from our latest client KPI audit &mdash; measurable output against the targets we commit to.</p>
  </div>
  <div class="roi-grid">
    <div class="roi-card reveal d1">
      <span class="ico-circle lg roi-ico"><i class="fa-solid fa-coins"></i></span>
      <div class="roi-num" data-count="78" data-suffix="%">0%</div>
      <div class="roi-lbl">Average reduction in staffing costs vs. in-house healthcare hires</div>
    </div>
    <div class="roi-card reveal d2">
      <span class="ico-circle lg roi-ico"><i class="fa-solid fa-arrow-trend-up"></i></span>
      <div class="roi-num" data-count="52" data-suffix="%">0%</div>
      <div class="roi-lbl">Average increase in team productivity &mdash; output delivered over monthly KPI targets</div>
    </div>
    <div class="roi-card reveal d3">
      <span class="ico-circle lg roi-ico"><i class="fa-solid fa-chart-pie"></i></span>
      <div class="roi-num" data-count="67" data-suffix="%">0%</div>
      <div class="roi-lbl">Growth in profitable revenue &mdash; value created above committed targets</div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- PROCESS -->
<section class="sec">
  <div style="text-align:center;max-width:600px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> Our Process</div>
    <h2 class="sec-h2">Hire in Days, Not Months</h2>
    <p class="sec-sub" style="margin:0 auto;">A lightning-fast 3-step process built for busy doctors, dentists, and practice managers.</p>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1">
      <div class="pstep-head">
        <div class="pstep-num">01</div>
        <i class="fa-solid fa-calendar-check pstep-ico"></i>
      </div>
      <div class="pstep-eta"><i class="fa-solid fa-clock"></i> Within 24 hours</div>
      <h3 class="pstep-title">Book a Strategy Consultation</h3>
      <p class="pstep-desc">Submit the form and we&rsquo;ll confirm a strategy-call slot <strong>within one business day</strong>. The call itself takes 30 minutes and maps your practice, workflows, and the exact clinical or admin support you need.</p>
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
      <p class="pstep-desc">Agreement, billing, EHR access and SOP handoff all wrapped in <strong>1&ndash;2 weeks</strong>. Your VA hits the ground running with a dedicated Client Success Manager and the 30-Day Right-Fit Promise behind every placement.</p>
    </div>
  </div>
  <div class="proc-cta reveal">
    <a href="#cta" class="btn-primary">Start the Process &mdash; It&rsquo;s Free <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<div class="divider"></div>

<!-- CASE STUDIES -->
<section class="sec" id="testimonials" aria-labelledby="cs-h">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-chart-column"></i> Client KPI Results &middot; Latest Audit</div>
    <h2 class="sec-h2" id="cs-h">Targets Set. <em>Targets Beaten.</em></h2>
    <p class="sec-sub">Straight from our most recent client KPI audit &mdash; what each VT healthcare-billing teammate actually delivered against the monthly target we committed to. Real clients, real numbers.</p>
  </div>
  <div class="case-grid case-grid-4">
    <article class="case-card reveal d1">
      <div class="case-metric">
        <div class="case-metric-h">Insurance Verifications <span class="case-metric-badge">+144%</span></div>
        <div class="case-metric-row">
          <div class="case-metric-before"><span class="lbl">Monthly target</span><span class="val">20</span></div>
          <div class="case-metric-arrow"><i class="fa-solid fa-arrow-right"></i></div>
          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">49</span></div>
        </div>
        <div class="case-metric-foot">Payment posting also beat target by <strong>140%</strong> &mdash; 20 goal, <strong>48</strong> delivered</div>
      </div>
      <p class="case-q">Our RCM virtual teammate cleared nearly <strong>2.5&times;</strong> the monthly insurance-verification goal and more than doubled the payment-posting target &mdash; turning a backlog-prone month into one of the practice&rsquo;s strongest on record.</p>
      <div class="case-auth">
        <div class="case-logo" data-label="LifeQuest">
          <img src="images/clients/lifequest.png" alt="LifeQuest Physical Medicine and Rehab" loading="lazy" onerror="this.closest('.case-logo').classList.add('is-missing');this.remove();">
        </div>
        <div>
          <div class="case-name">LifeQuest</div>
          <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d2">
      <div class="case-metric">
        <div class="case-metric-h">Pre-Certifications <span class="case-metric-badge">+60%</span></div>
        <div class="case-metric-row">
          <div class="case-metric-before"><span class="lbl">Monthly target</span><span class="val">10</span></div>
          <div class="case-metric-arrow"><i class="fa-solid fa-arrow-right"></i></div>
          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">16</span></div>
        </div>
        <div class="case-metric-foot">Claims processed beat target by <strong>47%</strong> &mdash; 60 goal, <strong>88</strong> delivered</div>
      </div>
      <p class="case-q">A dedicated billing teammate pushed pre-certs <strong>60% past target</strong> and claims volume <strong>47% over</strong> &mdash; keeping authorizations ahead of the schedule so procedures never stalled and clean claims went out the same week.</p>
      <div class="case-auth">
        <div class="case-logo" data-label="Elkhart Clinic">
          <img src="images/clients/elkhart.png" alt="Elkhart Clinic" loading="lazy" onerror="this.closest('.case-logo').classList.add('is-missing');this.remove();">
        </div>
        <div>
          <div class="case-name">Elkhart Clinic</div>
          <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d3">
      <div class="case-metric">
        <div class="case-metric-h">Payment Posting <span class="case-metric-badge">+48%</span></div>
        <div class="case-metric-row">
          <div class="case-metric-before"><span class="lbl">Monthly target</span><span class="val">$250</span></div>
          <div class="case-metric-arrow"><i class="fa-solid fa-arrow-right"></i></div>
          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">$370</span></div>
        </div>
        <div class="case-metric-foot">Insurance verifications also beat target by <strong>30%</strong> &mdash; 20 goal, <strong>26</strong> delivered</div>
      </div>
      <p class="case-q">Payment posting landed <strong>48% above target</strong> with insurance verifications <strong>30% over</strong> &mdash; tightening the front of the revenue cycle so claims leave clean and cash lands faster.</p>
      <div class="case-auth">
        <div class="case-logo" data-label="Prostate Cancer Institute">
          <img src="images/clients/prostate-cancer-institute-logo-full.png" alt="Prostate Cancer Institute of America" loading="lazy" onerror="this.closest('.case-logo').classList.add('is-missing');this.remove();">
        </div>
        <div>
          <div class="case-name">Prostate Cancer Institute</div>
          <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d4">
      <div class="case-metric">
        <div class="case-metric-h">Claims Processed <span class="case-metric-badge">+33%</span></div>
        <div class="case-metric-row">
          <div class="case-metric-before"><span class="lbl">Monthly target</span><span class="val">30</span></div>
          <div class="case-metric-arrow"><i class="fa-solid fa-arrow-right"></i></div>
          <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">40</span></div>
        </div>
        <div class="case-metric-foot">Ramped fast on a high-volume endodontic &amp; oral-surgery caseload</div>
      </div>
      <p class="case-q">A specialty-billing teammate cleared claims <strong>33% above the monthly target</strong> for a busy endodontics &amp; oral-surgery group &mdash; keeping a high-volume surgical schedule billed and out the door on time.</p>
      <div class="case-auth">
        <div class="case-logo" data-label="Valley Endodontics &amp; Oral Surgery">
          <img src="images/clients/valley-endodontics-oral-surgery.png" alt="Valley Endodontics &amp; Oral Surgery (North Valley Endo)" loading="lazy" onerror="this.closest('.case-logo').classList.add('is-missing');this.remove();">
        </div>
        <div>
          <div class="case-name">North Valley Endo</div>
          <div class="case-svc"><i class="fa-solid fa-tooth"></i> Specialty Billing &amp; RCM VA</div>
        </div>
      </div>
    </article>
  </div>
</section>

<div class="divider"></div>

<!-- DIFFERENTIATORS -->
<section class="sec diff" id="why-vt">
  <div class="reveal" style="text-align:center;max-width:780px;margin:0 auto;">
    <div class="sec-lbl"><i class="fa-solid fa-medal"></i> Why Virtual Teammate</div>
    <h2 class="sec-h2">Three Reasons Practices Pick VT Over Every Other <em>Healthcare VA Agency</em></h2>
    <p class="sec-sub" style="margin:0 auto;">Every healthcare VA agency will sell you the same four bullets: flat rate, fast hiring, scalable, friendly. Here&rsquo;s where we&rsquo;re genuinely different.</p>
  </div>

  <div class="diff-grid">
    <article class="diff-card reveal d1">
      <div class="diff-num">01</div>
      <span class="ico-circle lg diff-ico"><i class="fa-solid fa-user-tie"></i></span>
      <h3 class="diff-title">A Dedicated CSM <em>From Day One</em></h3>
      <p class="diff-desc">Most agencies hand you a VA and a Slack channel and call it done. Every VT placement comes with a <strong>named Client Success Manager from day one</strong> &mdash; they own onboarding, quality monitoring, backup coverage and quarterly performance reviews. It&rsquo;s the operational backbone behind our <strong>95%+ client retention</strong> in a category that runs 60&ndash;70%.</p>
      <div class="diff-vs">
        <div class="diff-vs-row"><span class="diff-vs-them">Category Avg</span><span class="diff-vs-val them">60&ndash;70% retention &middot; no CSM</span></div>
        <div class="diff-vs-row"><span class="diff-vs-us">VT</span><span class="diff-vs-val us">95%+ retention &middot; dedicated CSM</span></div>
      </div>
    </article>

    <article class="diff-card reveal d2">
      <div class="diff-num">02</div>
      <span class="ico-circle lg diff-ico"><i class="fa-solid fa-stethoscope"></i></span>
      <h3 class="diff-title">Deep Healthcare Experience &mdash; <em>Medical AND Dental</em></h3>
      <p class="diff-desc">The major HIPAA-staffing players are either medical-only or treat dental as an afterthought. We run <strong>dedicated specialists in both</strong> &mdash; medical billers fluent in Epic, Cerner and Athenahealth alongside dental coordinators fluent in Dentrix, Eaglesoft, Open Dental and Carestream. One vendor covers your entire front and back office. <strong>200+ medical and dental practices served</strong>.</p>
      <div class="diff-vs">
        <div class="diff-vs-row"><span class="diff-vs-them">Others</span><span class="diff-vs-val them">Medical OR dental, not both</span></div>
        <div class="diff-vs-row"><span class="diff-vs-us">VT</span><span class="diff-vs-val us">Specialist-deep in both &middot; one vendor</span></div>
      </div>
    </article>

    <article class="diff-card reveal d3">
      <div class="diff-num">03</div>
      <span class="ico-circle lg diff-ico"><i class="fa-solid fa-earth-americas"></i></span>
      <h3 class="diff-title">A Global Bench, <em>On Your Time Zone</em></h3>
      <p class="diff-desc">Most of the category recruits from a single country. We source globally so we can match for your specialty, EHR, accent, language and shift &mdash; not just whoever is on the bench this week. Every VA is matched to your US time zone, so collaboration is real-time, not overnight.</p>
      <div class="diff-vs">
        <div class="diff-vs-row"><span class="diff-vs-them">Others</span><span class="diff-vs-val them">1 country &middot; one accent &middot; one shift</span></div>
        <div class="diff-vs-row"><span class="diff-vs-us">VT</span><span class="diff-vs-val us">Global bench &middot; matched to your clock</span></div>
      </div>
    </article>
  </div>
</section>

<div class="divider"></div>

<!-- GLOBAL COVERAGE (client-benefit framing — single map, one anchor per continent) -->
<section class="global global-compact" id="global" aria-labelledby="global-h">
  <div class="global-grid">
    <div class="global-l reveal">
      <div class="sec-lbl"><i class="fa-solid fa-earth-americas"></i> Global Coverage &middot; Local Cadence</div>
      <h2 class="sec-h2" id="global-h">A <em>Global Bench</em> of HIPAA-Certified Medical &amp; Dental Talent &mdash; On Your Time Zone</h2>
      <p class="sec-sub">A global recruiting reach plus a single US point of contact. Your dedicated Client Success Manager runs the bench so you don&rsquo;t have to think about it.</p>

      <ul class="global-benefits">
        <li><span class="ico-circle"><i class="fa-solid fa-clock"></i></span><span><strong>24/7 coverage in your US time zone.</strong> Morning, afternoon, evening or overnight shifts &mdash; one vendor, no compromises.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-check"></i></span><span><strong>Best-fit talent, not least-cost talent.</strong> A bigger bench means we recruit for your specialty, EHR, language and accent &mdash; not just availability.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-sack-dollar"></i></span><span><strong>Up to 78% lower staffing cost</strong> than a US in-house hire. Flat rate, all-in &mdash; no benefits, payroll tax, recruiter fees or PTO.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-tie"></i></span><span><strong>A named Client Success Manager from day one.</strong> Owns onboarding, quality, backup coverage and quarterly reviews. It&rsquo;s how we hold 95%+ retention.</span></li>
      </ul>
    </div>

    <div class="reveal d2">
      <div class="world" aria-label="Virtual Teammate global coverage — one anchor per continent">
        <div class="world-grid"></div>
        <svg class="map" viewBox="0 0 1000 500" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
          <path class="land" d="M40,95 L75,75 L120,55 L170,52 L220,58 L268,72 L305,86 L340,108 L355,128 L348,148 L325,154 L300,150 L282,160 L278,180 L268,200 L255,212 L238,210 L218,200 L200,188 L188,170 L178,155 L160,150 L138,148 L118,138 L96,124 L78,112 L60,108 Z"/>
          <path class="land" d="M380,35 L418,32 L445,48 L452,75 L438,92 L408,98 L382,90 L370,72 L370,52 Z"/>
          <path class="land" d="M240,210 L262,215 L278,222 L290,232 L298,242 L286,238 L272,230 L255,222 L243,217 Z"/>
          <path class="land" d="M295,232 L322,228 L348,232 L372,242 L388,260 L398,282 L398,308 L388,335 L375,362 L358,388 L342,402 L324,405 L308,400 L295,386 L286,365 L280,340 L278,315 L283,290 L290,265 L295,248 Z"/>
          <path class="land" d="M468,100 L490,95 L512,90 L538,85 L562,82 L588,82 L608,90 L612,105 L600,115 L578,122 L558,125 L540,128 L520,128 L502,132 L488,130 L478,122 L470,112 Z"/>
          <path class="land" d="M470,102 L484,98 L490,112 L484,122 L472,120 L466,110 Z"/>
          <path class="land" d="M540,55 L555,52 L568,70 L562,88 L548,90 L538,82 L535,68 Z"/>
          <path class="land" d="M478,150 L505,142 L535,142 L568,148 L595,158 L615,178 L622,205 L620,232 L612,260 L598,290 L582,318 L565,338 L548,348 L530,346 L516,335 L505,318 L494,295 L484,270 L478,245 L472,220 L468,195 L468,172 Z"/>
          <path class="land" d="M610,300 L620,295 L628,315 L622,332 L612,328 L608,315 Z"/>
          <path class="land" d="M608,95 L640,82 L678,72 L720,65 L765,60 L810,58 L850,62 L885,72 L915,85 L928,100 L920,118 L898,128 L870,132 L840,135 L808,140 L778,148 L748,150 L720,150 L692,148 L668,142 L645,135 L625,125 L612,112 L605,102 Z"/>
          <path class="land" d="M615,160 L640,158 L665,165 L678,182 L675,205 L662,218 L645,215 L630,205 L620,188 L615,175 Z"/>
          <path class="land" d="M700,155 L725,152 L745,158 L755,172 L755,195 L742,215 L725,228 L708,225 L698,210 L692,192 L692,175 Z"/>
          <path class="land" d="M770,158 L795,158 L808,170 L815,188 L808,208 L798,222 L785,222 L775,210 L768,195 L765,178 Z"/>
          <path class="land" d="M770,232 L800,228 L832,228 L862,232 L885,238 L865,250 L838,252 L810,250 L786,245 L770,240 Z"/>
          <path class="land" d="M848,200 L860,196 L868,210 L866,222 L856,226 L848,218 L846,208 Z"/>
          <path class="land" d="M892,118 L905,108 L915,118 L915,135 L905,142 L892,138 L888,128 Z"/>
          <path class="land" d="M815,288 L848,282 L880,282 L912,290 L932,302 L935,320 L920,338 L895,348 L865,352 L832,348 L808,338 L798,322 L800,305 Z"/>
        </svg>
        <svg class="world-arc" viewBox="0 0 1000 500" preserveAspectRatio="none" aria-hidden="true">
          <defs>
            <linearGradient id="arcGrad" x1="0" x2="1" y1="0" y2="0">
              <stop offset="0%" stop-color="rgba(223,169,73,0.7)"/>
              <stop offset="100%" stop-color="rgba(57,25,186,0.7)"/>
            </linearGradient>
          </defs>
          <!-- One connection from US HQ to each populated continent -->
          <path d="M195,155 Q260,260 320,340"/>
          <path d="M195,155 Q360,40 500,108"/>
          <path d="M195,155 Q400,360 555,300"/>
          <path d="M195,155 Q500,40 825,210"/>
        </svg>
        <!-- One anchor pin per continent -->
        <div class="world-pin" style="top:31.8%;left:19.5%;"><div class="pin-lbl">North America &middot; HQ</div></div>
        <div class="world-pin" style="top:64%;left:33%;"><div class="pin-lbl">South America</div></div>
        <div class="world-pin" style="top:21.6%;left:50%;"><div class="pin-lbl">Europe</div></div>
        <div class="world-pin" style="top:55%;left:57%;"><div class="pin-lbl">Africa</div></div>
        <div class="world-pin" style="top:42%;left:78%;"><div class="pin-lbl">Asia</div></div>
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
        $glob = __DIR__ . '/data/media/vt/' . (int) $r['id'] . '/photo.*';
        if (!glob($glob)) { continue; }
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
<!-- PROFILES (live from the VT portal — medical + dental only) -->
<section class="sec" id="profiles">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;" class="reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-id-badge"></i> Meet the Team</div>
      <h2 class="sec-h2" style="margin-bottom:0;">Your Future Healthcare Teammates</h2>
    </div>
    <a href="#cta" data-cta-intent="strategy-call" class="btn-primary" style="font-size:15px;padding:14px 28px;">See a Tailored Shortlist <i class="fa-solid fa-arrow-right"></i></a>
  </div>

  <?php if (!empty($homepage_profiles)): ?>
    <div class="prof-grid">
      <?php foreach ($homepage_profiles as $i => $p):
        $name = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
        $role = $p['role_title'] ?: ($p['department'] ?: 'Healthcare VA');
        $years = (int) $p['experience_years'];
        $ehr   = trim((string) $p['ehr_software']);
        $delay = 'd' . (($i % 4) + 1);
      ?>
        <div class="prof-card reveal <?= htmlspecialchars($delay) ?>">
          <div class="prof-photo"><img src="talent-photo.php?id=<?= (int) $p['id'] ?>" alt="<?= htmlspecialchars($name, ENT_QUOTES) ?>" loading="lazy"/></div>
          <div class="prof-name"><?= htmlspecialchars($name) ?></div>
          <div class="prof-role"><?= htmlspecialchars($role) ?></div>
          <?php if (!empty($p['country'])): ?>
            <div class="prof-loc"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($p['country']) ?></div>
          <?php endif; ?>
          <div class="prof-meta">
            <?php if ($years > 0): ?><span class="prof-meta-pill"><i class="fa-solid fa-briefcase"></i> <?= $years ?>y</span><?php endif; ?>
            <?php if ($ehr !== ''): ?><span class="prof-meta-pill"><i class="fa-solid fa-laptop-medical"></i> <?= htmlspecialchars(mb_strimwidth($ehr, 0, 30, '…')) ?></span><?php endif; ?>
          </div>
          <span class="prof-tag <?= htmlspecialchars($p['_tag_cls']) ?>"><?= htmlspecialchars($p['_tag']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="prof-empty card">
      <i class="fa-solid fa-user-doctor" style="color:var(--gold);font-size:24px;margin-bottom:10px;"></i>
      <p>Our medical &amp; dental bench is reviewed and matched manually for every engagement. <a href="#cta" data-cta-intent="strategy-call">Book a strategy call</a> to see candidates tailored to your specialty, EHR and time-zone preferences.</p>
    </div>
  <?php endif; ?>
</section>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div>
    <h2 class="sec-h2">Frequently Asked Questions</h2>
  </div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a medical or dental VA cost?</div><div class="faq-a">Published flat-rate pricing, no quote required. <strong>Pro tier starts at $1,625/mo for a full-time VA</strong> ($867/mo part-time). <strong>Specialist tier</strong> (medical billing, scribing, advanced coding, dental billing) starts at <strong>$2,167/mo full-time</strong> ($1,300/mo part-time). All-in flat rate &mdash; no benefits, payroll tax, recruiter fees or PTO billed on top. Typically 60&ndash;78% less than an equivalent US in-house hire &mdash; use the calculator above for your exact savings.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-rotate"></i> What happens if my VA isn&rsquo;t the right fit?</div><div class="faq-a">The VT 30-Day Right-Fit Promise covers two scenarios: <strong>(1)</strong> the VA isn&rsquo;t the right fit &rarr; we replace them at no cost with a re-shortlist inside 5 business days; <strong>(2)</strong> outsourcing isn&rsquo;t working for your practice &rarr; cancel within the first 30 days and we refund every billed day, no clawback. The guarantee is published in writing, not hidden in a sales call.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> What security &amp; compliance do you carry?</div><div class="faq-a">Every healthcare and dental VA completes <strong>HIPAA training and certification</strong> before placement and signs a BAA-compatible confidentiality agreement. We operate <strong>SOC 2 Type 2-aligned controls</strong> on the VT infrastructure that touches your data &mdash; encrypted laptops, hardware MFA, mandatory password manager, controlled network egress, least-privilege EHR access. Every access event by a VT VA is captured in an <strong>audit trail</strong> (who, what, when, from where), retained for 12 months and reviewable on request by your Client Success Manager.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-bolt"></i> How quickly can I get a VA started?</div><div class="faq-a">Curated shortlist within <strong>5&ndash;7 business days</strong>. After you pick your VA, onboarding wraps in <strong>1&ndash;2 weeks</strong> — agreement, EHR access, SOP handoff, shadow week, then live work. Your dedicated Client Success Manager runs the timeline so it lands when you need it.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-brain"></i> What EHR and dental software do your VAs know?</div><div class="faq-a">Medical VAs are trained on Epic, Cerner, eClinicalWorks, Athenahealth, NextGen, Practice Fusion and Kareo. Dental VAs are proficient in Dentrix, Dentrix Ascend, Eaglesoft, Open Dental and Carestream. Plus all major clearinghouses (Availity, Office Ally, Waystar, DentalXChange).</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your VAs based?</div><div class="faq-a">Wherever the best fit lives. Our global recruiting reach lets us match for your specialty, EHR, accent, language, and US time-zone shift &mdash; not just whoever happens to be on the bench. You hire for skill set; we handle the sourcing.</div></div>
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> Will I have a dedicated point of contact?</div><div class="faq-a">Yes. From day one, a named Client Success Manager is on your account &mdash; reachable on email, Slack/Teams, and a direct line during your business hours. They own performance, backup coverage and quarterly check-ins so you&rsquo;re never managing the placement alone.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-handshake-slash"></i> Will I be locked into a long-term contract?</div><div class="faq-a">No. Most healthcare VA agencies require 6&ndash;12 month commitments &mdash; we don&rsquo;t. Pause, scale up, scale down or cancel month-to-month, with no early-termination fees and no recapture clauses. The 30-Day Right-Fit Promise covers your first month on top of that.</div></div>
  </div>
</section>

<!-- CTA FORM -->
<section class="sec cta-stages-section" id="cta" style="padding-top:80px;padding-bottom:110px;">
  <div class="cta-stages-h reveal">
    <div class="sec-lbl"><i class="fa-solid fa-paper-plane"></i> Three Ways to Start</div>
    <h2 class="cta-h2" style="font-size:36px;">Pick the Entry Point<br>That Fits Where You Are</h2>
    <p class="cta-sub">Top of funnel, mid-funnel, or ready to scope &mdash; same team, three different first steps. Same color, same SLA, same Client Success Manager waiting on the other side.</p>
  </div>

  <div class="cta-stages-grid reveal d1">
    <article class="cta-stage" data-cta-intent="buyers-checklist">
      <div class="cta-stage-tag">Just exploring</div>
      <span class="ico-circle lg"><i class="fa-solid fa-file-lines"></i></span>
      <h3>HIPAA VA Buyer&rsquo;s Checklist</h3>
      <p class="cta-stage-lead">22 questions to ask any healthcare VA agency before you sign. Drop your email &mdash; we&rsquo;ll send the PDF.</p>
      <ul class="cta-stage-list">
        <li>Compliance, BAA, audit-trail questions</li>
        <li>Pricing-model traps to watch for</li>
        <li>Performance &amp; quality SLAs to demand</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-form" data-cta-intent="buyers-checklist">Send Me the Checklist <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-mid" data-cta-intent="practice-audit">
      <div class="cta-stage-tag">Ready to diagnose</div>
      <span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span>
      <h3>Free 20-min Practice Audit</h3>
      <p class="cta-stage-lead">Diagnostic-only call. We map your top admin and clinical workflows and tell you what to delegate first.</p>
      <ul class="cta-stage-list">
        <li>Workflow inventory (8&ndash;12 mapped)</li>
        <li>Ranked outsourcing-priority list</li>
        <li>Tier + headcount recommendation</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-form" data-cta-intent="practice-audit">Book My Practice Audit <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-high" data-cta-intent="strategy-call">
      <div class="cta-stage-tag">Ready to talk</div>
      <span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span>
      <h3>Strategy Call &amp; Jumpstart</h3>
      <p class="cta-stage-lead">30-min call. Map your needs, define the role, get matched to candidates within 5&ndash;7 business days.</p>
      <ul class="cta-stage-list">
        <li>Role scope + EHR / specialty match</li>
        <li>Tailored candidate shortlist</li>
        <li>Onboarding plan + CSM intro</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-form" data-cta-intent="strategy-call">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
    </article>
  </div>

  <div class="cta-inner reveal" id="cta-form">
    <h2 class="cta-h2" id="ctaHeading" style="font-size:30px;">Tell Us About Your Practice</h2>
    <p class="cta-sub" id="ctaSub">Pick a stage above and complete the form &mdash; we&rsquo;ll reply within one business day.</p>

    <form id="ctaForm" method="post" action="<?= $home_base ?>lead.php" data-lead-form
          data-lead-thanks="Thanks! We will be in touch within 1 business day.">
      <input type="hidden" name="intent" id="ctaIntent" value="strategy-call"/>
      <div class="cf-row">
        <input class="cf-field" name="first_name" placeholder="First Name" required/>
        <input class="cf-field" name="last_name" placeholder="Last Name" required/>
      </div>
      <div class="cf-row">
        <input class="cf-field" name="email" placeholder="Email Address" type="email" required/>
        <input class="cf-field" name="phone" placeholder="Phone Number" type="tel"/>
      </div>
      <input class="cf-field" name="practice" placeholder="Practice / Clinic Name" style="margin-bottom:14px;" required/>
      <select class="cf-field" id="ctaRole" name="role" style="margin-bottom:14px;" required>
        <option value="">I need... (select role)</option>
        <optgroup label="Medical">
          <option>Medical Administrative Support</option>
          <option>Medical Receptionist</option>
          <option>Medical Biller / RCM Specialist</option>
          <option>Medical Scribe</option>
          <option>Medical Assistant</option>
        </optgroup>
        <optgroup label="Dental">
          <option>Dental Biller</option>
          <option>Dental Front-Desk / Recall</option>
        </optgroup>
        <option>Multiple VAs</option>
        <option>Business / Admin VA</option>
        <option>Not sure yet &mdash; help me diagnose</option>
      </select>
      <select class="cf-field" id="ctaSource" name="source"
              style="margin-bottom:14px;" required
              onchange="document.getElementById('ctaSourceOtherWrap').style.display = this.value === 'Other' ? '' : 'none'; var o = document.getElementById('ctaSourceOther'); if (this.value === 'Other') { o.required = true; o.focus(); } else { o.required = false; o.value=''; }">
        <option value="">Where did you hear about us?</option>
        <option>Google search</option>
        <option>Referral from a colleague or friend</option>
        <option>Existing client / Word of mouth</option>
        <option>Facebook</option>
        <option>Instagram</option>
        <option>LinkedIn</option>
        <option>YouTube</option>
        <option>TikTok</option>
        <option>Podcast</option>
        <option>Webinar / Event</option>
        <option>Industry publication or news</option>
        <option>Email newsletter</option>
        <option>Online ad</option>
        <option>Other</option>
      </select>
      <div id="ctaSourceOtherWrap" style="display:none;margin-bottom:20px;">
        <input class="cf-field" id="ctaSourceOther" name="source_other" type="text"
               placeholder="Please tell us where" maxlength="120"/>
      </div>
      <input type="hidden" name="form" value="homepage-cta">
      <input type="text" name="company_site" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit" id="ctaSubmit">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>No commitment &middot; We respond within 1 business day &middot; Covered by the 30-Day Right-Fit Promise</div>
    </form>
  </div>
</section>

</main>
<?php $hide_lead_band = true; /* homepage already has the #cta + ROI forms */ ?>
<?php include 'includes/footer.php'; ?>
