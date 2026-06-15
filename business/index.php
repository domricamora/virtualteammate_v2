<?php
$page_title       = 'Business & Non-Profit Virtual Assistants | Virtual Teammate';
$page_description = 'Virtual Teammate also staffs admin, sales, marketing, finance and customer-service VAs for businesses and non-profits. Same global network, same vetting, same flat-rate model.';
$og_title         = 'Business & Non-Profit Virtual Assistants — Virtual Teammate';
$og_description   = 'Admin, sales, marketing, finance & CX virtual assistants for businesses & non-profits — sourced globally, delivered in your time zone.';
$canonical        = 'https://virtualteammate.com/business/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',                       'url' => '/'],
  ['name' => 'Business & Non-Profit VAs',  'url' => '/business/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<script type="application/ld+json">
{
  "@context":"https://schema.org","@type":"Service",
  "serviceType":"Business Virtual Assistant Staffing",
  "name":"Business & Non-Profit Virtual Assistants",
  "description":"Administrative, sales, marketing, finance and customer-service virtual assistants for businesses and non-profit organizations — sourced from a global vetted network, matched to US time zones, billed at a transparent flat rate.",
  "provider":{"@type":"Organization","name":"Virtual Teammate","url":"https://virtualteammate.com/"},
  "areaServed":["US","CA","GB","AU"]
}
</script>
<script type="application/ld+json">
{
  "@context":"https://schema.org","@type":"FAQPage",
  "mainEntity":[
    {"@type":"Question","name":"What does a business virtual assistant do?","acceptedAnswer":{"@type":"Answer","text":"A business VA handles the repeatable work across sales, marketing, admin, finance and customer service — from lead generation, appointment setting and CRM management to bookkeeping, inbox control and tier-1 support. You decide the function; we match a vetted teammate trained on your tools and time zone."}},
    {"@type":"Question","name":"Can a virtual assistant generate leads for my business?","acceptedAnswer":{"@type":"Answer","text":"Yes. Our lead-generation and SDR virtual assistants build and enrich target lists, run outbound email and LinkedIn prospecting, qualify inbound enquiries and book meetings directly onto your reps' calendars — keeping top-of-funnel volume consistent without a full-time sales hire."}},
    {"@type":"Question","name":"How do virtual assistants help with client acquisition?","acceptedAnswer":{"@type":"Answer","text":"They remove the bottlenecks that lose deals: instant speed-to-lead response, disciplined follow-up sequences, clean CRM data, and steady marketing output. Combined, they keep every lead moving through the pipeline so more conversations turn into clients."}},
    {"@type":"Question","name":"Which CRMs and sales tools do your VAs work in?","acceptedAnswer":{"@type":"Answer","text":"Our teammates work daily in HubSpot, Salesforce, Pipedrive, Zoho and the rest of the common stack — plus outreach tools, email platforms, scheduling apps and the project tools your team already runs. We match for tool fluency during selection."}},
    {"@type":"Question","name":"How much does a business virtual assistant cost?","acceptedAnswer":{"@type":"Answer","text":"Transparent flat-rate pricing — typically 60-73% less than an equivalent in-house hire once salary, benefits, payroll tax and overhead are included. No recruiter fees, no long-term lock-in."}},
    {"@type":"Question","name":"How fast can I onboard a sales or marketing VA?","acceptedAnswer":{"@type":"Answer","text":"Most clients receive a curated shortlist within days and have their teammate live in 1-2 weeks — every placement backed by the 30-Day Right-Fit Promise."}}
  ]
}
</script>

<main>
<header class="svc-hero">
  <div class="orb orb1"></div><div class="orb orb2"></div>
  <div class="svc-hero-inner reveal">
    <nav class="svc-bc" aria-label="Breadcrumb">
      <a href="<?= $home_base ?>">Home</a>
      <i class="fa-solid fa-chevron-right"></i>
      <span aria-current="page">Business &amp; Non-Profit VAs</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-briefcase"></i> Business &amp; Non-Profit VAs</div>
    <h1 class="svc-h1">Beyond Healthcare: <em>Virtual Teammates</em> for Every Function</h1>
    <p class="svc-lead">Healthcare is our focus, but our network covers every back-office role a growing business or non-profit needs. <strong>Same global talent network, same multi-stage vetting, same transparent flat-rate model</strong> &mdash; matched to your US time zone and backed by the 30-Day Right-Fit Promise.</p>
    <div class="svc-trust">
      <div class="trust-item"><i class="fa-solid fa-globe"></i> 12+ Countries Sourced</div>
      <div class="trust-item"><i class="fa-solid fa-user-shield"></i> Background Checked</div>
      <div class="trust-item"><i class="fa-solid fa-rotate"></i> 30-Day Right-Fit Promise</div>
      <div class="trust-item"><i class="fa-solid fa-bolt"></i> Live in 1&ndash;2 Weeks</div>
    </div>
    <div class="svc-cta-row">
      <a href="#cta-ops-assessment" class="btn-primary" data-cta-intent="ops-assessment">Schedule My Operational Assessment <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#cta-buyback" class="btn-glass" data-cta-intent="buyback">Buy Back Your Company&rsquo;s Time <i class="fa-solid fa-clock"></i></a>
    </div>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-circle-check"></i> Multi-Stage Vetted</div>
    <div class="hv-chip c2"><i class="fa-solid fa-clock"></i> Your Time Zone</div>
    <div class="hv-card">
      <img src="<?= $home_base ?>images/photos/business/email-management.webp" alt="Business virtual assistant managing a client inbox and email workflows" width="760" height="1139" loading="lazy"/>
    </div>
  </div>
</header>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-list-check"></i> Roles We Staff</div>
    <h2 class="svc-h2">Pick a Function &mdash; <em>We&rsquo;ll Build the Bench</em></h2>
    <p class="sec-sub" style="max-width:680px;margin:0 auto;">From a single executive assistant to a full remote operations team, the same vetting standard that produces our HIPAA-certified healthcare placements applies here.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-clipboard"></i></span><h3>Administrative</h3><p>Executive assistants, calendar &amp; inbox managers, project coordinators, data entry, document control.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-bullseye"></i></span><h3>Sales &amp; SDR</h3><p>Inbound qualification, outbound prospecting, list research, CRM hygiene, appointment setting, follow-up sequences.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-bullhorn"></i></span><h3>Marketing</h3><p>Social-media management, content production, email automation, SEO assistants, paid-ad coordination, analytics reporting.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Finance &amp; Bookkeeping</h3><p>QuickBooks/Xero bookkeeping, AR &amp; AP, expense management, invoicing, budget reporting, payroll prep.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-headset"></i></span><h3>Customer Service</h3><p>Tier-1 support, ticket triage, live chat, refund &amp; returns, escalation handling &mdash; in your time zone, in your tone.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-hand-holding-heart"></i></span><h3>Non-Profit Operations</h3><p>Donor outreach, grant research, volunteer coordination, event logistics, social campaigns, board-meeting prep.</p></div>
  </div>
</section>

<!-- ROLE DEEP-DIVE — long-form SEO content framed around lead generation
     and client acquisition. Each role explains how a Virtual Teammate moves
     the pipeline, not just which tasks it covers. -->
<section class="svc-bens" id="business-roles">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-arrow-trend-up"></i> Roles That Drive Growth</div>
    <h2 class="svc-h2">Business Virtual Assistants Built Around <em>Lead Generation &amp; Client Acquisition</em></h2>
    <p class="sec-sub" style="max-width:760px;margin:0 auto;">Most growing companies don&rsquo;t lose deals because the product is weak &mdash; they lose them because no one had time to follow up, qualify the inbound, or keep the pipeline moving. A Virtual Teammate takes the repeatable revenue work off your plate so your closers stay closing. Here&rsquo;s how each role compounds into more leads and more clients.</p>
  </div>

  <div class="biz-deep">
    <article class="reveal d1">
      <h3><i class="fa-solid fa-bullseye"></i> Sales Development &amp; Lead Generation VAs</h3>
      <p>A dedicated <strong>lead generation virtual assistant</strong> builds and cleans your target lists, runs outbound prospecting across email and LinkedIn, qualifies inbound enquiries, and books meetings straight onto your reps&rsquo; calendars. Instead of senior salespeople burning hours on research and cold outreach, they walk into sales-qualified conversations that are ready to close.</p>
      <p>For most teams, a single SDR virtual assistant is the fastest, lowest-risk way to keep top-of-funnel volume consistent &mdash; without a recruiter fee or a $70k base salary.</p>
      <ul><li>Outbound prospecting</li><li>List building &amp; enrichment</li><li>Appointment setting</li><li>Lead qualification</li><li>Follow-up sequences</li></ul>
    </article>

    <article class="reveal d2">
      <h3><i class="fa-solid fa-bullhorn"></i> Marketing &amp; Demand-Generation VAs</h3>
      <p>A <strong>marketing virtual assistant</strong> keeps the demand engine running: publishing social content, building and scheduling email nurture campaigns, updating landing pages, coordinating paid ads, and packaging the SEO and analytics work that turns traffic into tracked leads. They make sure every campaign actually ships on time instead of sitting half-finished.</p>
      <p>The result is a steady flow of inbound interest feeding the same pipeline your sales VAs are working &mdash; a compounding client-acquisition loop rather than one-off bursts.</p>
      <ul><li>Social media management</li><li>Email marketing &amp; nurture</li><li>SEO &amp; content support</li><li>Paid-ad coordination</li><li>Landing pages &amp; analytics</li></ul>
    </article>

    <article class="reveal d3">
      <h3><i class="fa-solid fa-bolt"></i> Inbound &amp; Speed-to-Lead VAs</h3>
      <p>Studies consistently show the first business to respond wins the majority of deals &mdash; yet most inbound leads wait hours for a reply. A time-zone-aligned Virtual Teammate answers live chat, web forms and inbound calls within minutes, qualifies the prospect, and books the meeting before your competitor even sees the lead. <strong>Speed-to-lead</strong> becomes a system, not a scramble.</p>
      <ul><li>Live chat &amp; web-form response</li><li>Inbound qualification</li><li>Instant meeting booking</li><li>Lead routing</li></ul>
    </article>

    <article class="reveal d4">
      <h3><i class="fa-solid fa-diagram-project"></i> CRM &amp; Pipeline-Management VAs</h3>
      <p>Pipelines leak when records are stale and follow-ups slip. A <strong>CRM virtual assistant</strong> keeps HubSpot, Salesforce or your pipeline of choice clean and current &mdash; logging activity, advancing stages, triggering follow-up tasks, and surfacing the deals going cold. Your reps trust the data, forecasting gets accurate, and no warm lead falls through the cracks.</p>
      <ul><li>CRM data hygiene</li><li>Pipeline &amp; stage updates</li><li>Deal &amp; activity logging</li><li>Reporting &amp; dashboards</li></ul>
    </article>

    <article class="reveal d5">
      <h3><i class="fa-solid fa-clipboard"></i> Executive &amp; Administrative VAs</h3>
      <p>Every hour a founder or sales leader spends on inbox triage and calendar Tetris is an hour not spent winning clients. An <strong>executive virtual assistant</strong> owns scheduling, inbox management, travel, document prep and project coordination &mdash; handing your highest-value people back the time to sell, partner and close.</p>
      <ul><li>Inbox &amp; calendar management</li><li>Meeting &amp; travel coordination</li><li>Document control</li><li>Project &amp; task tracking</li></ul>
    </article>

    <article class="reveal d6">
      <h3><i class="fa-solid fa-sack-dollar"></i> Finance &amp; Bookkeeping VAs</h3>
      <p>Acquisition stalls when cash is tied up in unpaid invoices. A finance VA runs QuickBooks/Xero bookkeeping, invoicing, and disciplined AR follow-up so revenue lands faster and predictably &mdash; freeing working capital to reinvest in the marketing and sales that bring the next client in.</p>
      <ul><li>Bookkeeping (QuickBooks / Xero)</li><li>Invoicing &amp; AR follow-up</li><li>AP &amp; expense management</li><li>Budget &amp; cash reporting</li></ul>
    </article>

    <article class="reveal d1">
      <h3><i class="fa-solid fa-headset"></i> Customer-Success &amp; Retention VAs</h3>
      <p>The cheapest client to acquire is the one you keep. A customer-service Virtual Teammate handles tier-1 support, ticket triage, onboarding follow-up and renewal reminders in your tone and time zone &mdash; lifting satisfaction, reviews and referrals that feed straight back into new client acquisition.</p>
      <ul><li>Tier-1 support &amp; live chat</li><li>Onboarding follow-up</li><li>Renewal &amp; retention outreach</li><li>Review &amp; referral requests</li></ul>
    </article>

    <article class="reveal d2">
      <h3><i class="fa-solid fa-hand-holding-heart"></i> Non-Profit Development VAs</h3>
      <p>For mission-driven teams, &ldquo;client acquisition&rdquo; means donors, members and grants. A non-profit Virtual Teammate runs donor prospecting and outreach, grant research and submissions, fundraising-campaign support and volunteer coordination &mdash; the same pipeline discipline, pointed at growing your supporter base.</p>
      <ul><li>Donor prospecting &amp; outreach</li><li>Grant research &amp; submissions</li><li>Fundraising campaign support</li><li>Volunteer &amp; event coordination</li></ul>
    </article>
  </div>
</section>

<?php
  // Full business bench — every non-clinical role (excludes Medical & Dental),
  // with search + department + skill filters and a load-more button.
  $vtc_exclude_depts = ['Medical', 'Dental'];
  $vtc_filterable    = true;
  $vtc_page          = 9;
  $vtc_label         = 'Meet the Bench';
  $vtc_heading       = 'Business-Ready <em>Virtual Teammates</em>';
  $vtc_sub           = 'Browse our real, vetted business teammates across admin, sales, marketing, finance, customer-service and more. Filter by department or skill, then request the one that fits &mdash; matched to your time zone and ready in 1&ndash;2 weeks.';
  $vtc_cta_href      = '#cta-buyback';
  $vtc_cta_intent    = 'buyback';
  $vtc_cta_label     = 'Request this teammate';
  $vtc_cta_vt        = true;   // prefill the on-page Buy-Back form
  include __DIR__ . '/../includes/vt-cards.php';
?>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-circle-question"></i> FAQ</div>
    <h2 class="svc-h2">Business Virtual Assistant <em>Questions</em></h2>
  </div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-briefcase"></i> What does a business virtual assistant do?</div><div class="faq-a">A business VA handles the repeatable work across sales, marketing, admin, finance and customer service &mdash; from lead generation, appointment setting and CRM management to bookkeeping, inbox control and tier-1 support. You decide the function; we match a vetted teammate trained on your tools and time zone.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-bullseye"></i> Can a virtual assistant generate leads for my business?</div><div class="faq-a">Yes. Our lead-generation and SDR virtual assistants build and enrich target lists, run outbound email and LinkedIn prospecting, qualify inbound enquiries and book meetings directly onto your reps&rsquo; calendars &mdash; keeping top-of-funnel volume consistent without a full-time sales hire.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-handshake"></i> How do virtual assistants help with client acquisition?</div><div class="faq-a">They remove the bottlenecks that lose deals: instant speed-to-lead response, disciplined follow-up sequences, clean CRM data, and steady marketing output. Combined, they keep every lead moving through the pipeline so more conversations turn into clients.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-screwdriver-wrench"></i> Which CRMs and sales tools do your VAs work in?</div><div class="faq-a">Our teammates work daily in HubSpot, Salesforce, Pipedrive, Zoho and the rest of the common stack &mdash; plus outreach tools, email platforms, scheduling apps and the project tools your team already runs. We match for tool fluency during selection.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a business virtual assistant cost?</div><div class="faq-a">Transparent flat-rate pricing &mdash; typically 60&ndash;73% less than an equivalent in-house hire once salary, benefits, payroll tax and overhead are included. No recruiter fees, no long-term lock-in.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-bolt"></i> How fast can I onboard a sales or marketing VA?</div><div class="faq-a">Most clients receive a curated shortlist within days and have their teammate live in 1&ndash;2 weeks &mdash; every placement backed by the 30-Day Right-Fit Promise.</div></div>
  </div>
</section>

<section class="sec cta-stages-section" id="cta" style="padding-top:80px;padding-bottom:110px;">
  <div class="cta-stages-h reveal">
    <div class="sec-lbl"><i class="fa-solid fa-paper-plane"></i> Two Ways to Start</div>
    <h2 class="cta-h2" style="font-size:36px;">Healthcare or Not,<br>the Engine Is the Same</h2>
    <p class="cta-sub">Diagnose where the hours are going, or jump straight to reclaiming them &mdash; same vetted global network, same dedicated Client Success Manager (CSM), same 30-Day Right-Fit Promise.</p>
  </div>

  <div class="cta-stages-grid cta-stages-2 reveal d1">
    <article class="cta-stage" data-cta-intent="ops-assessment">
      <div class="cta-stage-tag">Ready to diagnose</div>
      <span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span>
      <h3>Operational Assessment</h3>
      <p class="cta-stage-lead">A diagnostic-only call. We map your busiest back-office workflows and tell you which functions to delegate first.</p>
      <ul class="cta-stage-list">
        <li>Workflow inventory across departments</li>
        <li>Ranked outsourcing-priority list</li>
        <li>Role + headcount recommendation</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-ops-assessment" data-cta-intent="ops-assessment">Schedule My Operational Assessment <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-high" data-cta-intent="buyback">
      <div class="cta-stage-tag">Ready to delegate</div>
      <span class="ico-circle lg"><i class="fa-solid fa-hourglass-half"></i></span>
      <h3>Buy Back Your Company&rsquo;s Time</h3>
      <p class="cta-stage-lead">Tell us the function eating your team&rsquo;s week. We&rsquo;ll build the bench and have a vetted teammate live in 1&ndash;2 weeks.</p>
      <ul class="cta-stage-list">
        <li>Curated shortlist within days</li>
        <li>Interview before you decide</li>
        <li>Transparent flat-rate pricing</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-buyback" data-cta-intent="buyback">Buy Back Your Company&rsquo;s Time <i class="fa-solid fa-arrow-right"></i></a>
    </article>
  </div>

  <p class="cta-stages-foot reveal">Prefer to talk it through? <a href="#cta-ops-assessment" data-cta-intent="ops-assessment">Schedule My Operational Assessment</a> and a Client Success Manager (CSM) will map it out with you.</p>
</section>

<!-- ENTRY-POINT MODALS — one tailored form per funnel stage. Opened via the
     #cta-<intent> hash (CSS :target, so they work with JS off too); the script
     below adds scroll-lock, autofocus and ESC-to-close. Each posts to lead.php
     and creates a lead. -->
<div class="cta-modal" id="cta-ops-assessment" role="dialog" aria-modal="true" aria-labelledby="bcm-oa-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-clipboard-check"></i> Operational Assessment</div>
    <h2 class="cta-modal-h" id="bcm-oa-h">Schedule Your Operational Assessment</h2>
    <p class="cta-modal-sub">Pick a time that works for you &mdash; a US-based Client Success Manager will map your busiest back-office workflows and tell you exactly what to delegate first. Diagnostic only, no obligation.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>

<div class="cta-modal" id="cta-buyback" role="dialog" aria-modal="true" aria-labelledby="bcm-bb-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-hourglass-half"></i> Buy Back Your Time</div>
    <h2 class="cta-modal-h" id="bcm-bb-h">Buy Back Your Company&rsquo;s Time</h2>
    <p class="cta-modal-sub">Pick a time below and we&rsquo;ll scope the function eating your team&rsquo;s week, match a vetted flat-rate teammate, and map your first steps &mdash; live in 1&ndash;2 weeks, covered by the 30-Day Right-Fit Promise.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>
</main>

<!-- Entry-point modal behavior. Open/closed state is driven by the URL hash
     (#cta-<intent>) so the CSS :target rule shows the right form even with JS
     off. This layer adds scroll-lock, autofocus, ESC-to-close, and jump-free
     opening. Mirrors the homepage handler. -->
<script>
(function () {
  var modals = {};
  document.querySelectorAll('.cta-modal').forEach(function (m) { modals['#' + m.id] = m; });
  if (!Object.keys(modals).length) { return; }
  var docEl = document.documentElement, body = document.body;
  var savedY = 0, locked = false;
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
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href^="#cta-"]');
    if (a && modals[a.getAttribute('href')]) { lock(); }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modals[location.hash]) { location.hash = '#cta'; }
  });
  window.addEventListener('hashchange', sync);
  sync();
})();
</script>

<!-- Card → modal prefill. Clicking "Request this teammate" stamps the chosen
     VT into the Buy-Back form's hidden fields before the #cta-buyback modal
     opens (the modal-behavior handler above does the opening + scroll-lock). -->
<script>
(function () {
  var idF = document.getElementById('bizBuybackVtId');
  var nmF = document.getElementById('bizBuybackVtName');
  if (!idF && !nmF) { return; }
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href="#cta-buyback"][data-vt-id]');
    if (!a) { return; }
    if (idF) { idF.value = a.getAttribute('data-vt-id') || ''; }
    if (nmF) { nmF.value = a.getAttribute('data-vt-name') || ''; }
  });
})();
</script>
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
<?php $hide_lead_band = true; /* page has its own entry-point CTA forms above */ ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
