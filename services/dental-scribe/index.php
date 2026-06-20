<?php
$page_title       = 'Virtual Dental Scribe | Notes Done Chairside | Virtual Teammate';
$page_description = 'A HIPAA-compliant dental scribe documents each visit live in your practice software — clinical notes, perio charting, treatment — so notes are done chairside and operatories turn over faster. About a third of an in-house hire.';
$og_title         = 'Keep your eyes on the patient, not the keyboard';
$og_description   = 'A HIPAA-compliant dental scribe charts each visit live in your software, so notes are done chairside and your day stops running behind.';
$canonical        = 'https://virtualteammate.com/services/dental-scribe/';
$home_base        = '../../';
$svc_slug         = 'dental-scribe';
$breadcrumbs      = [
  ['name' => 'Home',      'url' => '/'],
  ['name' => 'Services',  'url' => '/services/'],
  ['name' => 'Dental Scribe', 'url' => '/services/dental-scribe/'],
];
$faqs = [
  ['q' => 'What does a dental scribe do?',
   'a' => 'They join each visit over a secure, encrypted link and chart it live in your practice software — clinical notes, perio charting, existing and proposed treatment, and chart closure — so the note is finished chairside.'],
  ['q' => 'Do they know my software?',
   'a' => 'Yes — Dentrix, Dentrix Ascend, Open Dental, Curve and Carestream. Your scribe is trained on your system before the first visit.'],
  ['q' => 'Is it secure and HIPAA compliant?',
   'a' => 'Yes. Every scribe is HIPAA-trained and certified, background-checked, and works over encrypted audio/video only.'],
  ['q' => 'Will it actually speed up my day?',
   'a' => 'Yes. Charting happens live instead of between patients, so notes are done chairside and operatories turn over faster.'],
  ['q' => 'What does it cost?',
   'a' => 'A flat monthly rate — typically 60–73% less than a US in-house hire once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.'],
  ['q' => 'What if my scribe is out?',
   'a' => 'Trained backup is included — your Client Success Manager arranges a substitute so charts never back up.'],
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
      <span aria-current="page">Dental Scribe</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-pen-clip"></i> Real-Time Charting</div>
    <h1 class="svc-h1">Eyes on the patient, <em>not the keyboard</em></h1>
    <p class="svc-lead">Charting between patients slows the whole day down and pulls your attention off the chair. A <strong>HIPAA-compliant</strong> dental scribe documents each visit live in your software &mdash; clinical notes, perio charting, treatment &mdash; so the note is done chairside and the operatory turns over faster. For about <strong>a third of the cost</strong> of an in-house hire.</p>
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
    <h2 class="svc-h2">You do the dentistry. <em>They chart it.</em></h2>
    <p class="svc-p">Stopping to chart between patients is where the schedule quietly falls behind. Your scribe captures it all as you work, so the chart is closed before you&rsquo;ve moved to the next operatory.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Writes the clinical note live</strong> &mdash; captured as the visit happens, not after.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Records perio charting</strong> &mdash; accurate numbers, entered in real time.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Logs existing and proposed treatment</strong> &mdash; ready for the case conversation.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Closes the chart chairside</strong> &mdash; you review and sign, instead of typing it up later.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Keeps pace all day</strong> &mdash; from a packed hygiene column to back-to-back restorative.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Joins over an encrypted link</strong> &mdash; audio or video, fully private.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2" style="box-shadow:none;border:0;background:none;aspect-ratio:auto;">
    <div class="svc-ba">
      <div class="svc-ba-col is-before">
        <div class="svc-ba-lbl">Right now</div>
        <ul>
          <li><i class="fa-solid fa-xmark"></i> Charting between every patient</li>
          <li><i class="fa-solid fa-xmark"></i> The day running behind</li>
          <li><i class="fa-solid fa-xmark"></i> Notes finished after hours</li>
          <li><i class="fa-solid fa-xmark"></i> Attention split from the chair</li>
        </ul>
      </div>
      <div class="svc-ba-col is-after">
        <div class="svc-ba-lbl">With a scribe</div>
        <ul>
          <li><i class="fa-solid fa-check"></i> Notes done chairside</li>
          <li><i class="fa-solid fa-check"></i> Operatories turning over</li>
          <li><i class="fa-solid fa-check"></i> Charts closed the same day</li>
          <li><i class="fa-solid fa-check"></i> Full attention on the patient</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Why practices do this</div>
    <h2 class="svc-h2">A faster day and cleaner charts</h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;">Real-time charting &mdash; without hiring, training or replacing an in-house scribe.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-gauge-high"></i></span><h3>A faster day</h3><p>Notes done chairside means operatories turn over quicker and the schedule stays on time.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span><h3>Eyes on the patient</h3><p>You focus on the chair, not the screen &mdash; better visits, better case acceptance.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Private and compliant</h3><p>HIPAA-trained, background-checked, working over encrypted links only.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>One flat rate</h3><p>Predictable monthly cost &mdash; no benefits, payroll taxes or recruiter fees.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-file-shield"></i></span><h3>Cleaner records</h3><p>Complete, consistent notes captured live &mdash; the kind that hold up to any review.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-user-tie"></i></span><h3>Never charting alone</h3><p>A dedicated CSM arranges trained backup for sick days and leave.</p></div>
  </div>
</section>

<section class="svc-tools">
  <div class="svc-tools-wrap reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-screwdriver-wrench"></i> Works in your software</div>
      <h2 class="svc-h2" style="font-size:30px;margin-bottom:12px;">Charts where you already do</h2>
      <p class="svc-p" style="margin-bottom:0;">Your scribe is trained on your practice software before the first visit &mdash; no new tools, no learning curve for you.</p>
    </div>
    <div class="svc-tools-chips">
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Dentrix</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Dentrix Ascend</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Open Dental</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Curve Dental</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Carestream</span>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-proc">
  <div style="text-align:center;max-width:640px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> How it works</div>
    <h2 class="svc-h2">From first call to charting your visits in <em>under two weeks</em></h2>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-calendar-check pstep-ico"></i></div><h3 class="pstep-title">A quick, honest call</h3><p class="pstep-desc">15 minutes on your software, your charting style, and how your notes should read.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Meet your shortlist</h3><p class="pstep-desc">A few hand-picked scribes matched to your practice and system. You interview and choose.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">They start charting</h3><p class="pstep-desc">Software access, a smooth handoff, and a dedicated CSM &mdash; in your visits within 1&ndash;2 weeks.</p></div>
  </div>
</section>

<div class="divider"></div>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div><h2 class="svc-h2">Questions practices ask us</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-pen-clip"></i> What does a dental scribe do?</div><div class="faq-a">They join each visit over a secure, encrypted link and chart it live in your practice software — clinical notes, perio charting, existing and proposed treatment, and chart closure — so the note is finished chairside.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-tooth"></i> Do they know my software?</div><div class="faq-a">Yes — Dentrix, Dentrix Ascend, Open Dental, Curve and Carestream. Your scribe is trained on your system before the first visit.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Is it secure and HIPAA compliant?</div><div class="faq-a">Yes. Every scribe is HIPAA-trained and certified, background-checked, and works over encrypted audio/video only.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-gauge-high"></i> Will it actually speed up my day?</div><div class="faq-a">Yes. Charting happens live instead of between patients, so notes are done chairside and operatories turn over faster.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> What does it cost?</div><div class="faq-a">A flat monthly rate — typically 60–73% less than a US in-house hire once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> What if my scribe is out?</div><div class="faq-a">Trained backup is included — your Client Success Manager arranges a substitute so charts never back up.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
