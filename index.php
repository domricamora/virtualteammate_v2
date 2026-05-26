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
      <a href="#cta" class="btn-primary" data-cta-intent="strategy-call">Book My Free Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
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
        <div class="calc-cta-l">Ready to capture <strong id="calcCtaAmt">these savings</strong>?<br>Talk to us, or get the full report in your inbox.</div>
        <div class="calc-cta-btns">
          <a href="#cta" data-cta-intent="strategy-call" class="calc-cta-primary">Book My Free Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
          <a href="#cta" data-cta-intent="savings-report" class="calc-cta-secondary">Email Me My Full Savings Report <i class="fa-solid fa-envelope"></i></a>
        </div>
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
          <a class="pill" href="services/dental-biller/">Dental Biller <i class="fa-solid fa-arrow-right"></i></a>
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
      <div class="sec-lbl"><i class="fa-solid fa-shield-check"></i> Risk Reversal &middot; In Writing</div>
      <h2 class="sec-h2" id="g-h">If It&rsquo;s Not Working in Month One, <em>We Make It Right</em></h2>
      <p class="sec-sub">We&rsquo;ve placed VAs at 200+ practices. Most click immediately. The few that don&rsquo;t, we fix &mdash; fast. Our guarantee is in writing because your investment deserves it.</p>
      <div class="g-cards">
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-arrows-rotate"></i></span>
          <h3>No-Cost Replacement</h3>
          <p>Decide the fit isn&rsquo;t right in the first 30 days? We swap your VA at no charge, with a curated re-shortlist within 5 business days.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-rotate-left"></i></span>
          <h3>30-Day Satisfaction Window</h3>
          <p>Not sure outsourcing works for your practice? Cancel inside the first 30 days and we refund every billed day &mdash; no questions, no clawback.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
          <h3>Backup Coverage Built In</h3>
          <p>Sick day, PTO, surprise leave? Your dedicated Client Success Manager arranges trained backup coverage so your workflows never go dark.</p>
        </div>
      </div>
      <div class="g-foot">
        <a href="#cta" data-cta-intent="strategy-call" class="btn-primary">Book My Free Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
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

<!-- CASE STUDIES -->
<section class="sec" id="testimonials" aria-labelledby="cs-h">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-chart-column"></i> Case Studies &middot; Quantified Outcomes</div>
    <h2 class="sec-h2" id="cs-h">Real Numbers, Named Practices, <em>Three Regions</em></h2>
    <p class="sec-sub">Every case below is a current VT client with a measurable before/after &mdash; not a vague compliment. Pulled from our Q3-Q4 placement audits.</p>
  </div>
  <div class="case-grid">
    <article class="case-card reveal d1">
      <div class="case-metric">
        <div class="case-metric-h">AR Days</div>
        <div class="case-metric-row">
          <div class="case-metric-before"><span class="lbl">Before</span><span class="val">52d</span></div>
          <div class="case-metric-arrow"><i class="fa-solid fa-arrow-right"></i></div>
          <div class="case-metric-after"><span class="lbl">After 90 days</span><span class="val">23d</span></div>
        </div>
        <div class="case-metric-foot"><strong>$68k</strong> in stalled claims recovered in 12 weeks</div>
      </div>
      <p class="case-q">&ldquo;Our billing VA cleared 6 months of stalled claims in 12 weeks. Cash flow is the best it&rsquo;s ever been &mdash; and I&rsquo;ve stopped writing personal checks to cover payroll gaps.&rdquo;</p>
      <div class="case-auth">
        <div class="case-photo"><img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=200&q=80" alt="Dr. James Chen"/></div>
        <div>
          <div class="case-name">Dr. James Chen, MD</div>
          <div class="case-role"><i class="fa-solid fa-location-dot"></i> Hill Country Family Practice &middot; Austin, TX</div>
          <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Biller VA (Specialist tier)</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d2">
      <div class="case-metric">
        <div class="case-metric-h">No-Show Rate</div>
        <div class="case-metric-row">
          <div class="case-metric-before"><span class="lbl">Before</span><span class="val">22%</span></div>
          <div class="case-metric-arrow"><i class="fa-solid fa-arrow-right"></i></div>
          <div class="case-metric-after"><span class="lbl">After 60 days</span><span class="val">9%</span></div>
        </div>
        <div class="case-metric-foot"><strong>+14 visits/week</strong> recovered from confirmations &amp; rebooks</div>
      </div>
      <p class="case-q">&ldquo;Our virtual receptionist runs confirmations every afternoon and rebooks every cancellation the same day. She&rsquo;s added fourteen visits a week without us touching the phone.&rdquo;</p>
      <div class="case-auth">
        <div class="case-photo"><img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=200&q=80" alt="Dr. Sarah Patel"/></div>
        <div>
          <div class="case-name">Dr. Sarah Patel, DDS</div>
          <div class="case-role"><i class="fa-solid fa-location-dot"></i> Coastal Smile Pediatric Dental &middot; Tampa, FL</div>
          <div class="case-svc"><i class="fa-solid fa-tooth"></i> Dental Receptionist VA (Pro tier)</div>
        </div>
      </div>
    </article>

    <article class="case-card reveal d3">
      <div class="case-metric">
        <div class="case-metric-h">Provider Charting Time</div>
        <div class="case-metric-row">
          <div class="case-metric-before"><span class="lbl">Before</span><span class="val">11pm</span></div>
          <div class="case-metric-arrow"><i class="fa-solid fa-arrow-right"></i></div>
          <div class="case-metric-after"><span class="lbl">After</span><span class="val">6pm</span></div>
        </div>
        <div class="case-metric-foot"><strong>18 hrs/week</strong> of after-hours charting reclaimed</div>
      </div>
      <p class="case-q">&ldquo;My scribe documents every visit in Epic in real time. I look at patients again. I see my kids again. I will never go back to charting alone.&rdquo;</p>
      <div class="case-auth">
        <div class="case-photo"><img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=200&q=80" alt="Dr. Marcus Reyes"/></div>
        <div>
          <div class="case-name">Dr. Marcus Reyes, MD</div>
          <div class="case-role"><i class="fa-solid fa-location-dot"></i> Premier Internal Medicine &middot; Denver, CO</div>
          <div class="case-svc"><i class="fa-solid fa-pen-clip"></i> Medical Scribe VA (Specialist tier)</div>
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
    <h2 class="sec-h2">Three Things We Own That <em>The Category Doesn&rsquo;t</em></h2>
    <p class="sec-sub" style="margin:0 auto;">Every healthcare VA agency will sell you the same four bullets: flat rate, fast hiring, scalable, friendly. Here&rsquo;s where we&rsquo;re genuinely different.</p>
  </div>

  <div class="diff-grid">
    <article class="diff-card reveal d1">
      <div class="diff-num">01</div>
      <span class="ico-circle lg diff-ico"><i class="fa-solid fa-earth-americas"></i></span>
      <h3 class="diff-title">12+ Country Global Network &mdash; <em>Not Just Philippines</em></h3>
      <p class="diff-desc">Most of the category recruits from a single country (almost always the Philippines). We source from <strong>twelve</strong>: Philippines, Vietnam, Indonesia, India, Kenya, South Africa, UK, Mexico, Colombia, Peru, Brazil, Argentina. Bigger pool = better fit for your specialty, accent, language and time zone.</p>
      <div class="diff-vs">
        <div class="diff-vs-row"><span class="diff-vs-them">Others</span><span class="diff-vs-val them">1 country (PH-only)</span></div>
        <div class="diff-vs-row"><span class="diff-vs-us">VT</span><span class="diff-vs-val us">12+ countries &middot; 1,200+ vetted VAs</span></div>
      </div>
    </article>

    <article class="diff-card reveal d2">
      <div class="diff-num">02</div>
      <span class="ico-circle lg diff-ico"><i class="fa-solid fa-stethoscope"></i></span>
      <h3 class="diff-title">Specialist-Deep in Medical <em>and</em> Dental</h3>
      <p class="diff-desc">The major HIPAA-staffing players are either medical-only or run dental as an afterthought. We&rsquo;re one of the only major partners running <strong>dedicated dental specialists</strong> (Dentrix, Eaglesoft, Open Dental, Carestream) alongside our full medical roster &mdash; so one vendor covers your entire front and back office.</p>
      <div class="diff-vs">
        <div class="diff-vs-row"><span class="diff-vs-them">Others</span><span class="diff-vs-val them">Medical OR dental, not both</span></div>
        <div class="diff-vs-row"><span class="diff-vs-us">VT</span><span class="diff-vs-val us">Specialists in both &middot; 200+ medical &amp; dental clients</span></div>
      </div>
    </article>

    <article class="diff-card reveal d3">
      <div class="diff-num">03</div>
      <span class="ico-circle lg diff-ico"><i class="fa-solid fa-user-tie"></i></span>
      <h3 class="diff-title">A Dedicated CSM <em>From Day One</em></h3>
      <p class="diff-desc">Most agencies hand you the VA and a Slack channel and call it done. Every VT placement comes with a <strong>named Client Success Manager from day one</strong> &mdash; they own onboarding, quality monitoring, backup coverage and quarterly performance reviews. It&rsquo;s how we hold <strong>95%+ retention</strong> in a category that runs 60&ndash;70%.</p>
      <div class="diff-vs">
        <div class="diff-vs-row"><span class="diff-vs-them">Category Avg</span><span class="diff-vs-val them">60&ndash;70% retention &middot; no CSM</span></div>
        <div class="diff-vs-row"><span class="diff-vs-us">VT</span><span class="diff-vs-val us">95%+ retention &middot; dedicated CSM</span></div>
      </div>
    </article>
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
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a medical or dental VA cost?</div><div class="faq-a">Published flat-rate pricing, no quote required. <strong>Pro tier starts at $1,625/mo for a full-time VA</strong> ($867/mo part-time). <strong>Specialist tier</strong> (medical billing, scribing, advanced coding, dental billing) starts at <strong>$2,167/mo full-time</strong> ($1,300/mo part-time). All-in flat rate &mdash; no benefits, payroll tax, recruiter fees or PTO billed on top. Typically 60&ndash;78% less than an equivalent US in-house hire &mdash; use the calculator above for your exact savings.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-rotate"></i> What happens if my VA isn&rsquo;t the right fit?</div><div class="faq-a">The VT 30-Day Right-Fit Promise covers two scenarios: <strong>(1)</strong> the VA isn&rsquo;t the right fit &rarr; we replace them at no cost with a re-shortlist inside 5 business days; <strong>(2)</strong> outsourcing isn&rsquo;t working for your practice &rarr; cancel within the first 30 days and we refund every billed day, no clawback. The guarantee is published in writing, not hidden in a sales call.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Are your healthcare VAs HIPAA certified?</div><div class="faq-a">Yes. Every healthcare and dental VA completes HIPAA compliance training and certification before placement, signs a BAA-compatible confidentiality agreement, and works in controlled, encrypted environments. Patient data privacy is non-negotiable.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-bolt"></i> How quickly can I get a VA started?</div><div class="faq-a">Curated shortlist within days. Onboarding is handled by your dedicated Client Success Manager. Average time-to-live across 200+ placements is <strong>14 days</strong>.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-brain"></i> What EHR and dental software do your VAs know?</div><div class="faq-a">Medical VAs are trained on Epic, Cerner, eClinicalWorks, Athenahealth, NextGen, Practice Fusion, Kareo and more. Dental VAs are proficient in Dentrix, Dentrix Ascend, Eaglesoft, Open Dental and Carestream. Plus all major clearinghouses (Availity, Office Ally, Waystar, DentalXChange).</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your VAs based?</div><div class="faq-a">12+ countries spanning Latin America (Mexico, Colombia, Peru, Brazil, Argentina), Africa (Kenya, South Africa), Europe (UK), and Asia (Philippines, India, Vietnam, Indonesia). Every VA is matched to your US time zone for real-time collaboration.</div></div>
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> Do I get a dedicated point of contact?</div><div class="faq-a">Yes. Every placement comes with a named Client Success Manager from day one. They handle onboarding, performance monitoring, backup coverage, and quarterly reviews. This is the operational backbone behind our 95%+ client retention.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-arrows-up-down-left-right"></i> Can I scale up or cancel if my needs change?</div><div class="faq-a">Yes. Add VAs as you grow, drop hours during slow seasons, or pause entirely &mdash; no locked-in headcount or termination penalties. A staffing partner, not a contract trap.</div></div>
  </div>
</section>

<!-- CTA FORM -->
<section class="sec" id="cta" style="padding-top:80px;padding-bottom:110px;">
  <div class="cta-inner reveal">
    <div style="font-size:12px;font-weight:800;color:var(--gold);text-transform:uppercase;letter-spacing:1.2px;margin-bottom:14px;text-align:center;"><i class="fa-solid fa-paper-plane"></i> Three Ways to Start</div>
    <h2 class="cta-h2" id="ctaHeading">Ready to Hire Your<br>Virtual Teammate?</h2>
    <p class="cta-sub" id="ctaSub">Talk to us, book a free practice audit, or grab the buyer&rsquo;s checklist &mdash; pick the entry point that fits where you are.</p>

    <div class="cta-intents" role="tablist" aria-label="Pick how to start">
      <button type="button" class="cta-intent on" role="tab" aria-selected="true" data-intent="strategy-call">
        <i class="fa-solid fa-calendar-check"></i>
        <strong>Free Strategy Call</strong>
        <span>30 min. Map your workflows, get a placement plan.</span>
      </button>
      <button type="button" class="cta-intent" role="tab" aria-selected="false" data-intent="practice-audit">
        <i class="fa-solid fa-clipboard-check"></i>
        <strong>20-min Practice Audit</strong>
        <span>Diagnostic only. We tell you what to outsource first.</span>
      </button>
      <button type="button" class="cta-intent" role="tab" aria-selected="false" data-intent="buyers-checklist">
        <i class="fa-solid fa-file-lines"></i>
        <strong>HIPAA VA Buyer&rsquo;s Checklist</strong>
        <span>PDF. The 22 questions to ask any VA agency.</span>
      </button>
      <button type="button" class="cta-intent" role="tab" aria-selected="false" data-intent="savings-report">
        <i class="fa-solid fa-envelope-open-text"></i>
        <strong>Full Savings Report</strong>
        <span>Emailed PDF. Role-specific ROI math for your practice.</span>
      </button>
    </div>

    <form id="ctaForm" onsubmit="event.preventDefault();this.querySelector('.cf-submit').innerHTML='Thanks! We will be in touch within 1 business day';">
      <input type="hidden" name="intent" id="ctaIntent" value="strategy-call"/>
      <div class="cf-row">
        <input class="cf-field" placeholder="First Name" required/>
        <input class="cf-field" placeholder="Last Name" required/>
      </div>
      <div class="cf-row">
        <input class="cf-field" placeholder="Email Address" type="email" required/>
        <input class="cf-field" placeholder="Phone Number" type="tel"/>
      </div>
      <input class="cf-field" placeholder="Practice / Clinic Name" style="margin-bottom:14px;" required/>
      <select class="cf-field" id="ctaRole" style="margin-bottom:20px;" required>
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
      <button class="cf-submit" type="submit" id="ctaSubmit">Get My Free Strategy Call <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note">No commitment &middot; We respond within 1 business day &middot; Covered by the 30-Day Right-Fit Promise</div>
    </form>
  </div>
</section>

</main>
<?php include 'includes/footer.php'; ?>
