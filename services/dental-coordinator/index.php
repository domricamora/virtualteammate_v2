<?php
$page_title       = 'Dental Treatment Coordinator VA | Turn Yeses Into Booked Visits | Virtual Teammate';
$page_description = 'A HIPAA-compliant treatment coordinator works your unscheduled-treatment list, presents financing, and fills the recall column — so diagnosed dentistry actually gets done. About a third of an in-house hire, live in 1–2 weeks.';
$og_title         = 'Turn diagnosed treatment into booked visits';
$og_description   = 'A HIPAA-compliant dental treatment coordinator follows up on unscheduled treatment, presents financing, and fills your recall column.';
$canonical        = 'https://virtualteammate.com/services/dental-coordinator/';
$home_base        = '../../';
$svc_slug         = 'dental-coordinator';
$breadcrumbs      = [
  ['name' => 'Home',      'url' => '/'],
  ['name' => 'Services',  'url' => '/services/'],
  ['name' => 'Dental Coordinator', 'url' => '/services/dental-coordinator/'],
];
$faqs = [
  ['q' => 'What does a treatment coordinator teammate do?',
   'a' => 'They chase down the revenue that slips away: following up on diagnosed-but-unscheduled treatment, presenting financing options, filling your hygiene recall and reactivating lapsed patients, tightening the schedule, and coordinating referrals — all in your practice software.'],
  ['q' => 'Will it really improve case acceptance?',
   'a' => 'Yes. Diagnosed treatment that never gets scheduled is the biggest quiet leak in most practices. A coordinator works that list every day and brings those patients back to the chair.'],
  ['q' => 'Do they know my software?',
   'a' => 'Yes — Dentrix, Dentrix Ascend, Open Dental, Denticon and Carestream, plus Weave, NexHealth and Solutionreach. Confirmed before they start.'],
  ['q' => 'Is my patient data safe?',
   'a' => 'Yes. Every coordinator is HIPAA-trained and certified, background-checked, and signs a confidentiality agreement before they start.'],
  ['q' => 'What does it cost?',
   'a' => 'A flat monthly rate — typically 60–73% less than a US in-house hire once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.'],
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
      <span aria-current="page">Dental Coordinator</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-handshake-angle"></i> Treatment &amp; Recall</div>
    <h1 class="svc-h1">Turn diagnosed treatment into <em>booked visits</em></h1>
    <p class="svc-lead">Every practice has a list of patients who said yes &mdash; then never scheduled. A <strong>HIPAA-compliant</strong> treatment coordinator works that list every day: following up on unscheduled treatment, presenting financing, and filling the recall column &mdash; so the dentistry you&rsquo;ve already diagnosed actually gets done. For about <strong>a third of the cost</strong> of an in-house hire.</p>
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
    <h2 class="svc-h2">The revenue that&rsquo;s already in your chart &mdash; <em>recovered</em></h2>
    <p class="svc-p">The biggest opportunity in most practices isn&rsquo;t new patients &mdash; it&rsquo;s the treatment already diagnosed and the recall already due. Your coordinator works both lists every day, so fewer patients slip through and more dentistry gets booked.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Works the unscheduled-treatment list</strong> &mdash; the patients who said yes but never booked.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Presents financing options</strong> &mdash; so cost stops being the reason a case stalls.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Fills hygiene recall</strong> &mdash; and reactivates patients who&rsquo;ve fallen off the schedule.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Tightens the schedule</strong> &mdash; fills gaps and balances the day for steady production.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Coordinates referrals</strong> &mdash; out to specialists and back, with nothing dropped.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Follows up until it&rsquo;s booked</strong> &mdash; the persistent outreach a busy front desk rarely gets to.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2" style="box-shadow:none;border:0;background:none;aspect-ratio:auto;">
    <div class="svc-ba">
      <div class="svc-ba-col is-before">
        <div class="svc-ba-lbl">Right now</div>
        <ul>
          <li><i class="fa-solid fa-xmark"></i> Diagnosed treatment sitting unbooked</li>
          <li><i class="fa-solid fa-xmark"></i> Recall list nobody works</li>
          <li><i class="fa-solid fa-xmark"></i> Gaps in tomorrow&rsquo;s schedule</li>
          <li><i class="fa-solid fa-xmark"></i> Revenue quietly walking out</li>
        </ul>
      </div>
      <div class="svc-ba-col is-after">
        <div class="svc-ba-lbl">With a teammate</div>
        <ul>
          <li><i class="fa-solid fa-check"></i> Treatment followed up every day</li>
          <li><i class="fa-solid fa-check"></i> Recall column full</li>
          <li><i class="fa-solid fa-check"></i> A tighter, fuller schedule</li>
          <li><i class="fa-solid fa-check"></i> More cases accepted and seated</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Why practices do this</div>
    <h2 class="svc-h2">Grow production from the patients you already have</h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;">Persistent follow-up and recall &mdash; without hiring, training or covering another desk.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span><h3>More cases booked</h3><p>Diagnosed treatment gets worked daily, so the dentistry you planned actually gets done.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-arrows-rotate"></i></span><h3>A full recall column</h3><p>Hygiene stays busy and lapsed patients come back &mdash; steady, predictable production.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Your data stays safe</h3><p>HIPAA-trained, background-checked, working only in your approved systems.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>One flat rate</h3><p>Predictable monthly cost &mdash; no benefits, payroll taxes or recruiter fees.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-hand-holding-dollar"></i></span><h3>Cost off the table</h3><p>Financing presented clearly, so money stops being the reason patients say &ldquo;not yet.&rdquo;</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-user-tie"></i></span><h3>Backup built in</h3><p>A dedicated CSM arranges cover for sick days and leave, so the follow-up never stops.</p></div>
  </div>
</section>

<section class="svc-tools">
  <div class="svc-tools-wrap reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-screwdriver-wrench"></i> Works in your software</div>
      <h2 class="svc-h2" style="font-size:30px;margin-bottom:12px;">No new software to learn</h2>
      <p class="svc-p" style="margin-bottom:0;">Your coordinator works in the practice software and patient-messaging tools you already use.</p>
    </div>
    <div class="svc-tools-chips">
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Dentrix</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Dentrix Ascend</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Open Dental</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Denticon</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Carestream</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-comment-dots"></i> Weave</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-comment-dots"></i> NexHealth</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-comment-dots"></i> Solutionreach</span>
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
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-calendar-check pstep-ico"></i></div><h3 class="pstep-title">A quick, honest call</h3><p class="pstep-desc">15 minutes on your unscheduled-treatment list, recall numbers, software and financing options.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Meet your shortlist</h3><p class="pstep-desc">A few hand-picked coordinators who fit your practice. You interview them and choose.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">They get to work</h3><p class="pstep-desc">Access, a smooth handoff, and a dedicated CSM &mdash; recovering treatment in 1&ndash;2 weeks.</p></div>
  </div>
</section>

<div class="divider"></div>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div><h2 class="svc-h2">Questions practices ask us</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-handshake-angle"></i> What does a treatment coordinator teammate do?</div><div class="faq-a">They chase down the revenue that slips away: following up on diagnosed-but-unscheduled treatment, presenting financing options, filling your hygiene recall and reactivating lapsed patients, tightening the schedule, and coordinating referrals — all in your practice software.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-calendar-check"></i> Will it really improve case acceptance?</div><div class="faq-a">Yes. Diagnosed treatment that never gets scheduled is the biggest quiet leak in most practices. A coordinator works that list every day and brings those patients back to the chair.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-tooth"></i> Do they know my software?</div><div class="faq-a">Yes — Dentrix, Dentrix Ascend, Open Dental, Denticon and Carestream, plus Weave, NexHealth and Solutionreach. Confirmed before they start.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Is my patient data safe?</div><div class="faq-a">Yes. Every coordinator is HIPAA-trained and certified, background-checked, and signs a confidentiality agreement before they start.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> What does it cost?</div><div class="faq-a">A flat monthly rate — typically 60–73% less than a US in-house hire once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-arrows-up-down-left-right"></i> Can I add help later or scale back?</div><div class="faq-a">Yes. Add teammates as you grow busier or ease off in slower stretches — no locked-in headcount, no penalties.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
