<?php
$page_title       = 'Virtual Medical Assistant | Give Your MAs Their Day Back | Virtual Teammate';
$page_description = 'A HIPAA-compliant virtual MA handles refills, results, prior auths and patient follow-up inside your EHR, so your in-house team stays in the room. About a third of an in-house hire, live in 1–2 weeks.';
$og_title         = 'Give your MAs their day back';
$og_description   = 'A HIPAA-compliant virtual medical assistant absorbs the refills, results, prior auths and follow-up that pull your clinical team away from patients.';
$canonical        = 'https://virtualteammate.com/services/medical-assistant/';
$home_base        = '../../';
$svc_slug         = 'medical-assistant';
$breadcrumbs      = [
  ['name' => 'Home',      'url' => '/'],
  ['name' => 'Services',  'url' => '/services/'],
  ['name' => 'Medical Assistant', 'url' => '/services/medical-assistant/'],
];
$faqs = [
  ['q' => 'What does a virtual MA actually do?',
   'a' => 'They take the non-hands-on work off your clinical team: chart review before visits, refills, prior authorizations, routing results, coordinating referrals and follow-up, and patient call-backs — all inside your EHR.'],
  ['q' => 'Does this replace my in-house MA?',
   'a' => 'No — it backs them up. The hands-on work (vitals, rooming, injections) still happens in person. Your virtual MA absorbs the documentation, follow-up and coordination so your in-house team can stay with patients.'],
  ['q' => 'Is my patient data safe?',
   'a' => 'Yes. Every virtual MA is HIPAA-trained and certified, background-checked, and works only in your approved, encrypted systems.'],
  ['q' => 'Do they know my EHR?',
   'a' => 'Yes — Epic, eClinicalWorks, athenahealth, NextGen, Practice Fusion and more, plus e-prescribe and lab and patient portals. Confirmed before they start.'],
  ['q' => 'What does it cost?',
   'a' => 'A flat monthly rate — typically 60–73% less than a US in-house MA once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.'],
  ['q' => 'What if they are out sick?',
   'a' => 'Trained backup is included — your Client Success Manager arranges a substitute so nothing stalls.'],
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
      <span aria-current="page">Medical Assistant</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-user-nurse"></i> Clinical Support</div>
    <h1 class="svc-h1">Give your MAs <em>their day back</em></h1>
    <p class="svc-lead">Your medical assistants spend half their shift on refills, results, prior auths and call-backs instead of with patients. A <strong>HIPAA-compliant</strong> virtual MA takes that pile off them &mdash; handling the documentation, follow-up and coordination so your in-house team stays in the room. For about <strong>a third of the cost</strong> of an in-house hire.</p>
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
    <h2 class="svc-h2">The clinical busywork, <em>off your team&rsquo;s plate</em></h2>
    <p class="svc-p">A huge share of an MA&rsquo;s day never touches a patient &mdash; it&rsquo;s the inbox, the refills, the results, the phone tag. Your virtual MA owns that work so the people in the building can stay focused on care.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Preps for the visit</strong> &mdash; chart review, history, and pre-visit questionnaires done ahead of time.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Manages refills</strong> &mdash; requests handled and queued for your quick approval.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Handles prior authorizations</strong> &mdash; submitted, tracked, and chased to a decision.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Routes results</strong> &mdash; labs and imaging filed, with anything abnormal flagged straight to you.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Coordinates care</strong> &mdash; referrals out, follow-ups booked, loops closed.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Follows up with patients</strong> &mdash; call-backs and portal messages, so nobody&rsquo;s left waiting.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2" style="box-shadow:none;border:0;background:none;aspect-ratio:auto;">
    <div class="svc-ba">
      <div class="svc-ba-col is-before">
        <div class="svc-ba-lbl">Right now</div>
        <ul>
          <li><i class="fa-solid fa-xmark"></i> MAs buried in the inbox</li>
          <li><i class="fa-solid fa-xmark"></i> Refills lagging behind</li>
          <li><i class="fa-solid fa-xmark"></i> Results sitting unrouted</li>
          <li><i class="fa-solid fa-xmark"></i> Patients waiting on call-backs</li>
        </ul>
      </div>
      <div class="svc-ba-col is-after">
        <div class="svc-ba-lbl">With a virtual MA</div>
        <ul>
          <li><i class="fa-solid fa-check"></i> MAs back with patients</li>
          <li><i class="fa-solid fa-check"></i> Refills cleared daily</li>
          <li><i class="fa-solid fa-check"></i> Results routed fast, flags raised</li>
          <li><i class="fa-solid fa-check"></i> Patients hear back same day</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Why practices do this</div>
    <h2 class="svc-h2">More hands, without more hiring</h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;">Clinical support that backs up your team &mdash; minus the salary, benefits and turnover.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-people-group"></i></span><h3>Your team, unburdened</h3><p>The inbox-and-follow-up load comes off your in-house MAs so they can stay with patients.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-prescription-bottle-medical"></i></span><h3>Nothing falls behind</h3><p>Refills, results and prior auths worked every day &mdash; not whenever someone finds a minute.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Your data stays safe</h3><p>HIPAA-trained, background-checked, working only in your approved systems.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>One flat rate</h3><p>Predictable monthly cost &mdash; no benefits, payroll taxes or recruiter fees.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-clock"></i></span><h3>On your hours</h3><p>Matched to your US time zone, so follow-up happens during your day &mdash; not overnight.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-user-tie"></i></span><h3>Backup built in</h3><p>A dedicated CSM arranges cover for sick days and leave, so workflows never stall.</p></div>
  </div>
</section>

<section class="svc-tools">
  <div class="svc-tools-wrap reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-screwdriver-wrench"></i> Works in your systems</div>
      <h2 class="svc-h2" style="font-size:30px;margin-bottom:12px;">No new software to learn</h2>
      <p class="svc-p" style="margin-bottom:0;">Your virtual MA already knows the EHR and portals your practice runs on, so onboarding is a handoff, not a course.</p>
    </div>
    <div class="svc-tools-chips">
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Epic</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> eClinicalWorks</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Athenahealth</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> NextGen</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Practice Fusion</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-prescription-bottle-medical"></i> e-Prescribe</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-flask"></i> Lab portals</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-mobile-screen"></i> Patient portals</span>
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
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-calendar-check pstep-ico"></i></div><h3 class="pstep-title">A quick, honest call</h3><p class="pstep-desc">15 minutes on your specialty, your EHR, and which clinical busywork you want off your team.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Meet your shortlist</h3><p class="pstep-desc">A few hand-picked MAs matched to your specialty and systems. You interview and choose.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">They get to work</h3><p class="pstep-desc">Access, a smooth handoff, and a dedicated CSM &mdash; supporting your clinic in 1&ndash;2 weeks.</p></div>
  </div>
</section>

<div class="divider"></div>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div><h2 class="svc-h2">Questions practices ask us</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-user-nurse"></i> What does a virtual MA actually do?</div><div class="faq-a">They take the non-hands-on work off your clinical team: chart review before visits, refills, prior authorizations, routing results, coordinating referrals and follow-up, and patient call-backs — all inside your EHR.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-people-group"></i> Does this replace my in-house MA?</div><div class="faq-a">No — it backs them up. The hands-on work (vitals, rooming, injections) still happens in person. Your virtual MA absorbs the documentation, follow-up and coordination so your in-house team can stay with patients.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Is my patient data safe?</div><div class="faq-a">Yes. Every virtual MA is HIPAA-trained and certified, background-checked, and works only in your approved, encrypted systems.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Do they know my EHR?</div><div class="faq-a">Yes — Epic, eClinicalWorks, athenahealth, NextGen, Practice Fusion and more, plus e-prescribe and lab and patient portals. Confirmed before they start.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> What does it cost?</div><div class="faq-a">A flat monthly rate — typically 60–73% less than a US in-house MA once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> What if they are out sick?</div><div class="faq-a">Trained backup is included — your Client Success Manager arranges a substitute so nothing stalls.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
