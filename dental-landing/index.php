<?php
$page_title       = 'Dental Virtual Assistants | Live in 1–2 Weeks, 73% Less | Virtual Teammate';
$page_description = 'A dedicated, HIPAA-certified dental Virtual Teammate runs your scheduling, recall, verifications and claims inside Dentrix or Open Dental. Published flat-rate pricing from $750 bi-weekly full time, live in 1–2 weeks, backed by the 30-Day Right-Fit Promise.';
$og_title         = 'Fully staff your front office in weeks, not months, for up to 73% less.';
$og_description   = 'HIPAA-certified dental VAs trained on Dentrix, Open Dental and more: they keep chairs full, recall worked, treatment plans followed up and claims clean. Shortlist in days, live in 1–2 weeks.';
$canonical        = 'https://virtualteammate.com/dental-landing/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',   'url' => '/'],
  ['name' => 'Dental', 'url' => '/dental-landing/'],
];
// FAQPage schema — text mirrors the visible FAQ section below.
$faqs = [
  ['q' => 'Are your dental teammates HIPAA certified?',
   'a' => 'Yes. Every dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible.'],
  ['q' => 'Do they know my practice-management software?',
   'a' => 'We match on tool fluency. Our teammates work daily in Dentrix, Open Dental, Curve, Denticon, Dentrix Ascend and more, and we confirm the fit during selection.'],
  ['q' => 'How fast can someone start?',
   'a' => 'Most practices receive a curated shortlist within days and have their teammate live in 1–2 weeks, every placement backed by the 30-Day Right-Fit Promise.'],
  ['q' => 'How much does a dental VA cost?',
   'a' => 'Published flat-rate pricing, no quote required. From $750 bi-weekly full time ($400 part-time); Specialist tier (dental billing & coding) $1,000 bi-weekly full-time ($600 part-time). All-in, no benefits, payroll tax, recruiter fees or PTO. Up to 73% less than an equivalent in-house front-desk hire.'],
  ['q' => 'Am I locked into a contract?',
   'a' => 'No. Month-to-month after your first 90 days, pause, scale up or down, or cancel with no early-termination fees. The 30-Day Right-Fit Promise covers your first month on top of that.'],
  ['q' => 'Where are your dental VAs based?',
   'a' => 'Wherever the best fit lives. We match for your PMS, specialty, accent and US time-zone shift. You hire for skill set; we handle the sourcing.'],
  ['q' => "What if my VA isn't the right fit?",
   'a' => 'Two cases: (1) wrong fit → no-cost replacement, re-shortlisted within 2 business days, billing paused until they\'re live; (2) outsourcing isn\'t working → cancel inside 30 days and we refund every billed day, no clawback.'],
  ['q' => 'How do you protect patient data?',
   'a' => 'Five layers: HIPAA training & certification before any PHI; a BAA-compatible confidentiality agreement; industry-aligned security controls; a 12-month audit trail of every access event; and locked-down devices (encrypted laptops, hardware MFA, password manager, least-privilege PMS access).'],
  ['q' => 'Which practice-management software do your VAs know?',
   'a' => 'Dentrix, Dentrix Ascend, Open Dental, Curve, Denticon and Carestream, plus clearinghouses including DentalXChange. We match on tool fluency before placement.'],
  ['q' => 'Will my dental VA work my practice hours and time zone?',
   'a' => 'Yes. Every teammate is matched to your US time-zone shift and works your business hours, so scheduling, recall, verifications and patient calls are covered live during your day, not overnight.'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<style>
/* ── Dental landing — scoped styles (dark/gold theme) ── */
.dq-hero{padding:64px 20px 26px;max-width:880px;margin:0 auto;text-align:center;}
.dq-hero .sec-lbl{display:inline-flex;}
.dq-hero h1{font-size:44px;line-height:1.1;letter-spacing:-.5px;margin:16px 0 16px;color:#fff;}
.dq-hero h1 em{color:var(--gold);font-style:normal;}
.dq-hero .dq-sub{font-size:17px;line-height:1.65;color:var(--text-soft,#c9c8e2);margin:0 auto 14px;max-width:760px;}
.dq-hero .dq-promise{font-size:15px;line-height:1.6;color:rgba(255,255,255,.82);margin:0 auto 26px;max-width:740px;}
.dq-hero .dq-promise strong{color:#fff;}
.dq-hero-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;align-items:center;}
.dq-hero-tert{display:inline-block;margin:16px auto 0;color:var(--gold);font-weight:700;font-size:14.5px;text-decoration:none;}
.dq-hero-tert:hover{text-decoration:underline;}
.dq-cta-note{font-size:12.5px;color:rgba(255,255,255,.6);margin-top:14px;font-weight:600;}
.dq-cta-note i{color:var(--gold);}

/* Proof bar trust strip */
.dq-proofbar{max-width:760px;margin:0 auto 4px;}
.dq-proofbar .trust-row{border-top:none;margin:0;padding-top:0;}
.dq-proofbar + .svc-stats{margin-top:40px;}

/* Offer pricing callout */
.dq-price{max-width:960px;margin:22px auto 0;padding:20px 24px;border-radius:14px;
  background:linear-gradient(135deg,rgba(57,25,186,.4),rgba(223,169,73,.16));
  border:1px solid rgba(223,169,73,.32);color:rgba(255,255,255,.86);font-size:14.5px;line-height:1.6;text-align:center;}
.dq-price strong{color:#fff;}
.dq-close{max-width:760px;margin:18px auto 0;text-align:center;color:var(--text-soft,#c9c8e2);font-size:15px;line-height:1.6;}
.dq-offer-cta{text-align:center;margin-top:26px;}
/* Center the orphan (7th) card on the offer grid's last row */
#offer .offer-grid > .offer-item:last-child{grid-column:1 / -1;max-width:calc(50% - 9px);margin-left:auto;margin-right:auto;}

/* Proof block extras */
.dq-proof-spec{max-width:900px;margin:28px auto 0;text-align:center;color:rgba(255,255,255,.78);font-size:14.5px;line-height:1.7;}
.dq-proof-spec strong{color:#fff;}
.dq-proof-foot{max-width:820px;margin:14px auto 0;text-align:center;color:rgba(255,255,255,.5);font-size:12.5px;line-height:1.55;}

/* Guarantee outcome note */
.dq-g-outcome{max-width:900px;margin:22px auto 0;color:rgba(255,255,255,.78);font-size:14px;line-height:1.65;text-align:center;}
.dq-g-outcome strong{color:#fff;}

/* Pain section — centered header + balanced 2x2 grid (no image) */
.dq-pain{max-width:980px;margin:0 auto;text-align:center;}
.dq-pain .svc-p{max-width:760px;margin:0 auto 8px;}
.dq-pain-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:880px;margin:32px auto 0;text-align:left;}
.dq-pain-item{display:flex;gap:15px;align-items:flex-start;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);
  border-radius:14px;padding:18px 20px;color:rgba(255,255,255,.86);font-size:15px;line-height:1.5;}
.dq-pain-item strong{color:#fff;}
.dq-pain-item i{flex:0 0 38px;width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;
  background:rgba(223,169,73,.14);border:1px solid rgba(223,169,73,.4);color:var(--gold);font-size:15px;}
.dq-pain-close{max-width:740px;margin:28px auto 0;text-align:center;font-size:16px;line-height:1.6;color:rgba(255,255,255,.82);}
.dq-pain-close strong{color:#fff;}
@media (max-width:768px){.dq-pain-grid{grid-template-columns:1fr;}}

.dental-quiz-wrap{max-width:760px;margin:0 auto 0;padding:0 20px;}
.quiz-container{
  background:var(--glass-bg,rgba(255,255,255,.1));
  border:1px solid var(--glass-border,rgba(255,255,255,.28));
  border-radius:22px;padding:34px;
  backdrop-filter:var(--glass-blur,blur(18px));-webkit-backdrop-filter:var(--glass-blur,blur(18px));
  box-shadow:0 30px 80px -40px rgba(0,0,0,.6);
}
.progress-bar{height:8px;background:rgba(255,255,255,.12);border-radius:99px;overflow:hidden;margin-bottom:28px;}
.progress{height:100%;width:0;background:linear-gradient(90deg,var(--gold,#dfa949),#f5d27a);border-radius:99px;transition:width .35s ease;}
#quiz-body h2{font-size:23px;line-height:1.3;color:#fff;margin:0 0 22px;font-weight:800;}
.option{
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.14);
  border-radius:13px;padding:15px 18px;margin-bottom:12px;color:rgba(255,255,255,.9);
  cursor:pointer;font-weight:600;font-size:15px;transition:border-color .2s,background .2s,transform .2s;
}
.option:hover{border-color:var(--gold,#dfa949);background:rgba(223,169,73,.14);color:#fff;transform:translateY(-1px);}

.result-box{text-align:center;color:#fff;}
.result-box h2{color:var(--gold,#dfa949);font-size:26px;margin:0 0 14px;}
.result-box p{color:var(--text-soft,#c9c8e2);font-size:15.5px;line-height:1.65;margin:8px 0;}
.result-box p strong{color:#fff;}
.badge{display:inline-block;background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);color:#1a1535;font-weight:800;letter-spacing:.4px;padding:10px 20px;border-radius:99px;margin:18px 0 6px;}
.result-box input{
  width:100%;max-width:380px;box-sizing:border-box;margin:8px auto 0;display:block;
  background:rgba(255,255,255,.06);border:1px solid var(--glass-border,rgba(255,255,255,.28));
  border-radius:12px;padding:13px 16px;color:#fff;font-family:inherit;font-size:14.5px;outline:none;
}
.result-box input::placeholder{color:rgba(255,255,255,.5);}
.result-box input:focus{border-color:var(--gold,#dfa949);}
.result-box button{
  background:rgba(255,255,255,.07);border:1px solid var(--glass-border,rgba(255,255,255,.28));
  color:#fff;font-family:inherit;font-weight:700;font-size:14.5px;cursor:pointer;
  padding:13px 24px;border-radius:12px;transition:border-color .2s,background .2s;
}
.result-box button:hover{border-color:var(--gold,#dfa949);background:rgba(255,255,255,.12);}
.result-box .cta{
  background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);color:#1a1535;border:0;
  font-weight:800;font-size:15.5px;padding:15px 28px;border-radius:12px;margin-top:8px;
}
.result-box .cta:hover{filter:brightness(1.05);background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);}

@media (max-width:768px){
  .dq-hero{padding:40px 18px 18px;}
  .dq-hero .sec-lbl{justify-content:center;}
  .dq-hero h1{font-size:31px;}
  .dq-hero-btns{flex-direction:column;align-items:stretch;}
  .quiz-container{padding:24px 20px;}
  #offer .offer-grid > .offer-item:last-child{max-width:none;}
}
</style>

<main>
  <!-- HERO — offer-led, with CTA ladder -->
  <header class="dq-hero reveal">
    <div class="sec-lbl"><i class="fa-solid fa-tooth"></i> HIPAA-certified dental VAs &middot; backed by the 30-Day Right-Fit Promise</div>
    <h1>Fully staff your front office in weeks, not months, for <em>up to 73% less</em>.</h1>
    <p class="dq-sub">A dedicated, HIPAA-certified dental Virtual Teammate keeps chairs full, recall worked, treatment plans followed up and claims clean, trained on Dentrix, Dentrix Ascend, Open Dental, Curve, Denticon and Carestream, matched to your US time zone. Published flat-rate pricing from $750 bi-weekly full time, all-in.</p>
    <p class="dq-promise">Not the right fit in month one? <strong>We replace them at no cost, or refund every billed day.</strong> No clawback, no lock-in.</p>
    <div class="dq-hero-btns">
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>#cta-buyers-checklist" data-cta-intent="buyers-checklist" class="btn-glass">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-clipboard-check"></i></a>
    </div>
    <div><a href="#quiz" class="dq-hero-tert">Not sure where your time is going? Take the 2-min efficiency quiz &rarr;</a></div>
    <div class="dq-cta-note"><i class="fa-solid fa-shield-halved"></i> No commitment. Covered by the 30-Day Right-Fit Promise: replace at no cost or refund every billed day.</div>
  </header>

  <!-- PROOF BAR -->
  <div class="dq-proofbar reveal">
    <div class="trust-row">
      <div class="trust-item"><i class="fa-solid fa-location-dot"></i> Trusted by practices across the U.S.</div>
      <div class="trust-item"><i class="fa-brands fa-google"></i> 4.9 Google rating</div>
      <div class="trust-item"><i class="fa-solid fa-file-circle-check"></i> 95%+ clean-claim rate</div>
    </div>
  </div>
  <div class="svc-stats reveal">
    <div class="svc-stat"><div class="svc-stat-num">73%</div><div class="svc-stat-lbl">Lower Staffing Cost</div></div>
    <div class="svc-stat"><div class="svc-stat-num">95%+</div><div class="svc-stat-lbl">Clean-Claim Rate</div></div>
    <div class="svc-stat"><div class="svc-stat-num">4.9</div><div class="svc-stat-lbl">Avg Google Rating</div></div>
    <div class="svc-stat"><div class="svc-stat-num">1&ndash;2</div><div class="svc-stat-lbl">Weeks to Live</div></div>
  </div>

  <div class="divider"></div>

  <!-- PROOF BLOCK -->
  <section class="sec" id="proof" aria-labelledby="proof-h">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-chart-column"></i> Proven Client Impact</div>
      <h2 class="svc-h2" id="proof-h">The numbers from our <em>latest clients</em></h2>
      <p class="sec-sub" style="max-width:720px;margin:0 auto;">No projections, no spin: real KPIs from recent dental placements.</p>
    </div>
    <div class="case-grid case-grid-4">
      <article class="case-card reveal d1">
        <div class="case-metric">
          <div class="case-metric-h">No-Show Rate</div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Now</span><span class="val">9%</span></div>
          </div>
          <div class="case-metric-foot">Down from <strong>22%</strong></div>
        </div>
        <p class="case-q">No-shows cut 22% &rarr; 9%: pediatric dental practice, Tampa, FL.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-tooth"></i></span>
          <div>
            <div class="case-name">Pediatric Dental Practice</div>
            <div class="case-svc"><i class="fa-solid fa-location-dot"></i> Tampa, FL</div>
          </div>
        </div>
      </article>

      <article class="case-card reveal d2">
        <div class="case-metric">
          <div class="case-metric-h">Visits Recovered</div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+14/wk</span></div>
          </div>
        </div>
        <p class="case-q">+14 visits/week recovered from confirmations &amp; rebooks.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-calendar-check"></i></span>
          <div>
            <div class="case-name">Confirmations &amp; Rebooks</div>
            <div class="case-svc"><i class="fa-solid fa-headset"></i> Scheduling &amp; Recall VA</div>
          </div>
        </div>
      </article>

      <article class="case-card reveal d3">
        <div class="case-metric">
          <div class="case-metric-h">Claims Cleared</div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+33%</span></div>
          </div>
          <div class="case-metric-foot">Above target</div>
        </div>
        <p class="case-q">Claims cleared 33% above target: endodontics &amp; oral-surgery group.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-tooth"></i></span>
          <div>
            <div class="case-name">Endodontics &amp; Oral Surgery Group</div>
            <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Specialty Billing &amp; RCM VA</div>
          </div>
        </div>
      </article>

      <article class="case-card reveal d4">
        <div class="case-metric">
          <div class="case-metric-h">First-Pass Clean Claims</div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Rate</span><span class="val">95%+</span></div>
          </div>
        </div>
        <p class="case-q">95%+ first-pass clean-claim rate: CDT-coded claims with narratives.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-file-circle-check"></i></span>
          <div>
            <div class="case-name">Dental Billing &amp; Coding</div>
            <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Dental Billing VA</div>
          </div>
        </div>
      </article>
    </div>
    <p class="dq-proof-spec reveal">
      <strong>30%+ no-show reduction:</strong> practice in Phoenix, AZ. &middot;
      <strong>4.9</strong> average Google rating across Virtual Teammate clients.
    </p>
    <p class="dq-proof-foot reveal">Results reflect recent client placements and will vary by practice, PMS and starting baseline.</p>
  </section>

  <div class="divider"></div>

  <!-- PAIN -->
  <section class="sec" id="pain">
    <div class="dq-pain reveal">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-triangle-exclamation"></i> The Hidden Cost of a Busy Front Desk</div>
      <h2 class="svc-h2">Every hour on the phones is an hour <em>off the schedule</em></h2>
      <p class="svc-p">Scheduling, insurance verification, recall, treatment-plan follow-up, claims and payment posting: the front desk eats the time you meant to spend filling the schedule and seating treatment. It doesn&rsquo;t show up on a P&amp;L, but you feel it in open chairs, unscheduled treatment and production that walks out the door.</p>
      <div class="dq-pain-grid">
        <div class="dq-pain-item"><i class="fa-solid fa-calendar-xmark"></i><span><strong>Open chairs and last-minute holes</strong> nobody had time to backfill.</span></div>
        <div class="dq-pain-item"><i class="fa-solid fa-bell-slash"></i><span><strong>Lapsed hygiene recall</strong> that lets patients, and recurring production, slip away.</span></div>
        <div class="dq-pain-item"><i class="fa-solid fa-clipboard-list"></i><span><strong>Unscheduled treatment</strong> sitting in the software after the patient said yes.</span></div>
        <div class="dq-pain-item"><i class="fa-solid fa-inbox"></i><span><strong>A front desk underwater</strong> on calls, verifications and claims, every single day.</span></div>
      </div>
      <p class="dq-pain-close">The quiz below puts a number on it. A HIPAA-certified Virtual Teammate takes it off your plate.</p>
    </div>
  </section>

  <div class="divider"></div>

  <!-- SOLUTION GRID — WHAT THEY HANDLE -->
  <section class="svc-bens">
    <div class="reveal" style="text-align:center;">
      <img class="hipaa-seal" src="<?= $home_base ?>images/hipaa-compliant.webp" alt="HIPAA Compliant" width="640" height="691" loading="lazy" style="margin:0 auto 20px;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-list-check"></i> What They Take Off Your Plate</div>
      <h2 class="svc-h2">One teammate. <em>The whole front office.</em></h2>
      <p class="sec-sub" style="max-width:700px;margin:0 auto;">HIPAA-certified, PMS-trained, and matched to your time zone, your Virtual Teammate owns the repeatable work so your front desk and clinical team can focus on patients in the chair.</p>
    </div>
    <div class="svc-bens-grid">
      <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span><h3>Scheduling &amp; recall</h3><p>Booking, confirmations, reschedules, hygiene recare and reactivation calls: the schedule stays full and holes get filled.</p></div>
      <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Insurance &amp; verification</h3><p>Eligibility checks, breakdown of benefits and pre-authorizations completed before the visit, not at the chair.</p></div>
      <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-file-invoice-dollar"></i></span><h3>Billing &amp; claims</h3><p>Claims submission, attachments, EOB and payment posting, and AR follow-up worked daily so production lands faster.</p></div>
      <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span><h3>Treatment plan follow-up</h3><p>Unscheduled treatment chased down and financial arrangements set up so case acceptance turns into seated appointments.</p></div>
      <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-headset"></i></span><h3>Patient calls &amp; reminders</h3><p>Inbound and outbound calls, appointment reminders and recall outreach handled in your tone and time zone.</p></div>
      <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-inbox"></i></span><h3>Inbox &amp; PMS management</h3><p>Patient messages, forms, referrals and records kept current in Dentrix, Open Dental and more, never backlogged.</p></div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- QUIZ -->
  <section class="sec" id="quiz">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-tooth"></i> Dental Practice Efficiency Quiz</div>
      <h2 class="svc-h2">Not sure where the hours are going? <em>Find out in 2 minutes.</em></h2>
      <p class="sec-sub" style="max-width:720px;margin:0 auto 8px;">Take the 2-minute efficiency quiz, see the weekly hours and production admin is costing you. Enter your email to get the full report.</p>
    </div>
    <div class="dental-quiz-wrap">
      <div class="quiz-container">
        <div class="progress-bar">
          <div class="progress" id="progress"></div>
        </div>
        <div id="quiz-body"></div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- OFFER BLOCK -->
  <section class="sec" id="offer" aria-labelledby="offer-h">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-box-open"></i> The Offer</div>
      <h2 class="svc-h2" id="offer-h">Everything included, <em>one flat rate</em></h2>
      <p class="sec-sub" style="max-width:760px;margin:0 auto;">Your dental Virtual Teammate, fully managed:</p>
    </div>
    <div class="offer-grid reveal d1">
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-user-doctor"></i></span><p><strong>A dedicated, HIPAA-certified VA</strong> matched to your specialty, PMS and US time zone.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-calendar-check"></i></span><p><strong>Scheduling, recall &amp; reactivation:</strong> chairs filled, hygiene recare worked.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-shield-halved"></i></span><p><strong>Insurance verification &amp; benefit breakdowns</strong> done before the visit.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-file-invoice-dollar"></i></span><p><strong>CDT-coded claims with narratives</strong>, EOB posting and AR follow-up.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-clipboard-check"></i></span><p><strong>Treatment-plan follow-up:</strong> unscheduled treatment chased, financial arrangements set.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-user-tie"></i></span><p><strong>A dedicated Client Success Manager</strong> running performance and backup coverage.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-user-shield"></i></span><p><strong>Built-in backup</strong> the day your VA is out, arranged within hours, no charge.</p></div>
    </div>
    <div class="dq-price reveal">
      <strong>From $750 bi-weekly full time ($400 part-time).</strong> Specialist tier (dental billing &amp; coding): $1,000 bi-weekly full-time ($600 part-time). All-in flat rate, no payroll tax, benefits, recruiter fees or PTO, <strong>up to 73% less</strong> than an equivalent in-house front-desk hire.
    </div>
    <p class="dq-close reveal">Curated shortlist in 2 business days. Live in 1&ndash;2 weeks. No long-term contract.</p>
    <div class="dq-offer-cta reveal">
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <div class="dq-cta-note"><i class="fa-solid fa-shield-halved"></i> Diagnostic only. No commitment, covered by the 30-Day Right-Fit Promise: replace at no cost or refund every billed day.</div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- GUARANTEE -->
  <section class="sec guarantee" id="promise" aria-labelledby="g-h2">
    <div class="g-wrap reveal">
      <div class="g-copy">
        <div class="sec-lbl"><i class="fa-solid fa-shield-halved"></i> The Three Commitments</div>
        <h2 class="sec-h2" id="g-h2">If it&rsquo;s not working in month one, <em>we make it right</em></h2>
        <p class="sec-sub">The 30-Day Right-Fit Promise, published in writing, not buried in a sales call.</p>
        <div class="g-cards">
          <div class="g-card">
            <span class="ico-circle lg"><i class="fa-solid fa-arrows-rotate"></i></span>
            <h3>No-cost replacement</h3>
            <p>Not the right fit? We re-shortlist within <strong>2 business days</strong> and pause your billing until the replacement is live. You don&rsquo;t pay to fix a mismatch.</p>
          </div>
          <div class="g-card">
            <span class="ico-circle lg"><i class="fa-solid fa-rotate-left"></i></span>
            <h3>30-day money-back window</h3>
            <p>If outsourcing isn&rsquo;t working, cancel inside the first 30 days and we refund <strong>every billed day</strong>, no clawback, no lock-in.</p>
          </div>
          <div class="g-card">
            <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
            <h3>Backup coverage built in</h3>
            <p>The day your VA is out, your CSM arranges a <strong>trained backup within hours</strong>, no extra charge. Your front desk never goes dark.</p>
          </div>
        </div>
        <p class="dq-g-outcome">Dental billing placements can include a <strong>per-practice outcome commitment</strong>. Ask your CSM.</p>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- FAQ -->
  <section class="sec" id="faq" style="padding-top:60px;">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-circle-question"></i> FAQ</div>
      <h2 class="svc-h2">Questions practice owners <em>ask us first</em></h2>
    </div>
    <div class="faq-grid">
      <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Are your dental teammates HIPAA certified?</div><div class="faq-a">Yes. Every dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible.</div></div>
      <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-tooth"></i> Do they know my practice-management software?</div><div class="faq-a">We match on tool fluency. Our teammates work daily in Dentrix, Open Dental, Curve, Denticon, Dentrix Ascend and more, and we confirm the fit during selection.</div></div>
      <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-clock"></i> How fast can someone start?</div><div class="faq-a">Most practices receive a curated shortlist within days and have their teammate live in 1&ndash;2 weeks, every placement backed by the 30-Day Right-Fit Promise.</div></div>
      <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does a dental VA cost?</div><div class="faq-a">Published flat-rate pricing, no quote required. From $750 bi-weekly full time ($400 part-time); Specialist tier (dental billing &amp; coding) $1,000 bi-weekly full-time ($600 part-time). All-in, no benefits, payroll tax, recruiter fees or PTO. Up to 73% less than an equivalent in-house front-desk hire.</div></div>
      <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-file-contract"></i> Am I locked into a contract?</div><div class="faq-a">No. Month-to-month after your first 90 days, pause, scale up or down, or cancel with no early-termination fees. The 30-Day Right-Fit Promise covers your first month on top of that.</div></div>
      <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your dental VAs based?</div><div class="faq-a">Wherever the best fit lives. We match for your PMS, specialty, accent and US time-zone shift. You hire for skill set; we handle the sourcing.</div></div>
      <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-arrows-rotate"></i> What if my VA isn&rsquo;t the right fit?</div><div class="faq-a">Two cases: (1) wrong fit &rarr; no-cost replacement, re-shortlisted within 2 business days, billing paused until they&rsquo;re live; (2) outsourcing isn&rsquo;t working &rarr; cancel inside 30 days and we refund every billed day, no clawback.</div></div>
      <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-lock"></i> How do you protect patient data?</div><div class="faq-a">Five layers: HIPAA training &amp; certification before any PHI; a BAA-compatible confidentiality agreement; industry-aligned security controls; a 12-month audit trail of every access event; and locked-down devices (encrypted laptops, hardware MFA, password manager, least-privilege PMS access).</div></div>
      <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Which practice-management software do your VAs know?</div><div class="faq-a">Dentrix, Dentrix Ascend, Open Dental, Curve, Denticon and Carestream, plus clearinghouses including DentalXChange. We match on tool fluency before placement.</div></div>
      <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-clock"></i> Will my dental VA work my practice hours and time zone?</div><div class="faq-a">Yes. Every teammate is matched to your US time-zone shift and works your business hours, so scheduling, recall, verifications and patient calls are covered live during your day, not overnight.</div></div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section class="svc-cta">
    <h2>See your score? <em style="color:var(--gold);font-style:normal;">Now reclaim the hours.</em></h2>
    <p>Book your practice staffing audit and a Client Success Manager will map exactly which front-office tasks to delegate first, or grab the buyer&rsquo;s checklist first.</p>
    <div class="svc-cta-btns">
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>#cta-buyers-checklist" data-cta-intent="buyers-checklist" class="btn-glass">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-clipboard-check"></i></a>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> 30-Day Right-Fit Promise: no-cost replacement or every billed day refunded. No lock-in.</div>
    </div>
  </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
const quizData = [
{
question: "Q1. How many hours per week does your front desk spend on scheduling and confirmations?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q2. How often does hygiene recall / reactivation fall behind?",
options: [
{text:"Never", points:0},
{text:"Rarely", points:5},
{text:"Sometimes", points:10},
{text:"Often", points:15}
]
},
{
question: "Q3. How many patient calls/emails does the front desk handle daily?",
options: [
{text:"0–20", points:0},
{text:"21–50", points:5},
{text:"51–100", points:10},
{text:"100+", points:15}
]
},
{
question: "Q4. Weekly time spent on insurance verification & claims?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q5. How much accepted treatment goes unscheduled?",
options: [
{text:"Almost none", points:0},
{text:"A little", points:5},
{text:"A fair amount", points:10},
{text:"A lot", points:15}
]
},
{
question: "Q6. Choose your superpower:",
options: [
{text:"A full schedule", points:5},
{text:"More free time", points:5},
{text:"Less front-desk stress", points:5},
{text:"Grow my practice", points:5}
]
}
];

let currentQuestion = 0;
let totalPoints = 0;

function loadQuestion() {
    const quiz = document.getElementById("quiz-body");
    const progress = document.getElementById("progress");

    progress.style.width = ((currentQuestion / quizData.length) * 100) + "%";

    if(currentQuestion >= quizData.length){
        showResults();
        return;
    }

    const q = quizData[currentQuestion];
    quiz.innerHTML = `<h2>${q.question}</h2>`;

    q.options.forEach(opt=>{
        const btn = document.createElement("div");
        btn.classList.add("option");
        btn.innerText = opt.text;
        btn.onclick = ()=>{
            totalPoints += opt.points;
            currentQuestion++;
            loadQuestion();
        }
        quiz.appendChild(btn);
    });
}

function showResults(){
    const quiz = document.getElementById("quiz-body");
    document.getElementById("progress").style.width = "100%";

    let hoursSaved = Math.floor((totalPoints / 80) * 20);
    let revenuePotential = hoursSaved * 200;

    let message = "";
    if(totalPoints <= 20){
        message = "Your systems are solid, but small delegation improvements could keep the schedule fuller and free up your front desk.";
    } else if(totalPoints <= 40){
        message = "You have optimization opportunities. Delegating front-office admin could reclaim serious time and protect production.";
    } else if(totalPoints <= 60){
        message = "Your front desk is overloaded. A Virtual Teammate could dramatically improve scheduling, recall and collections.";
    } else {
        message = "Your practice is likely leaving production on the table due to front-office overload.";
    }

    quiz.innerHTML = `
        <div class="result-box">
            <h2>You could reclaim about ${hoursSaved} hours a week.</h2>
            <p><strong>Estimated time saved:</strong> ${hoursSaved} hours/week</p>
            <p><strong>Weekly production your admin work is costing you:</strong> ~$${revenuePotential.toLocaleString()}/week</p>
            <p>${message}</p>
            <br>
            <button class="cta" onclick="location.hash='#cta-book'">
                Book my practice staffing audit →
            </button>
            <p style="font-size:12.5px;color:rgba(255,255,255,.55);margin-top:10px;">No commitment. Covered by the 30-Day Right-Fit Promise.</p>
            <br>
            <input type="email" id="userEmail" placeholder="Enter email for your detailed report">
            <br>
            <input type="phone" id="userPhone" placeholder="Enter phone for your detailed report">
            <br><br>
            <button onclick="generatePDF(${hoursSaved}, ${revenuePotential})">
                Download My Detailed Report (PDF)
            </button>
            <br><br>
            <button onclick="restartQuiz()">Retake Quiz</button>
        </div>
    `;
}
function generatePDF(hoursSaved, revenuePotential){
    const email = document.getElementById("userEmail").value;
     const phone = document.getElementById("userPhone").value;
    const leadsource = "Virtual Teammate Quiz - Dental Owner";
     if(!email && !phone){
        alert("Please enter either your email or phone number before downloading your report.");
        return;
    }

    // 🔹 Submit to HubSpot
   submitToHubSpot(email, phone, leadsource);

    // 🔹 Submit to the Virtual Teammate lead database (portal Leads page)
   submitToLeadDB(email, phone, hoursSaved, revenuePotential);

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(18);
    doc.text("Virtual Teammate Dental Efficiency Report", 20, 20);

    doc.setFontSize(12);
    doc.text(`Generated for: ${email}`, 20, 30);
    doc.text(`Estimated Time Saved: ${hoursSaved} hours/week`, 20, 40);
    doc.text(`Potential Production Recovered: $${revenuePotential}/week`, 20, 50);

    doc.line(20, 58, 190, 58);

    doc.setFontSize(14);
    doc.text("Detailed Analysis", 20, 85);

    doc.setFontSize(11);
    let analysisText = `
Based on your responses, your practice is currently losing approximately ${hoursSaved} hours per week to front-office administrative tasks.

This represents an estimated $${revenuePotential} in recoverable weekly production opportunity.

Key Optimization Areas:
• Scheduling and recall efficiency
• Hygiene reactivation
• Insurance verification and claims
• Treatment plan follow-up
• Patient communication systems

Recommended Next Step:
Book your practice staffing audit to design your delegation strategy.
    `;

    doc.text(analysisText, 20, 95, { maxWidth: 170 });

    doc.setFontSize(10);
    doc.text("Confidential - Virtual Teammate Assessment", 20, 280);

    doc.save("Virtual_Teammate_Dental_Report.pdf");
}

function restartQuiz(){
    currentQuestion = 0;
    totalPoints = 0;
    loadQuestion();
}
// Fire-and-forget POST to the site lead endpoint (saves to the portal Leads DB).
const LEAD_URL = "<?= $home_base ?>lead.php";
function submitToLeadDB(email, phone, hoursSaved, revenuePotential){
    const fd = new URLSearchParams();
    fd.append("email", email || "");
    fd.append("phone", phone || "");
    fd.append("source", "Dental Practice Efficiency Quiz");
    fd.append("form", "dental-quiz");
    fd.append("message",
        "Quiz score: " + totalPoints + " pts. " +
        "Est. " + hoursSaved + " hrs/week reclaimable, ~$" + revenuePotential + "/week in recoverable production.");
    fetch(LEAD_URL, { method: "POST", body: fd, credentials: "same-origin" })
        .then(function(r){ if(!r.ok){ console.error("Lead DB submission error"); } })
        .catch(function(e){ console.error("Lead DB submission failed:", e); });
}
function submitToHubSpot(email, phone, leadsource){
       const portalId = "46221241";
    const formId = "e744deab-314f-47a5-986d-39c87e47646f";


    fetch(`https://api.hsforms.com/submissions/v3/integration/submit/${portalId}/${formId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            fields: [
               { name: "email", value: email },
                { name: "phone", value: phone },
                { name: "leadsource", value: leadsource }
            ],
            context: {
                pageUri: window.location.href,
                pageName: "Virtual Teammate Dental Quiz"
            }
        })
    })
    .then(response => {
        if(response.ok){
            console.log("Lead successfully sent to HubSpot");
        } else {
            console.error("HubSpot submission error");
        }
    })
    .catch(error => console.error("Submission failed:", error));
}


loadQuestion();
</script>

<?php include __DIR__ . '/../includes/book-modal.php'; /* #cta-book scheduler for the CTA above */ ?>
<?php $hide_lead_band = true; /* the quiz is the page's lead capture */ ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
