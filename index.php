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
    <div class="hero-eyebrow reveal"><span class="dot"></span> Global HIPAA-Certified Healthcare Staffing &middot; 12+ Countries</div>
    <h1 class="hero-h1 reveal d1">HIPAA-Certified <em>Medical &amp; Dental</em><br>Virtual Assistants &mdash; Sourced<br>Globally, Delivered Locally</h1>
    <p class="hero-sub reveal d2">A worldwide talent network of HIPAA-certified specialists trained on Epic, Cerner, Dentrix, Eaglesoft &amp; Open Dental &mdash; matched to <strong>your US time zone</strong> and cutting staffing costs by up to <strong>78%</strong>.</p>
    <div class="hero-btns reveal d3">
      <a href="#cta" class="btn-primary">Book a Free Strategy Session <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#calculator" class="btn-glass">Calculate My Savings <i class="fa-solid fa-calculator"></i></a>
      <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Reply within 1 business day</span>
    </div>
    <div class="trust-row reveal d4">
      <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Certified</div>
      <div class="trust-item"><i class="fa-solid fa-brain"></i> EHR &amp; Dental Software Trained</div>
      <div class="trust-item"><i class="fa-solid fa-user-shield"></i> Background Checked</div>
      <div class="trust-item"><i class="fa-solid fa-bolt"></i> Start in Days</div>
      <div class="trust-item"><i class="fa-solid fa-globe"></i> Your Time Zone</div>
    </div>
    <div class="hero-stats reveal d5">
      <div class="hstat"><div class="hstat-num" data-count="78" data-suffix="%">0%</div><div class="hstat-lbl">Cost Savings</div></div>
      <div class="hstat"><div class="hstat-num" data-count="1200" data-suffix="+">0+</div><div class="hstat-lbl">VAs in Network</div></div>
      <div class="hstat"><div class="hstat-num" data-count="12" data-suffix="+">0+</div><div class="hstat-lbl">Countries</div></div>
      <div class="hstat"><div class="hstat-num" data-count="200" data-suffix="+">0+</div><div class="hstat-lbl">Healthcare Clients</div></div>
    </div>
  </div>

  <!-- HERO VISUAL: photo collage with floating chips -->
  <div class="hero-visual reveal d2" aria-hidden="true">
    <div class="hv-chip t1"><i class="fa-solid fa-circle-check"></i> HIPAA Verified</div>
    <div class="hv-chip t2"><i class="fa-solid fa-earth-americas"></i> 12+ Countries</div>
    <div class="hv-chip t3"><i class="fa-solid fa-clock"></i> Your Time Zone</div>
    <div class="hv-card hv-main">
      <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=900&q=85" alt="American doctor in white coat with stethoscope"/>
    </div>
    <div class="hv-card hv-side">
      <img src="https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=600&q=85" alt="Dentist examining patient in dental clinic"/>
    </div>
  </div>
</header>

<!-- ROI CALCULATOR -->
<section class="calc-wrap" id="calculator" aria-labelledby="calc-h">
  <div class="calc-head reveal">
    <div class="calc-head-l">
      <div class="calc-badge"><i class="fa-solid fa-calculator"></i> Live ROI Calculator</div>
      <h2 class="sec-h2" id="calc-h">See Your Annual Savings <em>&mdash; In Real Time</em></h2>
      <p class="sec-sub">Built on bi-weekly placement data from 200+ live healthcare clients. Adjust your role and team size below to see what a Virtual Teammate replaces against an equivalent US in-house hire.</p>
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
          <div class="calc-hero-sub"><span id="calcBiWk">$0</span> bi-weekly &middot; <span id="calcMonthly">$0</span>/month</div>
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

      <div class="calc-cta">
        <div class="calc-cta-l">Ready to capture <strong id="calcCtaAmt">these savings</strong>?<br>Get your tailored placement plan in one free call.</div>
        <a href="#cta">Book My Free Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- CLIENT MARQUEE -->
<div class="marquee" aria-label="Companies served by Virtual Teammate">
  <div class="marquee-lbl"><i class="fa-solid fa-handshake-angle"></i> Trusted By Practices &amp; Businesses Across The U.S.</div>
  <div class="marquee-track-wrap">
    <div class="marquee-track" id="mqTrack"></div>
  </div>
</div>

<!-- GLOBAL NETWORK -->
<section class="global" id="global">
  <div class="global-grid">
    <div class="global-l reveal">
      <div class="sec-lbl"><i class="fa-solid fa-earth-americas"></i> Global Network</div>
      <h2 class="sec-h2">A <em>Worldwide</em> Talent Network. A Local Partner.</h2>
      <p class="sec-sub">Virtual Teammate operates as a global staffing network &mdash; sourcing HIPAA-certified specialists from talent hubs across four continents, vetted to a single standard and matched to your US time zone.</p>

      <div class="global-stats">
        <div class="gstat">
          <div class="gstat-ico"><span class="ico-circle"><i class="fa-solid fa-globe"></i></span></div>
          <div class="gstat-num"><span data-count="12" data-suffix="+">0+</span></div>
          <div class="gstat-lbl">Countries with active placements</div>
        </div>
        <div class="gstat">
          <div class="gstat-ico"><span class="ico-circle"><i class="fa-solid fa-users"></i></span></div>
          <div class="gstat-num"><span data-count="1200" data-suffix="+">0+</span></div>
          <div class="gstat-lbl">Vetted VAs in our global network</div>
        </div>
        <div class="gstat">
          <div class="gstat-ico"><span class="ico-circle"><i class="fa-solid fa-tower-broadcast"></i></span></div>
          <div class="gstat-num"><span data-count="4" data-suffix="">0</span></div>
          <div class="gstat-lbl">Continents covered by our hubs</div>
        </div>
        <div class="gstat">
          <div class="gstat-ico"><span class="ico-circle"><i class="fa-solid fa-clock"></i></span></div>
          <div class="gstat-num"><span data-count="24" data-suffix="/7">0/7</span></div>
          <div class="gstat-lbl">Coverage across all US time zones</div>
        </div>
      </div>

      <div>
        <div class="sec-lbl" style="margin-bottom:14px;font-size:11px;"><i class="fa-solid fa-location-dot"></i> Active Talent Hubs</div>
        <div class="global-hubs">
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> United States &middot; HQ</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Mexico</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Colombia</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Brazil</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Argentina</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Peru</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> United Kingdom</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> South Africa</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Kenya</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> India</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Philippines</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Vietnam</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Thailand</span>
          <span class="hub-chip"><i class="fa-solid fa-circle"></i> Indonesia</span>
        </div>
      </div>
    </div>

    <div class="reveal d2">
      <div class="world" aria-label="Global Virtual Teammate hub map">
        <div class="world-grid"></div>
        <!-- Realistic continent outlines (equirectangular projection, viewBox 1000×500) -->
        <svg class="map" viewBox="0 0 1000 500" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
          <!-- North America (Alaska → Canada → East Coast → Mexico → Pacific NW) -->
          <path class="land" d="M40,95 L75,75 L120,55 L170,52 L220,58 L268,72 L305,86 L340,108 L355,128 L348,148 L325,154 L300,150 L282,160 L278,180 L268,200 L255,212 L238,210 L218,200 L200,188 L188,170 L178,155 L160,150 L138,148 L118,138 L96,124 L78,112 L60,108 Z"/>
          <!-- Greenland -->
          <path class="land" d="M380,35 L418,32 L445,48 L452,75 L438,92 L408,98 L382,90 L370,72 L370,52 Z"/>
          <!-- Central America (separate sliver) -->
          <path class="land" d="M240,210 L262,215 L278,222 L290,232 L298,242 L286,238 L272,230 L255,222 L243,217 Z"/>
          <!-- South America (Caribbean coast → NE Brazil → Patagonia → Pacific) -->
          <path class="land" d="M295,232 L322,228 L348,232 L372,242 L388,260 L398,282 L398,308 L388,335 L375,362 L358,388 L342,402 L324,405 L308,400 L295,386 L286,365 L280,340 L278,315 L283,290 L290,265 L295,248 Z"/>
          <!-- Europe (Iberia → Scandinavia → Russia west) -->
          <path class="land" d="M468,100 L490,95 L512,90 L538,85 L562,82 L588,82 L608,90 L612,105 L600,115 L578,122 L558,125 L540,128 L520,128 L502,132 L488,130 L478,122 L470,112 Z"/>
          <!-- British Isles -->
          <path class="land" d="M470,102 L484,98 L490,112 L484,122 L472,120 L466,110 Z"/>
          <!-- Scandinavia / Norway peninsula -->
          <path class="land" d="M540,55 L555,52 L568,70 L562,88 L548,90 L538,82 L535,68 Z"/>
          <!-- Africa (Med coast → Horn → Cape → West Africa) -->
          <path class="land" d="M478,150 L505,142 L535,142 L568,148 L595,158 L615,178 L622,205 L620,232 L612,260 L598,290 L582,318 L565,338 L548,348 L530,346 L516,335 L505,318 L494,295 L484,270 L478,245 L472,220 L468,195 L468,172 Z"/>
          <!-- Madagascar -->
          <path class="land" d="M610,300 L620,295 L628,315 L622,332 L612,328 L608,315 Z"/>
          <!-- Asia main bulk (Anatolia → Siberia → Korea → SE) -->
          <path class="land" d="M608,95 L640,82 L678,72 L720,65 L765,60 L810,58 L850,62 L885,72 L915,85 L928,100 L920,118 L898,128 L870,132 L840,135 L808,140 L778,148 L748,150 L720,150 L692,148 L668,142 L645,135 L625,125 L612,112 L605,102 Z"/>
          <!-- Arabian Peninsula -->
          <path class="land" d="M615,160 L640,158 L665,165 L678,182 L675,205 L662,218 L645,215 L630,205 L620,188 L615,175 Z"/>
          <!-- India (Subcontinent) -->
          <path class="land" d="M700,155 L725,152 L745,158 L755,172 L755,195 L742,215 L725,228 L708,225 L698,210 L692,192 L692,175 Z"/>
          <!-- Indochina / Mainland SE Asia -->
          <path class="land" d="M770,158 L795,158 L808,170 L815,188 L808,208 L798,222 L785,222 L775,210 L768,195 L765,178 Z"/>
          <!-- Indonesia (Sumatra + Java + Borneo + Sulawesi cluster) -->
          <path class="land" d="M770,232 L800,228 L832,228 L862,232 L885,238 L865,250 L838,252 L810,250 L786,245 L770,240 Z"/>
          <path class="land" d="M810,242 L832,240 L848,250 L840,258 L820,256 L812,250 Z"/>
          <!-- Philippines -->
          <path class="land" d="M848,200 L860,196 L868,210 L866,222 L856,226 L848,218 L846,208 Z"/>
          <!-- Japan -->
          <path class="land" d="M892,118 L905,108 L915,118 L915,135 L905,142 L892,138 L888,128 Z"/>
          <!-- New Guinea -->
          <path class="land" d="M890,248 L920,248 L935,258 L928,265 L905,262 L888,255 Z"/>
          <!-- Australia -->
          <path class="land" d="M815,288 L848,282 L880,282 L912,290 L932,302 L935,320 L920,338 L895,348 L865,352 L832,348 L808,338 L798,322 L800,305 Z"/>
          <!-- Tasmania -->
          <path class="land" d="M880,358 L895,358 L898,368 L888,372 L878,368 Z"/>
          <!-- New Zealand -->
          <path class="land" d="M955,348 L965,348 L968,362 L962,372 L953,368 L952,358 Z"/>
          <path class="land" d="M960,378 L970,378 L972,388 L962,392 Z"/>
        </svg>
        <svg class="world-arc" viewBox="0 0 1000 500" preserveAspectRatio="none" aria-hidden="true">
          <defs>
            <linearGradient id="arcGrad" x1="0" x2="1" y1="0" y2="0">
              <stop offset="0%" stop-color="rgba(223,169,73,0.7)"/>
              <stop offset="100%" stop-color="rgba(57,25,186,0.7)"/>
            </linearGradient>
          </defs>
          <!-- Routes from US HQ outward -->
          <path d="M195,155 Q360,40 500,108"/>
          <path d="M195,155 Q420,260 720,175"/>
          <path d="M195,155 Q260,260 320,340"/>
          <path d="M195,155 Q380,340 855,210"/>
          <!-- Europe to Africa / Asia -->
          <path d="M500,108 Q560,210 605,255"/>
          <path d="M500,108 Q620,150 720,175"/>
          <!-- Within LATAM -->
          <path d="M225,200 Q280,230 320,340"/>
          <path d="M320,340 Q345,360 370,318"/>
          <!-- Within Asia -->
          <path d="M720,175 Q780,210 855,210"/>
          <path d="M855,210 Q830,240 810,248"/>
          <path d="M720,175 Q760,200 795,182"/>
        </svg>
        <!-- Hub pins (top/left % match equirectangular projection) -->
        <!-- North America -->
        <div class="world-pin" style="top:31.8%;left:19.5%;"><div class="pin-lbl">USA · HQ</div></div>
        <div class="world-pin" style="top:39.4%;left:22.5%;"><div class="pin-lbl">Mexico</div></div>
        <!-- South America (boosted) -->
        <div class="world-pin" style="top:47.2%;left:29.4%;"><div class="pin-lbl">Colombia</div></div>
        <div class="world-pin" style="top:62.8%;left:37.2%;"><div class="pin-lbl">Brazil</div></div>
        <div class="world-pin" style="top:68.8%;left:33.9%;"><div class="pin-lbl">Argentina</div></div>
        <div class="world-pin" style="top:54%;left:28.2%;"><div class="pin-lbl">Peru</div></div>
        <!-- Europe -->
        <div class="world-pin" style="top:21.6%;left:50%;"><div class="pin-lbl">UK</div></div>
        <!-- Africa -->
        <div class="world-pin" style="top:50.6%;left:60.3%;"><div class="pin-lbl">Kenya</div></div>
        <div class="world-pin" style="top:68.4%;left:55%;"><div class="pin-lbl">S. Africa</div></div>
        <!-- Asia (boosted) -->
        <div class="world-pin" style="top:34.4%;left:71.4%;"><div class="pin-lbl">India</div></div>
        <div class="world-pin" style="top:42.2%;left:83.6%;"><div class="pin-lbl">Philippines</div></div>
        <div class="world-pin" style="top:43.8%;left:79.7%;"><div class="pin-lbl">Vietnam</div></div>
        <div class="world-pin" style="top:53.4%;left:80.5%;"><div class="pin-lbl">Indonesia</div></div>
        <div class="world-pin" style="top:38%;left:77.5%;"><div class="pin-lbl">Thailand</div></div>
      </div>
    </div>
  </div>
</section>

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
        <img src="images/photos/medical-assistant.jpg" alt="Medical assistant in scrubs at work"/>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow med"><span class="dot"></span> Most Popular &middot; HIPAA Certified</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span>
          <h3 class="spec-title">Medical Virtual Assistants</h3>
        </div>
        <p class="spec-desc">HIPAA-certified medical VAs trained in clinical workflows, EHR systems, and patient communication. From billing and scribing to prior auth &mdash; plug into your practice in days, not weeks.</p>
        <div class="spec-pills">
          <span class="pill">Medical Billing</span>
          <span class="pill">Medical Scribing</span>
          <span class="pill">Patient Scheduling</span>
          <span class="pill">Prior Authorization</span>
          <span class="pill">Insurance Verification</span>
          <span class="pill">EHR Data Entry</span>
        </div>
        <a href="#calculator" class="spec-link">Calculate Medical VA Savings <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </article>

    <article class="spec-card alt reveal d2">
      <div class="spec-photo">
        <img src="images/photos/dental-assistant.jpg" alt="Dental assistant working with patient in dental clinic"/>
      </div>
      <div class="spec-content">
        <div class="spec-eyebrow dent"><span class="dot"></span> Dental Specialists &middot; PMS Trained</div>
        <div class="spec-title-row">
          <span class="ico-circle lg"><i class="fa-solid fa-tooth"></i></span>
          <h3 class="spec-title">Dental Virtual Assistants</h3>
        </div>
        <p class="spec-desc">Dental-specific VAs fluent in Dentrix, Eaglesoft, Open Dental and Carestream. Patient recall, insurance billing, treatment coordination &mdash; your remote front desk, fully covered.</p>
        <div class="spec-pills">
          <span class="pill">Dental Billing</span>
          <span class="pill">Patient Recall</span>
          <span class="pill">Insurance Claims</span>
          <span class="pill">Appointment Scheduling</span>
          <span class="pill">Treatment Coordination</span>
          <span class="pill">EOB Posting</span>
        </div>
        <a href="#calculator" class="spec-link dent">Calculate Dental VA Savings <i class="fa-solid fa-arrow-right"></i></a>
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

<!-- ROI STATS -->
<section class="sec">
  <div style="text-align:center;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-chart-line"></i> Proven Results</div>
    <h2 class="sec-h2">Real Numbers from Real Healthcare Clients</h2>
    <p class="sec-sub" style="margin:0 auto;">Medical practices and dental clinics see measurable ROI &mdash; often within the first 30 days.</p>
  </div>
  <div class="roi-grid">
    <div class="roi-card reveal d1">
      <span class="ico-circle lg roi-ico"><i class="fa-solid fa-coins"></i></span>
      <div class="roi-num" data-count="78" data-suffix="%">0%</div>
      <div class="roi-lbl">Average reduction in staffing costs vs. in-house healthcare hires</div>
    </div>
    <div class="roi-card reveal d2">
      <span class="ico-circle lg roi-ico"><i class="fa-solid fa-arrow-trend-up"></i></span>
      <div class="roi-num" data-count="80" data-suffix="%">0%</div>
      <div class="roi-lbl">Increase in team productivity reported by medical clients</div>
    </div>
    <div class="roi-card reveal d3">
      <span class="ico-circle lg roi-ico"><i class="fa-solid fa-chart-pie"></i></span>
      <div class="roi-num" data-count="50" data-suffix="%">0%</div>
      <div class="roi-lbl">Growth in profitable revenue potential within 6 months</div>
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
      <h3 class="pstep-title">Book a Strategy Consultation</h3>
      <p class="pstep-desc">Schedule a quick discovery call. We learn your practice, workflows, and the exact clinical or admin support you need.</p>
    </div>
    <div class="pstep reveal d2">
      <div class="pstep-head">
        <div class="pstep-num">02</div>
        <i class="fa-solid fa-users-viewfinder pstep-ico"></i>
      </div>
      <h3 class="pstep-title">Meet &amp; Interview Candidates</h3>
      <p class="pstep-desc">We handpick a curated shortlist of HIPAA-certified VAs for you to review and interview. You choose the perfect fit.</p>
    </div>
    <div class="pstep reveal d3">
      <div class="pstep-head">
        <div class="pstep-num">03</div>
        <i class="fa-solid fa-rocket pstep-ico"></i>
      </div>
      <h3 class="pstep-title">Launch &amp; Onboard Seamlessly</h3>
      <p class="pstep-desc">We handle the agreement, billing, and onboarding &mdash; your VA hits the ground running with a dedicated Client Success Manager.</p>
    </div>
  </div>
  <div class="proc-cta reveal">
    <a href="#cta" class="btn-primary">Start the Process &mdash; It&rsquo;s Free <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<div class="divider"></div>

<!-- TESTIMONIALS -->
<section class="sec">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-comment-medical"></i> Client Success Stories</div>
    <h2 class="sec-h2">Real Value Creation, Real Growth</h2>
  </div>
  <div class="test-grid">
    <div class="test-card reveal d1">
      <i class="fa-solid fa-quote-right test-quote-ico"></i>
      <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
      <p class="test-q">&ldquo;Our dental VA handles patient recall, insurance verification, and scheduling &mdash; she&rsquo;s become indispensable. We&rsquo;ve reduced no-shows by 30% since onboarding her.&rdquo;</p>
      <div class="test-auth">
        <div class="test-photo"><img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=200&q=80" alt="Dr. Marcus Reyes"/></div>
        <div>
          <div class="test-name">Dr. Marcus Reyes</div>
          <div class="test-role"><i class="fa-solid fa-location-dot"></i> Dental Practice Owner, Phoenix AZ</div>
        </div>
      </div>
    </div>
    <div class="test-card reveal d2">
      <i class="fa-solid fa-quote-right test-quote-ico"></i>
      <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
      <p class="test-q">&ldquo;We brought on a medical billing VA and cleared our AR backlog in 6 weeks. The ROI was immediate &mdash; we recovered over $40k in outstanding claims right away.&rdquo;</p>
      <div class="test-auth">
        <div class="test-photo"><img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=200&q=80" alt="Sarah Liu, MD"/></div>
        <div>
          <div class="test-name">Sarah Liu, MD</div>
          <div class="test-role"><i class="fa-solid fa-location-dot"></i> Family Practice Owner, Scottsdale AZ</div>
        </div>
      </div>
    </div>
    <div class="test-card reveal d3">
      <i class="fa-solid fa-quote-right test-quote-ico"></i>
      <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
      <p class="test-q">&ldquo;Our scribe VA charts during every visit so I can actually look at my patients again. Documentation is done before I walk out of the room.&rdquo;</p>
      <div class="test-auth">
        <div class="test-photo"><img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=200&q=80" alt="Dr. Janelle Torres"/></div>
        <div>
          <div class="test-name">Dr. Janelle Torres</div>
          <div class="test-role"><i class="fa-solid fa-location-dot"></i> Internal Medicine, Mesa AZ</div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- HOW WE WORK -->
<section class="sec">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-circle-info"></i> Why Virtual Teammate</div>
    <h2 class="sec-h2">We Didn&rsquo;t Invent Virtual Staffing &mdash;<br>We Made It Better for <em>Healthcare</em></h2>
  </div>
  <div class="hw-grid">
    <div class="hw-card reveal d1">
      <span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span>
      <div><h3 class="hw-title">Simple Flat-Rate Pricing</h3><p class="hw-desc">No hidden fees, no surprises. Transparent pricing means you always know what you&rsquo;re paying &mdash; predictable and budget-friendly.</p></div>
    </div>
    <div class="hw-card reveal d2">
      <span class="ico-circle lg"><i class="fa-solid fa-bolt"></i></span>
      <div><h3 class="hw-title">Lightning-Fast Hiring</h3><p class="hw-desc">Most clients have a HIPAA-certified shortlist ready within a week &mdash; no endless job postings, no recruiter fees.</p></div>
    </div>
    <div class="hw-card reveal d3">
      <span class="ico-circle lg"><i class="fa-solid fa-arrows-up-down-left-right"></i></span>
      <div><h3 class="hw-title">Dynamic Scalability</h3><p class="hw-desc">Scale your virtual team up or down as your patient volume changes &mdash; from a single VA to an entire remote back-office.</p></div>
    </div>
    <div class="hw-card reveal d4">
      <span class="ico-circle lg"><i class="fa-solid fa-trophy"></i></span>
      <div><h3 class="hw-title">Value-Based Culture</h3><p class="hw-desc">Our VAs are trained to think like value creators, not task-completers. Outcome-focused from day one.</p></div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- PROFILES -->
<section class="sec" id="profiles">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;" class="reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-id-badge"></i> Meet the Team</div>
      <h2 class="sec-h2" style="margin-bottom:0;">Your Future Healthcare Teammates</h2>
    </div>
    <a href="#" class="btn-primary" style="font-size:15px;padding:14px 28px;">View All Profiles <i class="fa-solid fa-arrow-right"></i></a>
  </div>
  <div class="prof-grid">
    <div class="prof-card reveal d1">
      <div class="prof-photo"><img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=300&q=80" alt="Roderick Oda"/></div>
      <div class="prof-name">Roderick Oda</div>
      <div class="prof-loc"><i class="fa-solid fa-location-dot"></i> Philippines</div>
      <span class="prof-tag med">Medical VA</span>
    </div>
    <div class="prof-card reveal d2">
      <div class="prof-photo"><img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=300&q=80" alt="Eldridge Urbana"/></div>
      <div class="prof-name">Eldridge Urbana</div>
      <div class="prof-loc"><i class="fa-solid fa-location-dot"></i> Colombia</div>
      <span class="prof-tag dent">Dental VA</span>
    </div>
    <div class="prof-card reveal d3">
      <div class="prof-photo"><img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80" alt="Rachelmae Autentico"/></div>
      <div class="prof-name">Rachelmae Autentico</div>
      <div class="prof-loc"><i class="fa-solid fa-location-dot"></i> Philippines</div>
      <span class="prof-tag med">Medical VA</span>
    </div>
    <div class="prof-card reveal d4">
      <div class="prof-photo"><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80" alt="Julvince Fantilanan"/></div>
      <div class="prof-name">Julvince Fantilanan</div>
      <div class="prof-loc"><i class="fa-solid fa-location-dot"></i> Philippines</div>
      <span class="prof-tag dent">Dental VA</span>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div>
    <h2 class="sec-h2">Frequently Asked Questions</h2>
  </div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Are your healthcare VAs HIPAA certified?</div><div class="faq-a">Yes. Every healthcare and dental VA completes HIPAA compliance training and certification before placement. Patient data privacy is non-negotiable in every engagement.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-bolt"></i> How quickly can I get a VA started?</div><div class="faq-a">Most clients receive a curated shortlist within days. After you select your VA, onboarding is handled by your dedicated Client Success Manager &mdash; often launching within 1&ndash;2 weeks.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-brain"></i> What EHR and dental software do your VAs know?</div><div class="faq-a">Medical VAs are trained in Epic, Cerner, eClinicalWorks, and more. Dental VAs are proficient in Dentrix, Eaglesoft, Open Dental, and Carestream.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your VAs based?</div><div class="faq-a">Virtual Teammate operates a global network spanning the Philippines, Latin America, Africa, and South Asia &mdash; every VA is matched to your US time zone for real-time collaboration.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a medical or dental VA cost?</div><div class="faq-a">Transparent flat-rate pricing with no hidden fees. Use the calculator above to see your exact annual savings &mdash; typically 60&ndash;78% less than an equivalent in-house hire.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-arrows-up-down-left-right"></i> Can I scale up or cancel if my needs change?</div><div class="faq-a">Yes. Virtual Teammate is built for flexibility &mdash; add teammates as you grow or adjust as your needs evolve. We&rsquo;re a staffing partner, not a locked-in contract.</div></div>
  </div>
</section>

<!-- BUSINESS STRIP -->
<aside class="biz-strip" aria-label="Business and nonprofit support">
  <div class="biz-strip-inner reveal">
    <div class="biz-strip-l">
      <div class="biz-strip-lbl">Beyond Healthcare</div>
      <div class="biz-strip-title">We also support businesses &amp; non-profits.</div>
      <div class="biz-strip-sub">Admin, sales, marketing, finance and customer-service VAs &mdash; same global network, same vetting, same flat-rate model.</div>
    </div>
    <div class="biz-strip-r">
      <a href="#" class="biz-tag"><i class="fa-solid fa-clipboard"></i> Administrative</a>
      <a href="#" class="biz-tag"><i class="fa-solid fa-bullseye"></i> Sales</a>
      <a href="#" class="biz-tag"><i class="fa-solid fa-bullhorn"></i> Marketing</a>
      <a href="#" class="biz-tag"><i class="fa-solid fa-sack-dollar"></i> Finance</a>
      <a href="#" class="biz-tag"><i class="fa-solid fa-headset"></i> Customer Service</a>
    </div>
  </div>
</aside>

<!-- CTA FORM -->
<section class="sec" id="cta" style="padding-top:80px;padding-bottom:110px;">
  <div class="cta-inner reveal">
    <div style="font-size:12px;font-weight:800;color:var(--gold);text-transform:uppercase;letter-spacing:1.2px;margin-bottom:14px;text-align:center;"><i class="fa-solid fa-paper-plane"></i> Get Started Today</div>
    <h2 class="cta-h2">Ready to Hire Your<br>Virtual Teammate?</h2>
    <p class="cta-sub">Join 200+ medical practices and dental clinics that have found their perfect HIPAA-certified VA match.</p>
    <form onsubmit="event.preventDefault();this.querySelector('.cf-submit').innerHTML='Thanks! We will be in touch within 1 business day';">
      <div class="cf-row">
        <input class="cf-field" placeholder="First Name" required/>
        <input class="cf-field" placeholder="Last Name" required/>
      </div>
      <div class="cf-row">
        <input class="cf-field" placeholder="Email Address" type="email" required/>
        <input class="cf-field" placeholder="Phone Number" type="tel" required/>
      </div>
      <input class="cf-field" placeholder="Practice / Clinic Name" style="margin-bottom:14px;" required/>
      <select class="cf-field" style="margin-bottom:20px;" required>
        <option value="">I need... (select type)</option>
        <option>Medical Virtual Assistant</option>
        <option>Dental Virtual Assistant</option>
        <option>Medical Billing / RCM Specialist</option>
        <option>Medical Scribe</option>
        <option>Multiple VAs</option>
        <option>Business / Admin VA</option>
      </select>
      <button class="cf-submit" type="submit">Get My Free Consultation <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note">No commitment required &middot; We respond within 1 business day</div>
    </form>
  </div>
</section>

</main>
<?php include 'includes/footer.php'; ?>
