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
      <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=900&q=85" alt="Business virtual assistant working at laptop" loading="lazy"/>
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

<?php
  $vtc_depts      = ['Administrative Support', 'Sales', 'Marketing', 'Finance', 'Accounting', 'Customer Service', 'Business Intelligence'];
  $vtc_label      = 'Meet the Bench';
  $vtc_heading    = 'Business-Ready <em>Virtual Teammates</em>';
  $vtc_sub        = 'A sample of real, vetted teammates across admin, sales, marketing, finance and customer-service functions &mdash; matched to your time zone and ready to start in 1&ndash;2 weeks.';
  $vtc_cta_href   = '#cta-buyback';
  $vtc_cta_intent = 'buyback';
  $vtc_cta_label  = 'Request this teammate';
  $vtc_cta_vt     = true;   // prefill the on-page Buy-Back form
  include __DIR__ . '/../includes/vt-cards.php';
?>

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

  <p class="cta-stages-foot reveal">Prefer to talk it through? <a href="#cta-ops-assessment" data-cta-intent="ops-assessment">Schedule an Operational Assessment</a> and a Client Success Manager (CSM) will map it out with you.</p>
</section>

<!-- ENTRY-POINT MODALS — one tailored form per funnel stage. Opened via the
     #cta-<intent> hash (CSS :target, so they work with JS off too); the script
     below adds scroll-lock, autofocus and ESC-to-close. Each posts to lead.php
     and creates a lead. -->
<div class="cta-modal" id="cta-ops-assessment" role="dialog" aria-modal="true" aria-labelledby="bcm-oa-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-clipboard-check"></i> Ready to diagnose</div>
    <h2 class="cta-modal-h" id="bcm-oa-h">Schedule Your Operational Assessment</h2>
    <p class="cta-modal-sub">A diagnostic-only call: we map your busiest back-office workflows and tell you exactly what to delegate first &mdash; no pitch.</p>
    <form class="cta-modal-form" id="bizOpsForm" method="post" action="<?= $home_base ?>lead.php" data-lead-form
          data-lead-thanks="Thanks! Your Client Success Manager will reach out within one business day to schedule your assessment.">
      <input type="hidden" name="intent" value="ops-assessment">
      <input type="hidden" name="form" value="business-ops-assessment">
      <input type="hidden" name="source" value="Operational Assessment">
      <select class="cf-field" name="role" style="margin-bottom:16px;">
        <option value="">Biggest bottleneck right now (optional)</option>
        <option>Administrative / executive support</option>
        <option>Sales &amp; SDR / appointment setting</option>
        <option>Marketing &amp; content</option>
        <option>Finance &amp; bookkeeping</option>
        <option>Customer service / support</option>
        <option>Non-profit operations</option>
        <option>Not sure yet &mdash; help me diagnose</option>
      </select>
      <div class="cf-row">
        <input class="cf-field" name="first_name" placeholder="First Name" required>
        <input class="cf-field" name="last_name" placeholder="Last Name" required>
      </div>
      <div class="cf-row">
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
        <input class="cf-field" name="phone" type="tel" placeholder="Phone Number">
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Schedule My Operational Assessment <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>20 minutes &middot; Diagnostic only &middot; No commitment</div>
    </form>
  </div>
</div>

<div class="cta-modal" id="cta-buyback" role="dialog" aria-modal="true" aria-labelledby="bcm-bb-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-hourglass-half"></i> Ready to delegate</div>
    <h2 class="cta-modal-h" id="bcm-bb-h">Buy Back Your Company&rsquo;s Time</h2>
    <p class="cta-modal-sub">Tell us the function eating your team&rsquo;s week and we&rsquo;ll hand-pick a vetted, flat-rate teammate &mdash; matched to your time zone and live in 1&ndash;2 weeks.</p>
    <form class="cta-modal-form" id="bizBuybackForm" method="post" action="<?= $home_base ?>lead.php" data-lead-form
          data-lead-thanks="Thanks! We&rsquo;ll be in touch within one business day with your matched shortlist.">
      <input type="hidden" name="intent" value="buyback">
      <input type="hidden" name="form" value="business-buyback">
      <input type="hidden" name="source" value="Buy Back Your Company&rsquo;s Time">
      <input type="hidden" name="vt_id" id="bizBuybackVtId" value="">
      <input type="hidden" name="vt_interest" id="bizBuybackVtName" value="">
      <select class="cf-field" name="role" style="margin-bottom:16px;">
        <option value="">Function to delegate first (optional)</option>
        <option>Administrative / executive support</option>
        <option>Sales &amp; SDR / appointment setting</option>
        <option>Marketing &amp; content</option>
        <option>Finance &amp; bookkeeping</option>
        <option>Customer service / support</option>
        <option>Non-profit operations</option>
        <option>Multiple roles</option>
      </select>
      <div class="cf-row">
        <input class="cf-field" name="first_name" placeholder="First Name" required>
        <input class="cf-field" name="last_name" placeholder="Last Name" required>
      </div>
      <div class="cf-row">
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
        <input class="cf-field" name="phone" type="tel" placeholder="Phone Number">
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Buy Back Your Company&rsquo;s Time <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>No commitment &middot; We respond within 1 business day &middot; Covered by the 30-Day Right-Fit Promise</div>
    </form>
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
<?php $hide_lead_band = true; /* page has its own entry-point CTA forms above */ ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
