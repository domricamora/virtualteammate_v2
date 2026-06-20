<?php
$page_title       = 'Virtual Medical Scribe | Stop Charting After Hours | Virtual Teammate';
$page_description = 'A HIPAA-compliant scribe joins each visit and writes your notes in real time, in your EHR, using your templates — so the chart is done when the patient leaves. About a third of an in-house scribe.';
$og_title         = 'Stop charting after the kids are asleep';
$og_description   = 'A HIPAA-compliant virtual scribe documents every visit in real time, in your EHR — get your evenings back and see a few more patients.';
$canonical        = 'https://virtualteammate.com/services/medical-scribe/';
$home_base        = '../../';
$svc_slug         = 'medical-scribe';
$breadcrumbs      = [
  ['name' => 'Home',      'url' => '/'],
  ['name' => 'Services',  'url' => '/services/'],
  ['name' => 'Medical Scribe', 'url' => '/services/medical-scribe/'],
];
$faqs = [
  ['q' => 'What does a scribe actually do?',
   'a' => 'They join each visit over a secure, encrypted link and write your note as the visit happens — history, exam, assessment and plan, plus orders and the after-visit summary — right in your EHR, using your templates.'],
  ['q' => 'How much time will I get back?',
   'a' => 'Most providers finish their notes when the visit ends instead of at home, and free up real time in their day.'],
  ['q' => 'Do they know my EHR?',
   'a' => 'Yes — Epic, eClinicalWorks, athenahealth, NextGen, Allscripts and more. Your scribe is trained on your system before the first visit.'],
  ['q' => 'Is it secure and HIPAA compliant?',
   'a' => 'Yes. Every scribe is HIPAA-trained and certified, background-checked, and works over encrypted audio/video only.'],
  ['q' => 'What does it cost?',
   'a' => 'A flat monthly rate — typically 60–73% less than a US in-house scribe once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.'],
  ['q' => 'What if my scribe is out sick?',
   'a' => 'Trained backup is included — your Client Success Manager arranges a substitute so you are never charting alone again.'],
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
      <span aria-current="page">Medical Scribe</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-pen-clip"></i> Real-Time Charting</div>
    <h1 class="svc-h1">Stop charting <em>after the kids are asleep</em></h1>
    <p class="svc-lead">You didn&rsquo;t go to med school to type notes until 9pm. A <strong>HIPAA-compliant</strong> scribe joins each visit and writes your note in real time &mdash; in your EHR, using your templates &mdash; so the chart is finished when the patient walks out. For about <strong>a third of the cost</strong> of an in-house scribe.</p>
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
    <h2 class="svc-h2">You see the patient. <em>They write it up.</em></h2>
    <p class="svc-p">Documentation is the single biggest time-sink in a provider&rsquo;s day, and the main reason charts pile up at home. Your scribe takes the keyboard off your hands so you can look the patient in the eye and still leave on time.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Writes the note as you go</strong> &mdash; history, exam, assessment and plan, captured live.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Enters orders and referrals</strong> &mdash; and drafts the after-visit summary for your sign-off.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Uses your templates</strong> &mdash; the note reads the way you already chart.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Keeps pace all clinic day</strong> &mdash; from a packed morning to the last add-on.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Tidies the chart for sign-off</strong> &mdash; you review and close, instead of building from scratch.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Joins over an encrypted link</strong> &mdash; audio or video, fully private.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2" style="box-shadow:none;border:0;background:none;aspect-ratio:auto;">
    <div class="svc-ba">
      <div class="svc-ba-col is-before">
        <div class="svc-ba-lbl">Right now</div>
        <ul>
          <li><i class="fa-solid fa-xmark"></i> Charting at night, every night</li>
          <li><i class="fa-solid fa-xmark"></i> Notes backing up for days</li>
          <li><i class="fa-solid fa-xmark"></i> Eyes on the screen, not the patient</li>
          <li><i class="fa-solid fa-xmark"></i> Quietly heading toward burnout</li>
        </ul>
      </div>
      <div class="svc-ba-col is-after">
        <div class="svc-ba-lbl">With a scribe</div>
        <ul>
          <li><i class="fa-solid fa-check"></i> Note finished when the visit ends</li>
          <li><i class="fa-solid fa-check"></i> Evenings back at home</li>
          <li><i class="fa-solid fa-check"></i> Full attention on the patient</li>
          <li><i class="fa-solid fa-check"></i> Room for a few more visits</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Why providers do this</div>
    <h2 class="svc-h2">The fix for charting burnout</h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;">Your notes done in real time &mdash; without hiring, training or replacing an in-house scribe.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-clock"></i></span><h3>Your time back</h3><p>Hours of charting handed back to you each day — for patients, or for your life.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span><h3>Eyes on the patient</h3><p>Real conversations instead of a screen between you and the person in front of you.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Private and compliant</h3><p>HIPAA-trained, background-checked, working over encrypted links only.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>One flat rate</h3><p>Predictable monthly cost &mdash; no benefits, payroll taxes or recruiter fees.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-notes-medical"></i></span><h3>Notes that read like yours</h3><p>Trained on your templates and style, so the chart sounds like you wrote it.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-user-tie"></i></span><h3>Never charting alone</h3><p>A dedicated CSM arranges trained backup for sick days and leave.</p></div>
  </div>
</section>

<section class="svc-tools">
  <div class="svc-tools-wrap reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-screwdriver-wrench"></i> Works in your EHR</div>
      <h2 class="svc-h2" style="font-size:30px;margin-bottom:12px;">Documents where you already do</h2>
      <p class="svc-p" style="margin-bottom:0;">Your scribe is trained on your EHR before the first visit &mdash; no new tools, no learning curve for you.</p>
    </div>
    <div class="svc-tools-chips">
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Epic</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> eClinicalWorks</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Athenahealth</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> NextGen</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Allscripts</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Practice Fusion</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> AdvancedMD</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> DrChrono</span>
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
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-calendar-check pstep-ico"></i></div><h3 class="pstep-title">A quick, honest call</h3><p class="pstep-desc">15 minutes on your specialty, your EHR, your templates, and how your notes should read.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Meet your shortlist</h3><p class="pstep-desc">A few hand-picked scribes matched to your specialty and system. You interview and choose.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">They start scribing</h3><p class="pstep-desc">EHR access, a smooth handoff, and a dedicated CSM &mdash; in your visits within 1&ndash;2 weeks.</p></div>
  </div>
</section>

<div class="divider"></div>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div><h2 class="svc-h2">Questions providers ask us</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-pen-clip"></i> What does a scribe actually do?</div><div class="faq-a">They join each visit over a secure, encrypted link and write your note as the visit happens — history, exam, assessment and plan, plus orders and the after-visit summary — right in your EHR, using your templates.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-clock"></i> How much time will I get back?</div><div class="faq-a">Most providers finish their notes when the visit ends instead of at home, and free up real time in their day.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Do they know my EHR?</div><div class="faq-a">Yes — Epic, eClinicalWorks, athenahealth, NextGen, Allscripts and more. Your scribe is trained on your system before the first visit.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Is it secure and HIPAA compliant?</div><div class="faq-a">Yes. Every scribe is HIPAA-trained and certified, background-checked, and works over encrypted audio/video only.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> What does it cost?</div><div class="faq-a">A flat monthly rate — typically 60–73% less than a US in-house scribe once you add benefits, taxes and overhead. See the pricing on our homepage, or book an audit for a tailored number.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> What if my scribe is out sick?</div><div class="faq-a">Trained backup is included — your Client Success Manager arranges a substitute so you are never charting alone again.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
