<?php
$page_title       = 'Medical Scribe Virtual Assistants | HIPAA-Certified Virtual Scribes | Virtual Teammate';
$page_description = 'Hire HIPAA-certified virtual medical scribes. Real-time EHR documentation, SOAP notes, charting & order entry: let providers focus on patients. Save up to 73%.';
$og_title         = 'Medical Scribe Virtual Assistants: Real-Time EHR Documentation';
$og_description   = 'HIPAA-certified virtual scribes document visits in real time inside Epic, Cerner & every major EHR: finish notes before the patient leaves.';
$canonical        = 'https://virtualteammate.com/services/medical-scribe/';
$home_base        = '../../';
$svc_slug         = 'medical-scribe';
$breadcrumbs      = [
  ['name' => 'Home',      'url' => '/'],
  ['name' => 'Services',  'url' => '/services/'],
  ['name' => 'Medical Scribe', 'url' => '/services/medical-scribe/'],
];
// FAQPage schema — text mirrors the visible FAQ section below.
$faqs = [
  ['q' => 'What does a virtual medical scribe actually do?',
   'a' => 'Joins each visit by HIPAA-certified audio or video link and documents the encounter in real time, HPI, ROS, exam, A/P, orders, AVS, inside your EHR using your templates.'],
  ['q' => 'How much time does a virtual scribe save?',
   'a' => 'Most providers reclaim 2–3 hours of charting per day, see 1–3 additional patients per shift, and eliminate after-hours documentation.'],
  ['q' => 'Which EHRs do your scribes know?',
   'a' => 'Epic, Cerner, eClinicalWorks, Athenahealth, NextGen, Allscripts, Practice Fusion, AdvancedMD, DrChrono and more: EHR-trained before placement.'],
  ['q' => 'Is a virtual scribe HIPAA certified?',
   'a' => 'Yes. HIPAA-certified, background-checked, BAA-compatible, working in encrypted environments only. Audio/video links are encrypted end-to-end.'],
  ['q' => 'How much does a virtual scribe cost?',
   'a' => 'Flat-rate pricing typically 60–73% less than a fully-loaded US in-house scribe. Use our ROI calculator for a specialty-specific quote.'],
  ['q' => 'What happens if my scribe is sick?',
   'a' => 'Trained backup coverage is included: your Client Success Manager (CSM) arranges a substitute scribe so you’re never charting alone again.'],
];
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/nav.php';
?>

<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"Service",
  "serviceType":"Medical Scribe Virtual Assistant",
  "name":"Medical Scribe Virtual Assistants",
  "description":"HIPAA-certified virtual medical scribes who document patient encounters in real time inside the provider's EHR: SOAP notes, HPI, ROS, assessments, orders and after-visit summaries.",
  "provider":{"@type":"MedicalBusiness","name":"Virtual Teammate","url":"https://virtualteammate.com/"},
  "areaServed":["US","CA","GB","AU"],
  "audience":{"@type":"MedicalAudience","audienceType":"Healthcare Provider"},
  "offers":{"@type":"Offer","priceCurrency":"USD","availability":"https://schema.org/InStock","url":"https://virtualteammate.com/services/medical-scribe/"}
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
      <span aria-current="page">Medical Scribe</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-pen-clip"></i> Real-Time EHR Documentation &middot; HIPAA Certified</div>
    <h1 class="svc-h1">Medical scribe <em>virtual</em> assistants</h1>
    <p class="svc-lead">Look at your patients again. Our <strong>HIPAA-certified virtual medical scribes</strong> document every visit in real time inside your EHR, SOAP notes, HPI, ROS, assessments, orders, so your charting is done before the patient leaves the room. Save <strong>2&ndash;3 hours of after-clinic documentation</strong> every day, at up to <strong>73% less</strong> than an in-house scribe.</p>
    <div class="svc-trust">
      <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> HIPAA Certified</div>
      <div class="trust-item"><i class="fa-solid fa-laptop-medical"></i> All Major EHRs</div>
      <div class="trust-item"><i class="fa-solid fa-microphone-lines"></i> Real-Time Live Visits</div>
      <div class="trust-item"><i class="fa-solid fa-bolt"></i> Live in 1&ndash;2 Weeks</div>
    </div>
    <div class="svc-cta-row">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>#cta-buyers-checklist" class="btn-glass" data-cta-intent="buyers-checklist">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-arrow-right"></i></a>
      <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Reply within 1 business day</span>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
    </div>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-circle-check"></i> Real-Time Notes</div>
    <div class="hv-chip c2"><i class="fa-solid fa-clock"></i> Save 2&ndash;3 hrs/day</div>
    <div class="hv-card">
      <img src="<?= $home_base ?>images/photos/healthcare/Healthcare-Virtual-Assistants-for-Efficient-Operations.webp" alt="Virtual medical scribe documenting patient visit in EHR" loading="lazy"/>
    </div>
  </div>
</header>

<div class="svc-stats reveal">
  <div class="svc-stat"><div class="svc-stat-num">2&ndash;3</div><div class="svc-stat-lbl">Hrs Saved / Day</div></div>
  <div class="svc-stat"><div class="svc-stat-num">+3</div><div class="svc-stat-lbl">More Patients / Day</div></div>
  <div class="svc-stat"><div class="svc-stat-num">73%</div><div class="svc-stat-lbl">Avg. Cost Savings</div></div>
  <div class="svc-stat"><div class="svc-stat-num">0</div><div class="svc-stat-lbl">Pajama-Time Charts</div></div>
</div>

<section class="svc-split">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-pen-clip"></i> What They Handle</div>
    <h2 class="svc-h2">Real-time documentation, <em>inside your EHR</em></h2>
    <p class="svc-p">The average US physician spends <strong>1.7 hours on EHR documentation for every 1 hour of patient care</strong>. A dedicated virtual scribe attends each visit via HIPAA-certified audio link and writes the chart as it happens: so you walk out of the room with the note already done.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Real-time SOAP notes:</strong> HPI, ROS, exam, assessment, plan, structured to your templates.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Order entry &amp; prep:</strong> labs, imaging, referrals, prescription drafts, queued for provider signature.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Coding support:</strong> level-of-service prompts, missing-documentation flags, E/M optimization cues.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>After-visit summary &amp; patient instructions:</strong> drafted and ready before checkout.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Inbox &amp; results triage:</strong> route lab results, imaging reports and pharmacy callbacks to the right action.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Same-day chart closure:</strong> notes finalized within minutes of the visit, not at 9 PM.</span></li>
    </ul>
  </div>
  <div class="svc-side-img reveal d2">
    <img src="<?= $home_base ?>images/photos/healthcare/Your-Virtual-Assistant-Every-Day.webp" alt="Physician seeing patient while virtual scribe documents in EHR" loading="lazy"/>
  </div>
</section>

<div class="divider"></div>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-star"></i> Why Providers Choose VT</div>
    <h2 class="svc-h2">Why hire a virtual scribe?</h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;">Get back your evenings, your patient face-time, and your sanity, without the cost of an in-house scribe.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-clock"></i></span><h3>Reclaim 2&ndash;3 Hrs/Day</h3><p>Stop charting after clinic. Notes are done in real time, not in the parking lot at 9 PM.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span><h3>See More Patients</h3><p>Most providers add 1&ndash;3 visits per shift once documentation drag is removed: pure throughput gain.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-face-smile"></i></span><h3>Better Patient Experience</h3><p>Eye contact. Active listening. No keyboard barrier. Patient satisfaction scores climb fast.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Up to 73% Cost Savings</h3><p>Flat-rate pricing replaces $48k&ndash;$65k loaded in-house scribe cost, with backup coverage included.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>HIPAA &amp; PHI-Safe</h3><p>HIPAA-certified, background-checked, BAA-compatible. Encrypted environments only.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-arrows-spin"></i></span><h3>Zero Burnout Risk</h3><p>Backup coverage and quality monitoring built in: the scribe shows up, every shift, no exceptions.</p></div>
  </div>
</section>

<section class="svc-tools">
  <div class="svc-tools-wrap reveal">
    <div>
      <div class="sec-lbl"><i class="fa-solid fa-screwdriver-wrench"></i> EHR &amp; Documentation Stack</div>
      <h2 class="svc-h2" style="font-size:30px;margin-bottom:12px;">Trained on every major EHR</h2>
      <p class="svc-p" style="margin-bottom:0;">EHR-fluent before they start: your scribe doesn&rsquo;t need to learn the system on your dime.</p>
    </div>
    <div class="svc-tools-chips">
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Epic</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Cerner</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> eClinicalWorks</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Athenahealth</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> NextGen</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Allscripts</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> Practice Fusion</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> AdvancedMD</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-laptop-medical"></i> DrChrono</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-microphone"></i> Dragon Medical</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-video"></i> Doxy.me / VSee</span>
      <span class="svc-tool-chip"><i class="fa-solid fa-code"></i> ICD-10 &middot; CPT &middot; E/M</span>
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
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-calendar-check pstep-ico"></i></div><h3 class="pstep-title">Strategy Call</h3><p class="pstep-desc">Walk us through your specialty, visit cadence, EHR, templates and documentation pain points.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Shortlist &amp; Interview</h3><p class="pstep-desc">Curated shortlist of EHR-trained scribes matched to your specialty: you interview, you choose.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">Onboard &amp; Go Live</h3><p class="pstep-desc">EHR access, template handoff, shadow week, and a Client Success Manager (CSM). Real-time scribing in 1&ndash;2 weeks.</p></div>
  </div>
</section>

<div class="divider"></div>

<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> FAQ</div><h2 class="svc-h2">Virtual medical scribe FAQs</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-pen-clip"></i> What does a virtual medical scribe actually do?</div><div class="faq-a">Joins each visit by HIPAA-certified audio or video link and documents the encounter in real time, HPI, ROS, exam, A/P, orders, AVS, inside your EHR using your templates.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-clock"></i> How much time does a virtual scribe save?</div><div class="faq-a">Most providers reclaim 2&ndash;3 hours of charting per day, see 1&ndash;3 additional patients per shift, and eliminate after-hours documentation.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Which EHRs do your scribes know?</div><div class="faq-a">Epic, Cerner, eClinicalWorks, Athenahealth, NextGen, Allscripts, Practice Fusion, AdvancedMD, DrChrono and more: EHR-trained before placement.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Is a virtual scribe HIPAA certified?</div><div class="faq-a">Yes. HIPAA-certified, background-checked, BAA-compatible, working in encrypted environments only. Audio/video links are encrypted end-to-end.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a virtual scribe cost?</div><div class="faq-a">Flat-rate pricing typically 60&ndash;73% less than a fully-loaded US in-house scribe. Use our ROI calculator for a specialty-specific quote.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> What happens if my scribe is sick?</div><div class="faq-a">Trained backup coverage is included: your Client Success Manager (CSM) arranges a substitute scribe so you&rsquo;re never charting alone again.</div></div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/service-cta.php'; ?>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
