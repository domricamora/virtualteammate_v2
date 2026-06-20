<?php
$page_title       = 'About Virtual Teammate — Founder, Leadership & Mission | HIPAA-Compliant VA Agency';
$page_description = 'Meet Virtual Teammate — US-owned, HIPAA-compliant virtual assistant agency. Founder Chris McShanag brings 30+ years of operational leadership. 2,000+ VAs placed across 600+ healthcare practices and businesses.';
$og_title         = 'About Virtual Teammate — The People Behind 2,000+ VA Placements';
$og_description   = 'US-owned, HIPAA-compliant VA agency. Founder Chris McShanag, leadership team, mission, values and the 5-step engagement process behind every match.';
$canonical        = 'https://virtualteammate.com/about/';
$home_base        = '../';
$has_cta_section  = true;   // uses the homepage "Ways to Start" #cta block
$breadcrumbs      = [
  ['name' => 'Home',  'url' => '/'],
  ['name' => 'About', 'url' => '/about/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@graph":[
    {
      "@type":"AboutPage",
      "@id":"https://virtualteammate.com/about/#aboutpage",
      "url":"https://virtualteammate.com/about/",
      "name":"About Virtual Teammate",
      "description":"US-owned, HIPAA-compliant virtual assistant agency. Founder, leadership team, mission and engagement process.",
      "isPartOf":{"@id":"https://virtualteammate.com/#website"},
      "about":{"@id":"https://virtualteammate.com/#org"}
    },
    {
      "@type":"Organization",
      "@id":"https://virtualteammate.com/#org",
      "name":"Virtual Teammate",
      "url":"https://virtualteammate.com/",
      "logo":"https://virtualteammate.com/images/logo.webp",
      "founder":{
        "@type":"Person",
        "name":"Chris McShanag",
        "jobTitle":"Founder and CEO",
        "worksFor":{"@id":"https://virtualteammate.com/#org"},
        "description":"Founder and CEO of Virtual Teammate with 30+ years of experience in operations and consulting across healthcare and business."
      },
      "employee":[
        {"@type":"Person","name":"Eunice Escano","jobTitle":"Finance Director"},
        {"@type":"Person","name":"Judy Anne Lim","jobTitle":"IT Director"},
        {"@type":"Person","name":"Kirsten Jillianne Tagle","jobTitle":"Marketing Director"},
        {"@type":"Person","name":"May Martin","jobTitle":"HR & Talent Acquisition Director"}
      ],
      "numberOfEmployees":{"@type":"QuantitativeValue","value":2000,"unitText":"virtual teammates placed"},
      "areaServed":["US","CA","GB","AU"]
    },
    {
      "@type":"FAQPage",
      "mainEntity":[
        {"@type":"Question","name":"Is Virtual Teammate US-owned?","acceptedAnswer":{"@type":"Answer","text":"Yes. Virtual Teammate is US-owned and headquartered in Arizona, with a global talent vetting network and Dedicated Client Success Managers on every account."}},
        {"@type":"Question","name":"Are your virtual teammates HIPAA compliant?","acceptedAnswer":{"@type":"Answer","text":"Every healthcare and dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible."}},
        {"@type":"Question","name":"Where are your virtual teammates based?","acceptedAnswer":{"@type":"Answer","text":"Globally — Philippines, Latin America, Africa, and South Asia. We hire on capability and fit, not geography. Every match works your business hours."}},
        {"@type":"Question","name":"How are your virtual teammates vetted?","acceptedAnswer":{"@type":"Answer","text":"Multi-stage: application screening, EFSET English assessment, IQ test, Cultural Index, technical skills check, IT setup verification and orientation — before they ever see a client."}}
      ]
    }
  ]
}
</script>

<style>
/* About page — eyebrows follow their header's alignment (the global rule hard-
   centers .sec-lbl, which mismatched the left-aligned svc-h1/svc-h2 sections). */
.sec-lbl{text-align:inherit;}

/* Graphic info panels — self-contained glass cards sized to their content (no
   forced tall photo frame), VT glass/gold style. */
.ab-panel{display:flex;flex-direction:column;gap:13px;padding:28px 26px;border-radius:20px;
  background:linear-gradient(150deg,rgba(57,25,186,.5),rgba(20,15,55,.9) 55%,rgba(223,169,73,.16));
  border:1px solid rgba(223,169,73,.32);box-shadow:0 24px 60px rgba(0,0,0,.4);}
.ab-panel-h{display:flex;align-items:center;gap:11px;font-size:15px;font-weight:800;color:#fff;line-height:1.25;margin-bottom:2px;}
.ab-panel-h i{color:var(--gold,#dfa949);font-size:17px;}
.ab-row{display:flex;align-items:flex-start;gap:13px;}
.ab-row .ic{flex:0 0 38px;width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;
  background:rgba(223,169,73,.14);border:1px solid rgba(223,169,73,.4);color:var(--gold,#dfa949);font-size:15px;}
.ab-row .tx{font-size:13.5px;line-height:1.4;color:rgba(255,255,255,.82);}
.ab-row .tx strong{color:#fff;display:block;font-size:14px;margin-bottom:1px;}
.ab-panel-foot{margin-top:4px;border-top:1px solid rgba(255,255,255,.12);padding-top:15px;}
.ab-panel-foot .n{font-size:26px;font-weight:800;color:var(--gold,#dfa949);letter-spacing:-.02em;line-height:1;}
.ab-panel-foot .l{font-size:11.5px;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.6);font-weight:700;margin-top:5px;}

/* Founder portrait — shown at native ratio (no crop, no upscale), aligned in
   its column. The image is 433x577, so capping at 400px keeps it crisp. */
.ab-vis{align-self:center;}
.ab-vis--photo{display:flex;justify-content:center;}
.ab-vis--photo img{width:100%;max-width:560px;height:auto;border-radius:20px;
  border:1px solid var(--glass-border);box-shadow:0 24px 60px rgba(0,0,0,.4);display:block;}
/* Preserve the zig-zag: panel/photo goes first in the reversed mission row. */
.svc-split.reverse .ab-vis{order:-1;}
@media(max-width:1024px){.svc-split.reverse .ab-vis{order:0;}}
</style>

<main>

<!-- HERO -->
<header class="svc-hero">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-building"></i> About Virtual Teammate</div>
    <h1 class="svc-h1">Built to enable practices to <em>achieve more, with less overhead costs</em></h1>
    <p class="svc-p">Virtual Teammate is a <strong>US-owned, HIPAA-compliant virtual assistant agency</strong> placing skilled, EHR-trained professionals inside medical, dental, and growing-business teams across the United States. <strong>2,000+ teammates placed.</strong> <strong>600+ practices and businesses served.</strong> Every match measured on value created, not hours billed.</p>
    <div class="svc-hero-ctas">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>case-studies/" class="btn-glass">See Client KPI Results <i class="fa-solid fa-chart-line"></i></a>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
    </div>
    <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Reply within 1 business day</span>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="ab-panel" role="img" aria-label="Virtual Teammate at a glance: US-owned, HIPAA-compliant, global vetting network, measured on value">
      <div class="ab-panel-h"><i class="fa-solid fa-building-shield"></i> Virtual Teammate, at a glance</div>
      <div class="ab-row"><span class="ic"><i class="fa-solid fa-flag-usa"></i></span><span class="tx"><strong>US-owned</strong>Headquartered in Phoenix, Arizona</span></div>
      <div class="ab-row"><span class="ic"><i class="fa-solid fa-shield-halved"></i></span><span class="tx"><strong>HIPAA-compliant</strong>Certified &amp; BAA-ready before placement</span></div>
      <div class="ab-row"><span class="ic"><i class="fa-solid fa-globe"></i></span><span class="tx"><strong>Global vetting network</strong>PH &middot; LATAM &middot; Africa &middot; S. Asia</span></div>
      <div class="ab-row"><span class="ic"><i class="fa-solid fa-chart-line"></i></span><span class="tx"><strong>Measured on value</strong>Monthly KPI scorecards, not hours billed</span></div>
    </div>
  </div>
</header>

<!-- STATS -->
<div class="svc-stats reveal">
  <div class="svc-stat"><div class="svc-stat-num">2,000+</div><div class="svc-stat-lbl">Virtual Teammates Placed</div></div>
  <div class="svc-stat"><div class="svc-stat-num">600+</div><div class="svc-stat-lbl">Practices &amp; Businesses Served</div></div>
  <div class="svc-stat"><div class="svc-stat-num">30+</div><div class="svc-stat-lbl">Years Operational Leadership</div></div>
  <div class="svc-stat"><div class="svc-stat-num">4</div><div class="svc-stat-lbl">Countries Served (US/CA/GB/AU)</div></div>
</div>

<!-- FOUNDER -->
<section class="svc-split">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-user-tie"></i> Meet the Founder</div>
    <h2 class="svc-h2">Chris McShanag: <em>30+ years of operational leadership</em></h2>
    <p class="svc-p">&ldquo;I&rsquo;m passionate about transforming the way healthcare and business operate. With more than 30 years of experience in operations and consulting, I built Virtual Teammate to bring <em>measurable</em> value, not commodity offshore labor, to every practice and every patient touchpoint.&rdquo;</p>
    <p class="svc-p">Under Chris&rsquo;s leadership, Virtual Teammate has scaled from a boutique recruiter into a US-owned staffing partner with a global vetting network spanning the Philippines, Latin America, Africa, and South Asia. Every virtual teammate is <strong>multi-stage vetted, HIPAA-compliant before placement</strong>, and matched to your time zone and tech stack.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span>30+ years operations &amp; consulting leadership</span></li>
      <li><i class="fa-solid fa-check"></i><span>Founded VT to bring value-creation culture to outsourced staffing</span></li>
      <li><i class="fa-solid fa-check"></i><span>Personally reviews new healthcare engagements</span></li>
    </ul>
    <a href="https://meetings.hubspot.com/clientsuccess/free-strategy-session" target="_blank" rel="noopener" class="btn-primary">Let&rsquo;s Connect with Chris <i class="fa-solid fa-arrow-right"></i></a>
  </div>
  <div class="ab-vis ab-vis--photo reveal d2">
    <img src="<?= $home_base ?>images/chris.webp" alt="Chris McShanag, Founder and CEO of Virtual Teammate" width="900" height="675" loading="lazy"/>
  </div>
</section>

<div class="divider"></div>

<!-- MISSION -->
<section class="svc-split reverse">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-bullseye"></i> Our Mission</div>
    <h2 class="svc-h2">Bringing a <em>value-creation culture</em> to every engagement</h2>
    <p class="svc-p">Most outsourced staffing is sold on cost. <strong>We sell on outcome.</strong> Our mission is to bring a value-creation culture to the forefront of staffing: connecting high-performing virtual teammates with the practices and businesses that need them, and measuring success on revenue cost savings and revenue growth, hours reclaimed, and patient experience improved.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Value, not just labor:</strong> every engagement is scoped against KPIs <em>you</em> choose.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>HIPAA-first by default:</strong> certified, background-checked, BAA-compatible teammates only.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>US time zone, your tools:</strong> matched to your hours, trained on your EHR/PMS.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Leadership accountability:</strong> a dedicated CSM owns every account.</span></li>
    </ul>
  </div>
  <div class="ab-vis reveal d2">
    <div class="ab-panel" role="img" aria-label="We measure on outcomes: cost savings and revenue growth, hours reclaimed, patient experience improved">
      <div class="ab-panel-h"><i class="fa-solid fa-bullseye"></i> Measured on outcomes, not hours</div>
      <div class="ab-row"><span class="ic"><i class="fa-solid fa-sack-dollar"></i></span><span class="tx"><strong>Cost savings &amp; revenue growth</strong>Up to 73% vs an in-house hire</span></div>
      <div class="ab-row"><span class="ic"><i class="fa-solid fa-clock"></i></span><span class="tx"><strong>Hours reclaimed</strong>Back to patients and high-value work</span></div>
      <div class="ab-row"><span class="ic"><i class="fa-solid fa-heart-pulse"></i></span><span class="tx"><strong>Patient experience improved</strong>Faster response, cleaner workflows</span></div>
      <div class="ab-panel-foot"><div class="n">100%</div><div class="l">Scoped to the KPIs you choose</div></div>
    </div>
  </div>
</section>

<!-- VALUES -->
<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Our Core Principles</div>
    <h2 class="svc-h2">Principles we hire on, <em>and live by</em></h2>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-sun"></i></span><h3>Positive energy</h3><p>Fuels growth, productivity, and the kind of optimism that makes teams perform under pressure.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-mountain"></i></span><h3>Edge &amp; resiliency</h3><p>A relentless spirit of possibility: people who push through, not around, hard problems.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-bullseye"></i></span><h3>Executing to win</h3><p>Transparency and integrity at every level: material, emotional, and relational value, delivered.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-handshake-angle"></i></span><h3>Alignment of passion</h3><p>The right teammate matched to the right client need, because mismatches are the #1 reason placements fail.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-crown"></i></span><h3>Value-creation leadership</h3><p>Prioritize the team and the client equally: we win when both win.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Trust &amp; ethical decision-making</h3><p>Keeping commitments with kindness and accountability, and principled decisions in every gray area.</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- WHY VT -->
<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-medal"></i> Why Practices Choose VT</div>
    <h2 class="svc-h2">More than staffing: <em>a partner that owns outcomes</em></h2>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>HIPAA compliant</h3><p>Every teammate is HIPAA compliant and BAA-compatible setup before placement.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-globe"></i></span><h3>Global talent pool</h3><p>Recruited across the Philippines, Latin America, Africa, and South Asia, vetted at every stage.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-clock"></i></span><h3>Your time zone</h3><p>Every teammate works your business hours, with backup coverage built in.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-chart-line"></i></span><h3>Real KPI reporting</h3><p>Monthly client KPI scorecards: claims worked, payments posted, calls answered, AR collected.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span><h3>Dedicated CSM</h3><p>A Dedicated Client Success Manager (CSM) owns every engagement: performance, training, escalation.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Up to 73% cost savings</h3><p>Flat-rate, transparent pricing: no hidden fees, no recruiter spread, no surprises.</p></div>
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
    <a href="<?= $audit_modal ?>" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
    <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
  </div>
</section>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> About VT, FAQs</div><h2 class="svc-h2">Frequently asked questions</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-building"></i> Is Virtual Teammate US-owned?</div><div class="faq-a">Yes. Virtual Teammate is US-owned and headquartered in Arizona, with a global talent vetting network and Dedicated Client Success Managers (CSMs) on every account.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Are your teammates HIPAA compliant?</div><div class="faq-a">Every healthcare and dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your teammates based?</div><div class="faq-a">Globally: Philippines, Latin America, Africa, South Asia. We hire on capability and fit, not geography. Every match works your business hours.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-handshake"></i> How are teammates vetted?</div><div class="faq-a">Multi-stage: application screening, EFSET English assessment, IQ test, Cultural Index, technical skills check, IT setup verification, and orientation: before they ever see a client.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-chart-line"></i> Do you actually measure results?</div><div class="faq-a">Yes: monthly KPI scorecards on the workstreams you choose (claims worked, AR days, calls answered, payment posting, intake completion). <a href="<?= $home_base ?>case-studies/">See real client KPI results &raquo;</a></div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> Who do I talk to first?</div><div class="faq-a">Book a value strategy session: you&rsquo;ll talk to a senior VT leader (often Chris himself for new engagements) and walk out with a clear scope, a price, and a timeline.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../includes/cta-stages.php'; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
