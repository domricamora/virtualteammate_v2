<?php
$page_title       = 'HIPAA-Certified Medical Virtual Assistants | Live in 1–2 Weeks, 73% Less | Virtual Teammate';
$page_description = 'A dedicated, HIPAA-certified medical virtual assistant who owns scheduling, billing, prior auth, scribing and patient follow-up inside your EHR. Published flat-rate pricing from $975/mo, live in 1–2 weeks, backed by the 30-Day Right-Fit Promise.';
$og_title         = 'One Teammate. The Whole Back Office. 73% Less Than an In-House Hire.';
$og_description   = 'HIPAA-certified medical VAs trained on Epic, Cerner, eClinicalWorks and more: they own your billing, scheduling, prior auth, scribing and follow-up. Shortlist in 5–7 days, live in 1–2 weeks.';
$canonical        = 'https://virtualteammate.com/healthcare-landing/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',       'url' => '/'],
  ['name' => 'Healthcare', 'url' => '/healthcare-landing/'],
];
// FAQPage schema — text mirrors the visible FAQ section below.
$faqs = [
  ['q' => 'Are your healthcare teammates HIPAA certified?',
   'a' => 'Yes. Every healthcare and dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible.'],
  ['q' => 'Do they know my EHR?',
   'a' => 'We match on tool fluency. Our teammates work daily in Epic, Cerner, athenahealth, eClinicalWorks, Dentrix, Eaglesoft and more, and we confirm the fit during selection.'],
  ['q' => 'How fast can someone start?',
   'a' => 'Most practices receive a curated shortlist within days and have their teammate live in 1–2 weeks: every placement backed by the 30-Day Right-Fit Promise.'],
  ['q' => 'How much does it cost?',
   'a' => 'Published flat-rate pricing, no quote required. Entry from $975/mo part-time. Pro tier: $1,625/mo full-time ($867/mo part-time). Specialist tier (billing, scribing, coding): $2,167/mo full-time ($1,300/mo part-time). All-in, no payroll tax, benefits, recruiter fees or PTO. Up to 73% less than an equivalent US in-house hire.'],
  ['q' => 'Will I be locked into a long-term contract?',
   'a' => 'No. Month-to-month after the first 90 days, no early-termination fees, no recapture clauses. The 30-Day Right-Fit Promise covers your first month on top of that.'],
  ['q' => "What happens if my VA isn't the right fit?",
   'a' => 'Two cases: (1) wrong fit, we replace them at no cost, re-shortlisting within 5 business days, billing paused until the replacement is live; (2) outsourcing isn\'t working, cancel within the first 30 days and we refund every billed day, no clawbacks.'],
  ['q' => 'Where are your VAs based?',
   'a' => 'Wherever the best fit lives. We match for your specialty, EHR, accent, language and US time-zone shift, and every teammate works your business hours.'],
  ['q' => 'How is patient data kept secure?',
   'a' => 'Five layers: HIPAA training & certification for every VA, a BAA-compatible confidentiality agreement, industry-aligned controls on the infrastructure that touches your data, a 12-month audit trail of every access event, and locked-down devices (encrypted laptops, hardware MFA, password manager, least-privilege EHR access).'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<style>
/* ── Healthcare landing — scoped styles (dark/gold theme) ── */
.hcq-hero{padding:64px 20px 26px;max-width:880px;margin:0 auto;text-align:center;}
.hcq-hero .sec-lbl{display:inline-flex;}
.hcq-hero h1{font-size:44px;line-height:1.1;letter-spacing:-.5px;margin:16px 0 16px;color:#fff;}
.hcq-hero h1 em{color:var(--gold);font-style:normal;}
.hcq-hero .hcq-sub{font-size:17px;line-height:1.65;color:var(--text-soft,#c9c8e2);margin:0 auto 14px;max-width:760px;}
.hcq-hero .hcq-promise{font-size:15px;line-height:1.6;color:rgba(255,255,255,.82);margin:0 auto 26px;max-width:740px;}
.hcq-hero .hcq-promise strong{color:#fff;}
.hcq-hero-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;align-items:center;}
.hcq-hero-tert{display:inline-block;margin:16px auto 0;color:var(--gold);font-weight:700;font-size:14.5px;text-decoration:none;}
.hcq-hero-tert:hover{text-decoration:underline;}
.hcq-cta-note{font-size:12.5px;color:rgba(255,255,255,.6);margin-top:14px;font-weight:600;}
.hcq-cta-note i{color:var(--gold);}

/* Proof bar trust strip */
.hcq-proofbar{max-width:760px;margin:0 auto 4px;}
.hcq-proofbar .trust-row{border-top:none;margin:0;padding-top:0;}
.hcq-proofbar + .svc-stats{margin-top:40px;}

/* Offer pricing callout */
.hcq-price{max-width:960px;margin:22px auto 0;padding:20px 24px;border-radius:14px;
  background:linear-gradient(135deg,rgba(57,25,186,.4),rgba(223,169,73,.16));
  border:1px solid rgba(223,169,73,.32);color:rgba(255,255,255,.86);font-size:14.5px;line-height:1.6;text-align:center;}
.hcq-price strong{color:#fff;}
.hcq-close{max-width:760px;margin:18px auto 0;text-align:center;color:var(--text-soft,#c9c8e2);font-size:15px;line-height:1.6;}
.hcq-offer-cta{text-align:center;margin-top:26px;}

/* Proof block extras */
.hcq-proof-spec{max-width:900px;margin:28px auto 0;text-align:center;color:rgba(255,255,255,.78);font-size:14.5px;line-height:1.7;}
.hcq-proof-spec strong{color:#fff;}
.hcq-proof-foot{max-width:820px;margin:14px auto 0;text-align:center;color:rgba(255,255,255,.5);font-size:12.5px;line-height:1.55;}

/* Guarantee measurable-outcome note */
.hcq-g-outcome{max-width:900px;margin:22px auto 0;color:rgba(255,255,255,.78);font-size:14px;line-height:1.65;text-align:center;}
.hcq-g-outcome strong{color:#fff;}

/* Pain section — centered header + balanced 2x2 grid */
.hcq-pain{max-width:980px;margin:0 auto;text-align:center;}
.hcq-pain .svc-p{max-width:720px;margin:0 auto 8px;}
.hcq-pain-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:860px;margin:32px auto 0;text-align:left;}
.hcq-pain-item{display:flex;gap:15px;align-items:flex-start;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);
  border-radius:14px;padding:18px 20px;color:rgba(255,255,255,.86);font-size:15px;line-height:1.5;}
.hcq-pain-item strong{color:#fff;}
.hcq-pain-item i{flex:0 0 38px;width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;
  background:rgba(223,169,73,.14);border:1px solid rgba(223,169,73,.4);color:var(--gold);font-size:15px;}
.hcq-pain-close{max-width:720px;margin:28px auto 0;text-align:center;font-size:16px;line-height:1.6;color:rgba(255,255,255,.82);}
.hcq-pain-close strong{color:#fff;}
@media (max-width:768px){.hcq-pain-grid{grid-template-columns:1fr;}}

.healthcare-quiz-wrap{max-width:760px;margin:0 auto 0;padding:0 20px;}
.quiz-container{
  background:var(--glass-bg,rgba(255,255,255,.1));
  border:1px solid var(--glass-border,rgba(255,255,255,.28));
  border-radius:22px;padding:34px;
  backdrop-filter:var(--glass-blur,blur(18px));-webkit-backdrop-filter:var(--glass-blur,blur(18px));
  box-shadow:0 30px 80px -40px rgba(0,0,0,.6);
}
.progress-bar{height:8px;background:rgba(255,255,255,.12);border-radius:99px;overflow:hidden;margin-bottom:28px;}
.progress{height:100%;width:0;background:linear-gradient(90deg,var(--gold,#dfa949),#f5d27a);border-radius:99px;transition:width .35s ease;}
#quiz h2{font-size:23px;line-height:1.3;color:#fff;margin:0 0 22px;font-weight:800;}
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
  .hcq-hero{padding:40px 18px 18px;}
  .hcq-hero .sec-lbl{justify-content:center;}
  .hcq-hero h1{font-size:31px;}
  .hcq-hero-btns{flex-direction:column;align-items:stretch;}
  .quiz-container{padding:24px 20px;}
}
</style>

<main>
  <!-- HERO — offer-led, with CTA ladder -->
  <header class="hcq-hero reveal">
    <div class="sec-lbl"><i class="fa-solid fa-stethoscope"></i> HIPAA-Certified Medical Virtual Assistants &middot; 30-Day Right-Fit Promise</div>
    <h1>One teammate. The whole back office. <em>73% less</em> than an in-house hire.</h1>
    <p class="hcq-sub">A dedicated, HIPAA-certified medical VA, trained on Epic, Cerner, eClinicalWorks, Athenahealth and more, owns your billing, scheduling, prior auth, scribing and patient follow-up inside your EHR. Curated shortlist in 5&ndash;7 business days. Live in 1&ndash;2 weeks.</p>
    <p class="hcq-promise">Published flat-rate pricing from <strong>$975/mo part-time</strong>, all-in: no payroll tax, benefits, recruiter fees or PTO. Every placement carries the <strong>30-Day Right-Fit Promise</strong>: not the right fit in month one, and we replace them at no cost or refund every billed day.</p>
    <div class="hcq-hero-btns">
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>#cta-buyers-checklist" data-cta-intent="buyers-checklist" class="btn-glass">Get the HIPAA VA Buyer&rsquo;s Checklist <i class="fa-solid fa-clipboard-check"></i></a>
    </div>
    <div><a href="#quiz" class="hcq-hero-tert">Or take the 2-minute Practice Efficiency Quiz &rarr;</a></div>
    <div class="hcq-cta-note"><i class="fa-solid fa-shield-halved"></i> No commitment. Covered by the 30-Day Right-Fit Promise: replace at no cost or refund every billed day.</div>
  </header>

  <!-- PROOF BAR -->
  <div class="hcq-proofbar reveal">
    <div class="trust-row">
      <div class="trust-item"><i class="fa-solid fa-location-dot"></i> Trusted by practices across the U.S.</div>
      <div class="trust-item"><i class="fa-brands fa-google"></i> 4.9 Google rating</div>
      <div class="trust-item"><i class="fa-solid fa-file-circle-check"></i> 95%+ clean-claim rate</div>
    </div>
  </div>
  <div class="svc-stats reveal">
    <div class="svc-stat"><div class="svc-stat-num">73%</div><div class="svc-stat-lbl">Lower Staffing Cost</div></div>
    <div class="svc-stat"><div class="svc-stat-num">95%+</div><div class="svc-stat-lbl">Clean-Claim Rate</div></div>
    <div class="svc-stat"><div class="svc-stat-num">1&ndash;2</div><div class="svc-stat-lbl">Weeks to Live</div></div>
    <div class="svc-stat"><div class="svc-stat-num">4.9</div><div class="svc-stat-lbl">Avg Google Rating</div></div>
  </div>

  <div class="divider"></div>

  <!-- PROOF BLOCK -->
  <section class="sec" id="proof" aria-labelledby="proof-h">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-chart-column"></i> Proven Client Impact</div>
      <h2 class="svc-h2" id="proof-h">Real numbers from our <em>latest clients</em></h2>
      <p class="sec-sub" style="max-width:720px;margin:0 auto;">No projections, no spin: results our virtual teammates delivered against the targets that matter most to a practice.</p>
    </div>
    <div class="case-grid case-grid-4">
      <article class="case-card reveal d1">
        <div class="case-metric">
          <div class="case-metric-h">Insurance Verifications </div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+30%</span></div>
          </div>
          <div class="case-metric-foot">Payment posting also beat target: <strong>+48%</strong> of goal</div>
        </div>
        <p class="case-q">Our virtual teammate cleared insurance verifications <strong>30% above goal</strong> and payment posting <strong>48% over target</strong>: turning a challenging AR into one of the practice&rsquo;s strongest on record.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-ribbon"></i></span>
          <div>
            <div class="case-name">Cancer Center</div>
            <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
          </div>
        </div>
      </article>

      <article class="case-card reveal d2">
        <div class="case-metric">
          <div class="case-metric-h">Pre-Certifications</div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+60%</span></div>
          </div>
          <div class="case-metric-foot">Claims volume also beat target: <strong>+47%</strong> of goal</div>
        </div>
        <p class="case-q">A dedicated billing teammate achieved pre-certs <strong>60% over target</strong> and claims volume <strong>47% above plan</strong>: keeping authorizations ahead of schedule and clean claims moving.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-hospital"></i></span>
          <div>
            <div class="case-name">Multi-Specialty Clinic</div>
            <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
          </div>
        </div>
      </article>

      <article class="case-card reveal d3">
        <div class="case-metric">
          <div class="case-metric-h">Payment Posting</div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+40%</span></div>
          </div>
          <div class="case-metric-foot">Insurance verifications also beat target: <strong>+44%</strong> of goal</div>
        </div>
        <p class="case-q">Payment posting landed <strong>+40% over target</strong> and insurance verifications <strong>+44% over</strong>: streamlining the revenue cycle so claims go out clean and cash comes in faster.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-heart-pulse"></i></span>
          <div>
            <div class="case-name">Primary Care Group</div>
            <div class="case-svc"><i class="fa-solid fa-file-invoice-dollar"></i> Medical Billing &amp; RCM VA</div>
          </div>
        </div>
      </article>

      <article class="case-card reveal d4">
        <div class="case-metric">
          <div class="case-metric-h">Claims Processed </div>
          <div class="case-metric-row">
            <div class="case-metric-after"><span class="lbl">Delivered</span><span class="val">+33%</span></div>
          </div>
        </div>
        <p class="case-q">A specialty-billing teammate cleared claims <strong>33% above target</strong> for an endodontics &amp; oral-surgery group: a full surgical schedule billed on time.</p>
        <div class="case-auth">
          <span class="ico-circle case-ico"><i class="fa-solid fa-tooth"></i></span>
          <div>
            <div class="case-name">Endodontics &amp; Oral Surgery Group</div>
            <div class="case-svc"><i class="fa-solid fa-tooth"></i> Specialty Billing &amp; RCM VA</div>
          </div>
        </div>
      </article>
    </div>
    <p class="hcq-proof-spec reveal">
      <strong>Family Practice, Southwest:</strong> A/R days 52 &rarr; 23, $68k in stalled claims recovered in 12 weeks. &middot;
      <strong>Internal Medicine, MidWest:</strong> +18 hours/week reclaimed. &middot;
      Across placements: <strong>95%+ clean-claim rate</strong> &middot; <strong>4.9</strong> average Google rating.
    </p>
    <p class="hcq-proof-foot reveal">Results from VT client engagements. Outcomes vary by specialty, baseline and scope. Your Practice Staffing Audit maps the realistic numbers for your practice.</p>
  </section>

  <div class="divider"></div>

  <?php if (false): /* "The Hidden Cost of Admin" (PAIN) + "What They Take Off Your Plate" (SOLUTION GRID) hidden per request — flip to true to restore */ ?>
  <!-- PAIN -->
  <section class="sec" id="pain">
    <div class="hcq-pain reveal">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-triangle-exclamation"></i> The Hidden Cost of Admin</div>
      <h2 class="svc-h2">Every hour on paperwork is an hour <em>away from patients</em></h2>
      <p class="svc-p">Scheduling, insurance verification, charting, callbacks, claims follow-up: the back office quietly eats the time you meant to spend on care and on growing the practice. It doesn&rsquo;t show up on a P&amp;L, but you feel it in late nights, slow follow-ups and stalled revenue.</p>
      <div class="hcq-pain-grid">
        <div class="hcq-pain-item"><i class="fa-solid fa-moon"></i><span><strong>Charting after hours</strong> instead of clocking out with your team.</span></div>
        <div class="hcq-pain-item"><i class="fa-solid fa-phone-slash"></i><span><strong>Missed follow-ups</strong> that let patients, and revenue, slip away.</span></div>
        <div class="hcq-pain-item"><i class="fa-solid fa-hourglass-half"></i><span><strong>Aging claims and unverified benefits</strong> tying up cash you&rsquo;ve already earned.</span></div>
        <div class="hcq-pain-item"><i class="fa-solid fa-inbox"></i><span><strong>A front desk underwater</strong> on calls, intake and inbox, every single day.</span></div>
      </div>
      <p class="hcq-pain-close">A HIPAA-certified Virtual Teammate takes it off your plate, and the quiz below puts a number on exactly what it&rsquo;s costing you now.</p>
    </div>
  </section>

  <div class="divider"></div>

  <!-- SOLUTION GRID — WHAT THEY HANDLE -->
  <section class="svc-bens">
    <div class="reveal" style="text-align:center;">
      <img class="hipaa-seal" src="<?= $home_base ?>images/hipaa-compliant.webp" alt="HIPAA Compliant" width="640" height="691" loading="lazy" style="margin:0 auto 20px;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-list-check"></i> What They Take Off Your Plate</div>
      <h2 class="svc-h2">One teammate. <em>The whole back office.</em></h2>
      <p class="sec-sub" style="max-width:700px;margin:0 auto;">HIPAA-certified, EHR-trained, and matched to your time zone: your Virtual Teammate owns the repeatable work so your clinical team can focus on patients.</p>
    </div>
    <div class="svc-bens-grid">
      <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span><h3>Scheduling &amp; Intake</h3><p>Booking, confirmations, reschedules, recalls and new-patient intake: calendars stay full and gaps get filled.</p></div>
      <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-file-invoice-dollar"></i></span><h3>Billing &amp; RCM</h3><p>Claims, payment posting, AR follow-up and denials worked daily so revenue lands faster and cleaner.</p></div>
      <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span><h3>Insurance &amp; Prior Auth</h3><p>Eligibility checks, benefit verification and prior authorizations completed before the visit, not after.</p></div>
      <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-pen-clip"></i></span><h3>Scribing &amp; Documentation</h3><p>Real-time charting inside your EHR so notes are done at the visit, not at midnight.</p></div>
      <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-headset"></i></span><h3>Patient Calls &amp; Follow-Up</h3><p>Inbound and outbound calls, reminders and follow-ups handled in your tone and time zone.</p></div>
      <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-inbox"></i></span><h3>Inbox &amp; Records</h3><p>Portal messages, faxes, referrals and records management kept current and never backlogged.</p></div>
    </div>
  </section>

  <div class="divider"></div>
  <?php endif; ?>

  <!-- OFFER BLOCK -->
  <section class="sec" id="offer" aria-labelledby="offer-h">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-box-open"></i> The Offer</div>
      <h2 class="svc-h2" id="offer-h">What you get with a <em>Virtual Teammate</em></h2>
      <p class="sec-sub" style="max-width:760px;margin:0 auto;">One HIPAA-certified medical VA, fully managed, takes the entire back office off your plate. Here&rsquo;s what&rsquo;s included, at a published flat rate, with nothing billed on top.</p>
    </div>
    <div class="offer-grid reveal d1">
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-user-doctor"></i></span><p><strong>A dedicated, US-time-zone medical VA</strong> trained on your EHR: Epic, Cerner, eClinicalWorks, Athenahealth, NextGen, Practice Fusion or Kareo.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-layer-group"></i></span><p><strong>Full back-office coverage:</strong> scheduling &amp; intake, billing &amp; RCM, insurance &amp; prior auth, scribing &amp; documentation, patient calls &amp; follow-up, inbox &amp; records.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-user-tie"></i></span><p><strong>A dedicated Client Success Manager</strong> on your account from day one.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-user-shield"></i></span><p><strong>Backup coverage built in</strong>: a trained backup arranged within hours, at no charge, if your VA is ever out.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-lock"></i></span><p><strong>HIPAA training &amp; certification</strong> before anyone touches PHI, a BAA-compatible confidentiality agreement, encrypted devices, hardware MFA and a 12-month audit trail.</p></div>
      <div class="offer-item"><span class="ico-circle"><i class="fa-solid fa-clipboard-check"></i></span><p><strong>Every AI-assisted output reviewed</strong> and signed off by a human before it hits a chart.</p></div>
    </div>
    <div class="hcq-price reveal">
      <strong>Published flat-rate pricing, from $975/mo part-time.</strong> Pro tier: $1,625/mo full-time ($867/mo part-time). Specialist tier (billing, scribing, coding): $2,167/mo full-time ($1,300/mo part-time). All-in: no payroll tax, no benefits, no recruiter fees, no PTO. <strong>Up to 73% less</strong> than an equivalent US in-house hire.
    </div>
    <p class="hcq-close reveal">Curated shortlist in 5&ndash;7 business days. Live in 1&ndash;2 weeks. Month-to-month after the first 90 days, no long-term contract.</p>
    <div class="hcq-offer-cta reveal">
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <div class="hcq-cta-note"><i class="fa-solid fa-shield-halved"></i> Diagnostic only. No commitment, covered by the 30-Day Right-Fit Promise: replace at no cost or refund every billed day.</div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- QUIZ -->
  <section class="sec" id="quiz">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-stethoscope"></i> Practice Efficiency Quiz</div>
      <h2 class="svc-h2">Not sure where to start? <em>See what admin is costing you</em> in 2 minutes.</h2>
      <p class="sec-sub" style="max-width:720px;margin:0 auto 8px;">Answer 6 quick questions and you&rsquo;ll see the hours you could reclaim each week, the weekly revenue your admin work is costing you, and a downloadable report. Takes about 2 minutes.</p>
    </div>
    <div class="healthcare-quiz-wrap">
      <div class="quiz-container">
        <div class="progress-bar">
          <div class="progress" id="progress"></div>
        </div>
        <div id="quiz-body"></div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- GUARANTEE -->
  <section class="sec guarantee" id="promise" aria-labelledby="g-h2">
    <div class="g-wrap reveal">
      <div class="g-copy">
        <div class="sec-lbl"><i class="fa-solid fa-shield-halved"></i> The Three Commitments</div>
        <h2 class="sec-h2" id="g-h2">If it&rsquo;s not working in month one, <em>we make it right</em></h2>
        <p class="sec-sub">Every placement carries the 30-Day Right-Fit Promise, published in writing, not buried in a sales call.</p>
        <div class="g-cards">
          <div class="g-card">
            <span class="ico-circle lg"><i class="fa-solid fa-arrows-rotate"></i></span>
            <h3>No-Cost Replacement</h3>
            <p>Not the right fit? We re-shortlist within <strong>5 business days</strong> and <strong>pause billing</strong> until your replacement is live.</p>
          </div>
          <div class="g-card">
            <span class="ico-circle lg"><i class="fa-solid fa-rotate-left"></i></span>
            <h3>30-Day Money-Back</h3>
            <p>Decide outsourcing isn&rsquo;t working in the first 30 days? We refund <strong>every billed day</strong>. No clawbacks, no lock-in.</p>
          </div>
          <div class="g-card">
            <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
            <h3>Backup Coverage Built In</h3>
            <p>If your VA is ever out, your CSM arranges a <strong>trained backup within hours</strong>, at no charge.</p>
          </div>
        </div>
        <p class="hcq-g-outcome">For revenue-cycle placements, we can also commit to a <strong>measurable outcome</strong>: improve your first-pass denial rate by an agreed number of points, or recover an agreed dollar amount of aged A/R within 90 days. Miss it and we work for free until we hit it, or refund the last 30 days. The target is set with you during baseline capture: ask your CSM whether your practice qualifies.</p>
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
      <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Are your healthcare teammates HIPAA certified?</div><div class="faq-a">Yes. Every healthcare and dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible.</div></div>
      <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Do they know my EHR?</div><div class="faq-a">We match on tool fluency. Our teammates work daily in Epic, Cerner, athenahealth, eClinicalWorks, Dentrix, Eaglesoft and more, and we confirm the fit during selection.</div></div>
      <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-clock"></i> How fast can someone start?</div><div class="faq-a">Most practices receive a curated shortlist within days and have their teammate live in 1&ndash;2 weeks: every placement backed by the 30-Day Right-Fit Promise.</div></div>
      <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does it cost?</div><div class="faq-a">Published flat-rate pricing, no quote required. Entry from $975/mo part-time. Pro tier: $1,625/mo full-time ($867/mo part-time). Specialist tier (billing, scribing, coding): $2,167/mo full-time ($1,300/mo part-time). All-in, no payroll tax, benefits, recruiter fees or PTO. <strong>Up to 73% less</strong> than an equivalent US in-house hire.</div></div>
      <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-file-contract"></i> Will I be locked into a long-term contract?</div><div class="faq-a">No. Month-to-month after the first 90 days, no early-termination fees, no recapture clauses. The 30-Day Right-Fit Promise covers your first month on top of that.</div></div>
      <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-arrows-rotate"></i> What happens if my VA isn&rsquo;t the right fit?</div><div class="faq-a">Two cases: (1) wrong fit, we replace them at no cost, re-shortlisting within 5 business days, billing paused until the replacement is live; (2) outsourcing isn&rsquo;t working, cancel within the first 30 days and we refund every billed day, no clawbacks.</div></div>
      <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-globe"></i> Where are your VAs based?</div><div class="faq-a">Wherever the best fit lives. We match for your specialty, EHR, accent, language and US time-zone shift, and every teammate works your business hours.</div></div>
      <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-lock"></i> How is patient data kept secure?</div><div class="faq-a">Five layers: HIPAA training &amp; certification for every VA, a BAA-compatible confidentiality agreement, industry-aligned controls on the infrastructure that touches your data, a 12-month audit trail of every access event, and locked-down devices (encrypted laptops, hardware MFA, password manager, least-privilege EHR access).</div></div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section class="svc-cta">
    <h2>Ready to hand off the <em style="color:var(--gold);font-style:normal;">back office?</em></h2>
    <p>Book your Practice Staffing Audit and a US-based Client Success Manager will map your busiest workflows and show you which roles to delegate first, or grab the buyer&rsquo;s checklist first.</p>
    <div class="svc-cta-btns">
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>#cta-buyers-checklist" data-cta-intent="buyers-checklist" class="btn-glass">Get the HIPAA VA Buyer&rsquo;s Checklist <i class="fa-solid fa-clipboard-check"></i></a>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> No commitment. Covered by the 30-Day Right-Fit Promise: replace at no cost or refund every billed day.</div>
    </div>
  </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
const quizData = [
{
question: "Q1. How many hours per week do you personally spend on scheduling?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q2. How often do patient follow-ups get delayed?",
options: [
{text:"Never", points:0},
{text:"Rarely", points:5},
{text:"Sometimes", points:10},
{text:"Often", points:15}
]
},
{
question: "Q3. How many patient calls/emails daily?",
options: [
{text:"0–20", points:0},
{text:"21–50", points:5},
{text:"51–100", points:10},
{text:"100+", points:15}
]
},
{
question: "Q4. Weekly documentation time?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q5. Admin backlogs slow workflow?",
options: [
{text:"Never", points:0},
{text:"Rarely", points:5},
{text:"Sometimes", points:10},
{text:"Often", points:15}
]
},
{
question: "Q6. Choose your superpower:",
options: [
{text:"More patient time", points:5},
{text:"More free time", points:5},
{text:"Less stress", points:5},
{text:"Grow my clinic", points:5}
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
    let revenuePotential = hoursSaved * 150;

    let message = "";
    if(totalPoints <= 20){
        message = "Your systems are solid, but even small delegation wins could give you these hours back.";
    } else if(totalPoints <= 40){
        message = "There's real room to optimize. Delegating admin could reclaim serious time and revenue.";
    } else if(totalPoints <= 60){
        message = "You're carrying a heavy admin load. A Virtual Teammate could win back most of these hours.";
    } else {
        message = "Admin overload is costing your practice dearly: this is exactly what a Virtual Teammate is built to fix.";
    }

    quiz.innerHTML = `
        <div class="result-box">
            <h2>You could reclaim about ${hoursSaved} hours a week.</h2>
            <p><strong>Estimated time saved:</strong> ${hoursSaved} hours/week</p>
            <p><strong>Weekly revenue your admin work is costing you:</strong> ~$${revenuePotential.toLocaleString()}/week</p>
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
    const leadsource = "Virtual Teammate Quiz - Healthcare Owner";
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
    doc.text("Virtual Teammate Efficiency Report", 20, 20);

    doc.setFontSize(12);
    doc.text(`Generated for: ${email}`, 20, 30);
    doc.text(`Estimated Time Saved: ${hoursSaved} hours/week`, 20, 40);
    doc.text(`Weekly Revenue Recoverable: $${revenuePotential}/week`, 20, 50);

    doc.line(20, 58, 190, 58);

    doc.setFontSize(14);
    doc.text("Detailed Analysis", 20, 85);

    doc.setFontSize(11);
    let analysisText = `
Based on your responses, your clinic is currently losing approximately ${hoursSaved} hours per week to administrative tasks.

This represents an estimated $${revenuePotential} in recoverable weekly revenue opportunity.

Key Optimization Areas:
• Scheduling efficiency
• Follow-up automation
• Documentation delegation
• Workflow optimization
• Patient communication systems

Recommended Next Step:
Book your Staffing Audit to map your delegation strategy.
    `;

    doc.text(analysisText, 20, 95, { maxWidth: 170 });

    doc.setFontSize(10);
    doc.text("Confidential - Virtual Teammate Assessment", 20, 280);

    doc.save("Virtual_Teammate_Report.pdf");
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
    fd.append("source", "Healthcare Efficiency Quiz");
    fd.append("form", "healthcare-quiz");
    fd.append("message",
        "Quiz score: " + totalPoints + " pts. " +
        "Est. " + hoursSaved + " hrs/week reclaimable, ~$" + revenuePotential + "/week in recoverable revenue.");
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
                pageName: "Virtual Teammate Quiz"
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
