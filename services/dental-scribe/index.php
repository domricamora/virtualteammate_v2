<?php
$page_title       = 'Dental Scribe Virtual Assistants | HIPAA-Certified Virtual Dental Scribes | Virtual Teammate';
$page_description = 'Hire HIPAA-certified virtual dental scribes. Real-time clinical notes, perio charting, treatment documentation & chart closure in Dentrix, Eaglesoft & Open Dental. Save up to 73%.';
$og_title         = 'Dental Scribe Virtual Assistants: Real-Time Clinical Charting';
$og_description   = 'Virtual dental scribes document exams and procedures in real time inside your dental PMS, so providers finish charts chairside and stay focused on the patient.';
$canonical        = 'https://virtualteammate.com/services/dental-scribe/';
$home_base        = '../../';
$svc_slug         = 'dental-scribe';
$breadcrumbs      = [
  ['name' => 'Home',      'url' => '/'],
  ['name' => 'Services',  'url' => '/services/'],
  ['name' => 'Dental Scribe', 'url' => '/services/dental-scribe/'],
];
// FAQPage schema — text mirrors the visible FAQ section below.
$faqs = [
  ['q' => 'What does a virtual dental scribe do?',
   'a' => 'Joins each visit by HIPAA-certified audio/video link and documents in real time inside your PMS: clinical notes, perio charting, existing/proposed treatment, chart closure.'],
  ['q' => 'Which dental software do they know?',
   'a' => 'Dentrix, Dentrix Ascend, Eaglesoft, Open Dental, Carestream and Curve Dental: PMS-trained before placement.'],
  ['q' => 'Is a dental scribe HIPAA certified?',
   'a' => 'Yes. HIPAA-certified, background-checked, BAA-compatible, working in encrypted environments only. A/V links are encrypted end-to-end.'],
  ['q' => 'Will it really speed up my day?',
   'a' => 'Yes: charting happens live instead of between patients, so operatories turn over faster and notes are done chairside.'],
  ['q' => 'How much does a dental scribe cost?',
   'a' => 'Flat-rate pricing typically 60–73% less than a fully-loaded US in-house hire. Use the homepage ROI calculator for an exact estimate.'],
  ['q' => 'What if my scribe is out?',
   'a' => 'Trained backup coverage is included: your Client Success Manager (CSM) arranges a substitute so charts never back up.'],
];
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/nav.php';
?>

<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"Service",
  "serviceType":"Dental Scribe Virtual Assistant",
  "name":"Dental Scribe Virtual Assistants",
  "description":"HIPAA-certified virtual dental scribes documenting exams and procedures in real time inside the dental PMS: clinical notes, perio charting, treatment documentation, and same-day chart closure.",
  "provider":{"@type":"MedicalBusiness","name":"Virtual Teammate","url":"https://virtualteammate.com/"},
  "areaServed":["US","CA","GB","AU"],
  "audience":{"@type":"MedicalAudience","audienceType":"Dental Provider"},
  "offers":{"@type":"Offer","priceCurrency":"USD","availability":"https://schema.org/InStock","url":"https://virtualteammate.com/services/dental-scribe/"}
}
</script>
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
    <div class="svc-eyebrow"><i class="fa-solid fa-pen-clip"></i> Real-Time Dental Charting &middot; HIPAA Certified</div>
    <h1 class="svc-h1">Dental scribe <em>virtual</em> assistants</h1>
    <p class="svc-lead">Stay focused on the patient, not the keyboard. Our <strong>HIPAA-certified virtual dental scribes</strong> document every exam and procedure in real time inside your PMS, clinical notes, perio charting, treatment documentation, so charts close chairside, not after hours, at up to <strong>73% less</strong> than an in-house hire.</p>
    <div class="svc-trust">
      <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Certified</div>
      <div class="trust-item"><i class="fa-solid fa-tooth"></i> Dental PMS Trained</div>
      <div class="trust-item"><i class="fa-solid fa-microphone-lines"></i> Real-Time Live Visits</div>
      <div class="trust-item"><i class="fa-solid fa-bolt"></i> Live in 1&ndash;2 Weeks</div>
    </div>
    <div class="svc-cta-row">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book My Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>#cta-buyers-checklist" class="btn-glass" data-cta-intent="buyers-checklist">Get the HIPAA VA Buyer&rsquo;s Checklist <i class="fa-solid fa-arrow-right"></i></a>
      <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Reply within 1 business day</span>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
    </div>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-circle-check"></i> Real-Time Notes</div>
    <div class="hv-chip c2"><i class="fa-solid fa-clock"></i> Chairside Chart Closure</div>
    <div class="hv-card">
      <img src="<?= $home_base ?>images/photos/dental/Dental-Virtual-Assistants-Help-You-Stay-Ahead.webp" alt="Virtual dental scribe documenting a dental visit in the PMS" loading="lazy"/>
    </div>
  </div>
</header>

<div class="svc-stats reveal">
  <div class="svc-stat"><div class="svc-stat-num">100%</div><div class="svc-stat-lbl">Same-Day Chart Closure</div></div>
  <div class="svc-stat"><div class="svc-stat-num">+2</div><div class="svc-stat-lbl">More Patients / Day</div></div>
  <div class="svc-stat"><div class="svc-stat-num">73%</div><div class="svc-stat-lbl">Avg. Cost Savings</div></div>
  <div class="svc-stat"><div class="svc-stat-num">1&ndash;2</div><div class="svc-stat-lbl">Weeks to Launch</div></div>
</div>

<section class="svc-split">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-pen-clip"></i> What They Handle</div>
    <h2 class="svc-h2">Real-time documentation, <em>inside your PMS</em></h2>
    <p class="svc-p">Charting between patients is where dental schedules slip and notes go thin. A dedicated virtual dental scribe attends each visit via HIPAA-certified link and writes the chart as it happens: so the operatory turns over faster and documentation is complete and defensible.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Real-time clinical notes:</strong> exam findings, diagnoses, procedures, structured to your templates.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Perio &amp; existing-condition charting:</strong> pocket depths, recession, existing restorations entered live.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Treatment documentation:</strong> proposed and completed treatment, materials, tooth/surface coding cues.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Clinical-note compliance:</strong> complete, consistent notes that hold up to insurance and audit review.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Same-day chart closure:</strong> notes finalized chairside, not stacked up at end of day.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Hand-off to billing:</strong> clean, coded documentation ready for your biller or billing teammate.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2">
    <img src="<?= $home_base ?>images/photos/dental/Why-Highly-Skilled-Dental-Virtual-Assistants-Matter.webp" alt="Dentist working with a patient while a virtual scribe charts" loading="lazy"/>
  </div>
</section>

<div class="divider"></div>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Why Providers Choose VT</div>
    <h2 class="svc-h2">Why hire a virtual dental scribe?</h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;">Faster operatory turnover, better notes, and a calmer provider, without the cost of an in-office assistant on charting duty.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-clock"></i></span><h3>Faster Operatory Turnover</h3><p>Charting happens live, so the provider moves to the next patient instead of catching up on notes.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-file-shield"></i></span><h3>Audit-Ready Notes</h3><p>Complete, consistent clinical documentation that supports claims and stands up to review.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-face-smile"></i></span><h3>Better Patient Experience</h3><p>The provider stays present with the patient instead of buried in the keyboard.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Up to 73% Cost Savings</h3><p>Flat-rate pricing with backup coverage included: no benefits, PTO or recruiter fees.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>HIPAA &amp; PHI-Safe</h3><p>HIPAA-certified, background-checked, BAA-compatible. Encrypted environments only.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-arrows-spin"></i></span><h3>Zero Charting Backlog</h3><p>Backup coverage means notes never pile up when your scribe is out.</p></div>
  </div>
</section>

<section class="svc-tools">
  <div class="svc-tools-wrap reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-screwdriver-wrench"></i> Dental PMS Stack</div>
      <h2 class="svc-h2" style="font-size:30px;margin-bottom:12px;">Trained on your PMS</h2>
      <p class="svc-p" style="margin-bottom:0;">PMS-fluent before they start: your scribe doesn&rsquo;t learn the system on your dime.</p>
    </div>
    <div class="svc-tools-chips">
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Dentrix</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Dentrix Ascend</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Eaglesoft</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Open Dental</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Carestream</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-tooth"></i> Curve Dental</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-video"></i> Secure A/V link</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-code"></i> CDT-aware notes</span>
    </div>
  </div>
</section>

<div class="divider"></div>

<section class="svc-proc">
  <div style="text-align:center;max-width:640px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> How It Works</div>
    <h2 class="svc-h2">From call to live scribe in <em>under two weeks</em></h2>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-calendar-check pstep-ico"></i></div><h3 class="pstep-title">Value Strategy Call</h3><p class="pstep-desc">Walk us through your operatory flow, PMS, note templates and charting pain points.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Shortlist &amp; Interview</h3><p class="pstep-desc">Curated shortlist of PMS-trained dental scribes. You interview, you choose.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">Onboard &amp; Go Live</h3><p class="pstep-desc">PMS access, template handoff, shadow week, and a Client Success Manager (CSM). Real-time scribing in 1&ndash;2 weeks.</p></div>
  </div>
</section>

<div class="divider"></div>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div><h2 class="svc-h2">Dental scribe teammate FAQs</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-pen-clip"></i> What does a virtual dental scribe do?</div><div class="faq-a">Joins each visit by HIPAA-certified audio/video link and documents in real time inside your PMS: clinical notes, perio charting, existing/proposed treatment, chart closure.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Which dental software do they know?</div><div class="faq-a">Dentrix, Dentrix Ascend, Eaglesoft, Open Dental, Carestream and Curve Dental: PMS-trained before placement.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Is a dental scribe HIPAA certified?</div><div class="faq-a">Yes. HIPAA-certified, background-checked, BAA-compatible, working in encrypted environments only. A/V links are encrypted end-to-end.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-stopwatch"></i> Will it really speed up my day?</div><div class="faq-a">Yes: charting happens live instead of between patients, so operatories turn over faster and notes are done chairside.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a dental scribe cost?</div><div class="faq-a">Flat-rate pricing typically 60&ndash;73% less than a fully-loaded US in-house hire. Use the homepage ROI calculator for an exact estimate.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> What if my scribe is out?</div><div class="faq-a">Trained backup coverage is included: your Client Success Manager (CSM) arranges a substitute so charts never back up.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
