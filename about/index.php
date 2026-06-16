<?php
$page_title       = 'About Virtual Teammate — Founder, Leadership & Mission | HIPAA-Certified VA Agency';
$page_description = 'Meet Virtual Teammate — US-owned, HIPAA-certified virtual assistant agency. Founder Chris McShanag brings 30+ years of operational leadership. 2,000+ VAs placed across 600+ healthcare practices and businesses.';
$og_title         = 'About Virtual Teammate — The People Behind 2,000+ VA Placements';
$og_description   = 'US-owned, HIPAA-certified VA agency. Founder Chris McShanag, leadership team, mission, values and the 5-step engagement process behind every match.';
$canonical        = 'https://virtualteammate.com/about/';
$home_base        = '../';
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
      "description":"US-owned, HIPAA-certified virtual assistant agency. Founder, leadership team, mission and engagement process.",
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
        {"@type":"Question","name":"Is Virtual Teammate US-owned?","acceptedAnswer":{"@type":"Answer","text":"Yes. Virtual Teammate is US-owned and headquartered in Arizona, with a global talent vetting network and US-based Client Success Managers on every account."}},
        {"@type":"Question","name":"Are your virtual teammates HIPAA certified?","acceptedAnswer":{"@type":"Answer","text":"Every healthcare and dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible."}},
        {"@type":"Question","name":"Where are your virtual teammates based?","acceptedAnswer":{"@type":"Answer","text":"Globally — Philippines, Latin America, Africa, and South Asia. We hire on capability and fit, not geography. Every match works your business hours."}},
        {"@type":"Question","name":"How are your virtual teammates vetted?","acceptedAnswer":{"@type":"Answer","text":"Multi-stage: application screening, EFSET English assessment, IQ test, Cultural Index, technical skills check, IT setup verification and orientation — before they ever see a client."}}
      ]
    }
  ]
}
</script>

<main>

<!-- HERO -->
<header class="svc-hero">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-building"></i> About Virtual Teammate</div>
    <h1 class="svc-h1">Built to Help Practices and Businesses <em>Do More — With Less Overhead</em></h1>
    <p class="svc-p">Virtual Teammate is a <strong>US-owned, HIPAA-certified virtual assistant agency</strong> placing skilled, EHR-trained professionals inside medical, dental, and growing-business teams across the United States. <strong>2,000+ teammates placed.</strong> <strong>600+ practices and businesses served.</strong> Every match measured on value created — not hours billed.</p>
    <div class="svc-hero-ctas">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book My Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>case-studies/" class="btn-glass">See Client KPI Results <i class="fa-solid fa-chart-line"></i></a>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise &mdash; free replacement or your money back.</div>
    </div>
    <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Reply within 1 business day</span>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-handshake"></i> 600+ Clients</div>
    <div class="hv-chip c2"><i class="fa-solid fa-users"></i> 2,000+ Placed</div>
    <div class="hv-card">
      <img src="<?= $home_base ?>images/photos/healthcare/Healthcare-Virtual-Assistants.webp" alt="Virtual Teammate team supporting healthcare practices and businesses across the US" loading="lazy"/>
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
    <h2 class="svc-h2">Chris McShanag &mdash; <em>30+ Years of Operational Leadership</em></h2>
    <p class="svc-p">&ldquo;I&rsquo;m passionate about transforming the way healthcare and business operate. With more than 30 years of experience in operations and consulting, I built Virtual Teammate to bring <em>measurable</em> value &mdash; not commodity offshore labor &mdash; to every practice and every patient touchpoint.&rdquo;</p>
    <p class="svc-p">Under Chris&rsquo;s leadership, Virtual Teammate has scaled from a boutique recruiter into a US-owned staffing partner with a global vetting network spanning the Philippines, Latin America, Africa, and South Asia. Every virtual teammate is <strong>multi-stage vetted, HIPAA-certified before placement</strong>, and matched to your time zone and tech stack.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span>30+ years operations &amp; consulting leadership</span></li>
      <li><i class="fa-solid fa-check"></i><span>Founded VT to bring value-creation culture to outsourced staffing</span></li>
      <li><i class="fa-solid fa-check"></i><span>Personally reviews new healthcare engagements</span></li>
    </ul>
    <a href="https://meetings.hubspot.com/clientsuccess/free-strategy-session" target="_blank" rel="noopener" class="btn-primary">Let&rsquo;s Connect with Chris <i class="fa-solid fa-arrow-right"></i></a>
  </div>
  <div class="svc-side-img reveal d2">
    <img src="<?= $home_base ?>images/photos/about-us/Chris-McShanag.webp" alt="Chris McShanag, Founder and CEO of Virtual Teammate" loading="lazy"/>
  </div>
</section>

<div class="divider"></div>

<!-- MISSION -->
<section class="svc-split">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-bullseye"></i> Our Mission</div>
    <h2 class="svc-h2">Bringing a <em>Value-Creation Culture</em> to Every Engagement</h2>
    <p class="svc-p">Most outsourced staffing is sold on cost. <strong>We sell on outcome.</strong> Our mission is to bring a value-creation culture to the forefront of staffing &mdash; connecting high-performing virtual teammates with the practices and businesses that need them, and measuring success on revenue cost savings and revenue growth, hours reclaimed, and patient experience improved.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Value, not just labor:</strong> every engagement is scoped against KPIs <em>you</em> choose.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>HIPAA-first by default:</strong> certified, background-checked, BAA-compatible teammates only.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>US time zone, your tools:</strong> matched to your hours, trained on your EHR/PMS.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Leadership accountability:</strong> a dedicated CSM owns every account.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2">
    <img src="<?= $home_base ?>images/photos/healthcare/How-Our-Virtual-Teammate-Help-Reduce-Costs.webp" alt="Virtual Teammate value creation in practice — measurable KPI outcomes for healthcare clients" loading="lazy"/>
  </div>
</section>

<!-- VALUES -->
<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Our Core Principles</div>
    <h2 class="svc-h2">Principles We Hire On &mdash; <em>and Live By</em></h2>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-sun"></i></span><h3>Positive Energy</h3><p>Fuels growth, productivity, and the kind of optimism that makes teams perform under pressure.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-mountain"></i></span><h3>Edge &amp; Resiliency</h3><p>A relentless spirit of possibility &mdash; people who push through, not around, hard problems.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-bullseye"></i></span><h3>Executing to Win</h3><p>Transparency and integrity at every level &mdash; material, emotional, and relational value, delivered.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-handshake-angle"></i></span><h3>Alignment of Passion</h3><p>The right teammate matched to the right client need &mdash; because mismatches are the #1 reason placements fail.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-crown"></i></span><h3>Value-Creation Leadership</h3><p>Prioritize the team and the client equally &mdash; we win when both win.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Trust &amp; Ethical Decision-Making</h3><p>Keeping commitments with kindness and accountability &mdash; and principled decisions in every gray area.</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- WHY VT -->
<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-medal"></i> Why Practices and Businesses Choose VT</div>
    <h2 class="svc-h2">More Than Staffing &mdash; A <em>Partner That Owns Outcomes</em></h2>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>HIPAA by Default</h3><p>Every teammate completes HIPAA certification and BAA-compatible setup before placement.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-globe"></i></span><h3>Global Talent Pool</h3><p>Recruited across the Philippines, Latin America, Africa, and South Asia &mdash; vetted at every stage.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-clock"></i></span><h3>Your Time Zone</h3><p>Every teammate works your business hours, with backup coverage built in.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-chart-line"></i></span><h3>Real KPI Reporting</h3><p>Monthly client KPI scorecards &mdash; claims worked, payments posted, calls answered, AR collected.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span><h3>Dedicated CSM</h3><p>A US-based Client Success Manager (CSM) owns every engagement &mdash; performance, training, escalation.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Up to 73% Cost Savings</h3><p>Flat-rate, transparent pricing &mdash; no hidden fees, no recruiter spread, no surprises.</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- PROCESS / FUNNEL -->
<section class="svc-proc">
  <div style="text-align:center;max-width:720px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> How We Engage</div>
    <h2 class="svc-h2">Five Steps from First Call to <em>First Win</em></h2>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-magnifying-glass pstep-ico"></i></div><h3 class="pstep-title">Pinpoint Goals</h3><p class="pstep-desc">A 30-minute strategy session: surface the bottleneck, the KPI gap, and the workflow you&rsquo;d hand off first.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-lightbulb pstep-ico"></i></div><h3 class="pstep-title">Discover the Fit</h3><p class="pstep-desc">We map your needs to the right role spec &mdash; medical, dental, admin, sales, or back-office.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Match Your Teammate</h3><p class="pstep-desc">Curated shortlist within days. You interview, you choose &mdash; no commitment until the right candidate.</p></div>
    <div class="pstep reveal d4"><div class="pstep-head"><div class="pstep-num">04</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">Integrate</h3><p class="pstep-desc">A tailored 1&ndash;2 week integration plan: tools, EHR access, workflows, communication cadence.</p></div>
    <div class="pstep reveal d5"><div class="pstep-head"><div class="pstep-num">05</div><i class="fa-solid fa-chart-line pstep-ico"></i></div><h3 class="pstep-title">Track &amp; Scale</h3><p class="pstep-desc">Monthly KPI scorecard &mdash; and when results land, scale the engagement with confidence.</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> About VT &mdash; FAQs</div><h2 class="svc-h2">Frequently Asked Questions</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-building"></i> Is Virtual Teammate US-owned?</div><div class="faq-a">Yes. Virtual Teammate is US-owned and headquartered in Arizona, with a global talent vetting network and US-based Client Success Managers (CSMs) on every account.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Are your teammates HIPAA certified?</div><div class="faq-a">Every healthcare and dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your teammates based?</div><div class="faq-a">Globally &mdash; Philippines, Latin America, Africa, South Asia. We hire on capability and fit, not geography. Every match works your business hours.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-handshake"></i> How are teammates vetted?</div><div class="faq-a">Multi-stage: application screening, EFSET English assessment, IQ test, Cultural Index, technical skills check, IT setup verification, and orientation &mdash; before they ever see a client.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-chart-line"></i> Do you actually measure results?</div><div class="faq-a">Yes &mdash; monthly KPI scorecards on the workstreams you choose (claims worked, AR days, calls answered, payment posting, intake completion). <a href="<?= $home_base ?>case-studies/">See real client KPI results &raquo;</a></div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> Who do I talk to first?</div><div class="faq-a">Book a value strategy session &mdash; you&rsquo;ll talk to a senior VT leader (often Chris himself for new engagements) and walk out with a clear scope, a price, and a timeline.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
