<?php
$page_title       = 'Medical Admin Virtual Assistants | Less Paperwork, More Patients | Virtual Teammate';
$page_description = 'A HIPAA-compliant admin teammate handles charts, intake, records and prior auths inside your EHR — so your team gets back to patients. About a third of an in-house hire, live in 1–2 weeks.';
$og_title         = 'Take the paperwork off your team’s plate';
$og_description   = 'A dedicated, HIPAA-compliant medical admin teammate quietly runs charts, intake, records and prior auths inside your EHR.';
$canonical        = 'https://virtualteammate.com/services/medical-administrative-support/';
$home_base        = '../../';
$svc_slug         = 'medical-administrative-support';
$breadcrumbs      = [
  ['name' => 'Home',      'url' => '/'],
  ['name' => 'Services',  'url' => '/services/'],
  ['name' => 'Medical Administrative Support', 'url' => '/services/medical-administrative-support/'],
];
// FAQPage schema — text mirrors the visible FAQ section below.
$faqs = [
  ['q' => 'What does an admin teammate actually do?',
   'a' => 'They take the non-clinical pile off your team: prepping charts before visits, handling intake and insurance details, requesting and routing records, putting prior-auth packets together, and keeping your EHR tidy — all inside the systems you already use.'],
  ['q' => 'Is my patient data safe?',
   'a' => 'Yes. Every teammate is HIPAA-trained and certified, background-checked, and signs a confidentiality agreement before they ever touch a chart.'],
  ['q' => 'Do they know my EHR?',
   'a' => 'They do. Your teammate works daily in systems like Epic, eClinicalWorks and athenahealth, plus the everyday office tools, and we confirm the fit before they start.'],
  ['q' => 'What does it cost?',
   'a' => 'A flat monthly rate — typically 60–73% less than a US in-house hire once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a number tailored to your practice.'],
  ['q' => 'How fast can someone start?',
   'a' => 'You get a short list of matched candidates within days, and your teammate is usually up and running in 1–2 weeks.'],
  ['q' => 'Can I add help later or scale back?',
   'a' => 'Yes. Add teammates as you grow busier or ease off in slower stretches — no locked-in headcount, no penalties.'],
];
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/nav.php';
?>
<main>
<header class="svc-hero">
  <div class="orb orb1"></div><div class="orb orb2"></div>
  <div class="svc-hero-inner reveal">
    <nav class="svc-bc" aria-label="Breadcrumb">
      <a href="<?= $home_base ?>">Home</a>
      <i class="fa-solid fa-chevron-right"></i>
      <a href="<?= $home_base ?>#specialties">Services</a>
      <i class="fa-solid fa-chevron-right"></i>
      <span aria-current="page">Medical Administrative Support</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-clipboard-list"></i> Front-Office Help</div>
    <h1 class="svc-h1">Take the paperwork <em>off your team&rsquo;s plate</em></h1>
    <p class="svc-lead">Charts, intake, records requests, prior auths &mdash; the pile of admin that eats your providers&rsquo; day and never really ends. A dedicated, <strong>HIPAA-compliant</strong> admin teammate quietly handles all of it inside your EHR, so your front desk and your doctors can get back to patients. For about <strong>a third of the cost</strong> of an in-house hire.</p>
    <div class="svc-trust">
      <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Compliant</div>
      <div class="trust-item"><i class="fa-solid fa-bolt"></i> Live in 1&ndash;2 Weeks</div>
      <div class="trust-item"><i class="fa-solid fa-rotate"></i> 30-Day Right-Fit Promise</div>
    </div>
    <div class="svc-cta-row">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#cta-buyers-checklist" class="btn-glass" data-cta-intent="buyers-checklist">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-arrow-right"></i></a>
      <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Reply within 1 business day</span>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
    </div>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="svc-snap">
      <div class="svc-snap-h"><i class="fa-solid fa-circle-check"></i> What you&rsquo;re signing up for</div>
      <div class="svc-snap-row"><div class="svc-snap-line"><span>Lower cost vs. an in-house hire</span><span class="v">up to 73%</span></div></div>
      <div class="svc-snap-row"><div class="svc-snap-line"><span>Up and running</span><span class="v">1&ndash;2 weeks</span></div></div>
      <div class="svc-snap-row"><div class="svc-snap-line"><span>Average Google rating</span><span class="v">4.9&#9733;</span></div></div>
      <div class="svc-snap-foot"><i class="fa-solid fa-shield-halved"></i> Covered by the 30-Day Right-Fit Promise &mdash; replace at no cost or money back.</div>
    </div>
  </div>
</header>

<div class="svc-stats reveal">
  <div class="svc-stat"><div class="svc-stat-num">Up to 73%</div><div class="svc-stat-lbl">Lower Cost vs In-House</div></div>
  <div class="svc-stat"><div class="svc-stat-num">1&ndash;2 wks</div><div class="svc-stat-lbl">To Get Started</div></div>
  <div class="svc-stat"><div class="svc-stat-num">4.9&#9733;</div><div class="svc-stat-lbl">Avg Google Rating</div></div>
  <div class="svc-stat"><div class="svc-stat-num">30-Day</div><div class="svc-stat-lbl">Right-Fit Promise</div></div>
</div>

<section class="svc-split">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-list-check"></i> What they handle</div>
    <h2 class="svc-h2">A trained back office, <em>without the back office</em></h2>
    <p class="svc-p">Most practices lose a dozen-plus hours a week to work that doesn&rsquo;t need a clinician &mdash; prepping charts, indexing documents, chasing intake forms, pulling records, building auth packets. Your teammate takes it off everyone&rsquo;s plate so the people in the building can focus on patients.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Gets charts ready</strong> &mdash; pulled and updated before each appointment, so the provider walks in prepared.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Runs patient intake</strong> &mdash; confirms insurance and details, sends and follows up on forms, finishes the pre-visit paperwork.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Handles records</strong> &mdash; requests, retrieves and routes them between providers, labs and patients, by the book.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Puts together prior auths &amp; referrals</strong> &mdash; assembles the packet, submits it, and tracks it to a yes.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Keeps the inbox and calendar moving</strong> &mdash; portal messages triaged, schedules tidy, nothing left sitting.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Keeps your EHR clean</strong> &mdash; accurate data in, tidy records out, ready whenever you need to pull a report.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2" style="box-shadow:none;border:0;background:none;aspect-ratio:auto;">
    <div class="svc-ba">
      <div class="svc-ba-col is-before">
        <div class="svc-ba-lbl">Right now</div>
        <ul>
          <li><i class="fa-solid fa-xmark"></i> Providers charting after hours</li>
          <li><i class="fa-solid fa-xmark"></i> Records requests piling up</li>
          <li><i class="fa-solid fa-xmark"></i> Prior auths stuck in limbo</li>
          <li><i class="fa-solid fa-xmark"></i> Front desk buried in forms</li>
        </ul>
      </div>
      <div class="svc-ba-col is-after">
        <div class="svc-ba-lbl">With a teammate</div>
        <ul>
          <li><i class="fa-solid fa-check"></i> Charts ready before each visit</li>
          <li><i class="fa-solid fa-check"></i> Records routed the same day</li>
          <li><i class="fa-solid fa-check"></i> Auths submitted and tracked</li>
          <li><i class="fa-solid fa-check"></i> Your front desk back with patients</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Why practices do this</div>
    <h2 class="svc-h2">Healthcare-trained help, without the hiring grind</h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;">Someone who already knows clinic admin &mdash; minus the salary, benefits and months of onboarding.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>One flat rate</h3><p>A predictable monthly cost instead of salary, benefits and payroll taxes &mdash; no surprises.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-bolt"></i></span><h3>Help in 1&ndash;2 weeks</h3><p>No job posts, no recruiters, no long ramp. A short list in days, working in under two weeks.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Your data stays safe</h3><p>HIPAA-trained, background-checked, working only in your approved systems.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-clock"></i></span><h3>On your hours</h3><p>Matched to your US time zone, so chart prep and follow-up happen during your day, not overnight.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-arrows-up-down-left-right"></i></span><h3>Grows with you</h3><p>Start with one. Add more as you get busier, ease back when you don&rsquo;t. No locked-in headcount.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-user-tie"></i></span><h3>Someone owns it</h3><p>A dedicated Client Success Manager handles training, quality and backup coverage so it never stalls.</p></div>
  </div>
</section>

<section class="svc-tools">
  <div class="svc-tools-wrap reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-screwdriver-wrench"></i> Works in your systems</div>
      <h2 class="svc-h2" style="font-size:30px;margin-bottom:12px;">No new software to learn</h2>
      <p class="svc-p" style="margin-bottom:0;">Your teammate already knows the EHR and office tools your practice runs on, so onboarding is a handoff, not a course.</p>
    </div>
    <div class="svc-tools-chips">
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Epic</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> eClinicalWorks</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Athenahealth</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Practice Fusion</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> NextGen</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Kareo</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-envelope"></i> Microsoft 365</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-envelope"></i> Google Workspace</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-file-signature"></i> DocuSign</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-comments"></i> Slack / Teams</span>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-proc">
  <div style="text-align:center;max-width:640px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> How it works</div>
    <h2 class="svc-h2">From first call to working teammate in <em>under two weeks</em></h2>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-calendar-check pstep-ico"></i></div><h3 class="pstep-title">A quick, honest call</h3><p class="pstep-desc">15 minutes to understand your practice, your software, and exactly what you want off your plate.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Meet your shortlist</h3><p class="pstep-desc">We hand-pick a few teammates who fit your needs. You interview them and choose.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">They get to work</h3><p class="pstep-desc">Access, a smooth handoff, and a dedicated Client Success Manager &mdash; up and running in 1&ndash;2 weeks.</p></div>
  </div>
</section>

<div class="divider"></div>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div><h2 class="svc-h2">Questions practices ask us</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-clipboard-list"></i> What does an admin teammate actually do?</div><div class="faq-a">They take the non-clinical pile off your team: prepping charts before visits, handling intake and insurance details, requesting and routing records, putting prior-auth packets together, and keeping your EHR tidy &mdash; all inside the systems you already use.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Is my patient data safe?</div><div class="faq-a">Yes. Every teammate is HIPAA-trained and certified, background-checked, and signs a confidentiality agreement before they ever touch a chart.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Do they know my EHR?</div><div class="faq-a">They do. Your teammate works daily in systems like Epic, eClinicalWorks and athenahealth, plus the everyday office tools, and we confirm the fit before they start.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> What does it cost?</div><div class="faq-a">A flat monthly rate &mdash; typically 60&ndash;73% less than a US in-house hire once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a number tailored to your practice.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-bolt"></i> How fast can someone start?</div><div class="faq-a">You get a short list of matched candidates within days, and your teammate is usually up and running in 1&ndash;2 weeks.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-arrows-up-down-left-right"></i> Can I add help later or scale back?</div><div class="faq-a">Yes. Add teammates as you grow busier or ease off in slower stretches &mdash; no locked-in headcount, no penalties.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
