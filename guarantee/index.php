<?php
$page_title       = '30-Day Right-Fit Promise — Our No-Risk VA Hiring Guarantee | Virtual Teammate';
$page_description = 'Hire a virtual assistant with zero risk. Virtual Teammate\'s 30-Day Right-Fit Promise: if your VA isn\'t the right fit within 30 days, we replace them at no charge, refund every billed day, and ship backup coverage with every placement. No fine print.';
$og_title         = '30-Day Right-Fit Promise — No-Risk VA Hiring Guarantee';
$og_description   = 'Three published commitments: no-cost replacement, full 30-day money-back window, and built-in backup coverage. No contract traps, no clawbacks.';
$canonical        = 'https://virtualteammate.com/guarantee/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',                       'url' => '/'],
  ['name' => '30-Day Right-Fit Promise',   'url' => '/guarantee/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@graph":[
    {
      "@type":"WebPage",
      "@id":"https://virtualteammate.com/guarantee/#webpage",
      "url":"https://virtualteammate.com/guarantee/",
      "name":"30-Day Right-Fit Promise",
      "description":"Virtual Teammate's published 30-day no-risk hiring guarantee for healthcare and business virtual assistant engagements.",
      "isPartOf":{"@id":"https://virtualteammate.com/#website"}
    },
    {
      "@type":"Service",
      "name":"Virtual Teammate VA Staffing — 30-Day Right-Fit Promise",
      "provider":{"@id":"https://virtualteammate.com/#org"},
      "areaServed":["US","CA","GB","AU"],
      "offers":{
        "@type":"Offer",
        "priceCurrency":"USD",
        "availability":"https://schema.org/InStock",
        "url":"https://virtualteammate.com/guarantee/",
        "warranty":{
          "@type":"WarrantyPromise",
          "durationOfWarranty":{"@type":"QuantitativeValue","value":30,"unitCode":"DAY"},
          "warrantyScope":"30-day right-fit replacement, money-back window, and built-in backup coverage"
        }
      }
    },
    {
      "@type":"FAQPage",
      "mainEntity":[
        {"@type":"Question","name":"What is the 30-Day Right-Fit Promise?","acceptedAnswer":{"@type":"Answer","text":"A published three-part guarantee. If a placed teammate isn't the right fit inside the first 30 days, you can request a no-cost replacement, you can cancel for a full refund of billed days, and backup coverage is included by default — no upsell."}},
        {"@type":"Question","name":"How quickly will you replace a teammate?","acceptedAnswer":{"@type":"Answer","text":"You receive a curated re-shortlist within 5 business days and we onboard the replacement teammate at no charge. Billing is paused until the new teammate is live and producing."}},
        {"@type":"Question","name":"Is there a cancellation fee?","acceptedAnswer":{"@type":"Answer","text":"No. In the first 30 days you may cancel at any time for any reason. We refund every billed day in full — no clawbacks, no termination fees, no minimum-term lock-in."}},
        {"@type":"Question","name":"What's included in backup coverage?","acceptedAnswer":{"@type":"Answer","text":"If your teammate is on PTO, sick, or unavailable, your Client Success Manager arranges a trained backup teammate within hours, at no extra cost, briefed on your workflows and EHR access. Backup ships with every placement — it's not an upsell."}},
        {"@type":"Question","name":"What if I'm not sure outsourcing is right for us?","acceptedAnswer":{"@type":"Answer","text":"Book the Practice Staffing Audit. If outsourcing isn't right for you, we'll say so on the call — no follow-up sales sequence."}}
      ]
    }
  ]
}
</script>

<style>
/* Guarantee page — scoped overrides on top of existing .guarantee/.g-* classes. */
.gp-vs{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:34px;}
@media (max-width:880px){.gp-vs{grid-template-columns:1fr;}}
.gp-vs-card{background:var(--glass-bg,rgba(255,255,255,0.04));backdrop-filter:blur(var(--glass-blur,18px));-webkit-backdrop-filter:blur(var(--glass-blur,18px));border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:28px 26px;}
.gp-vs-card.us{border-color:rgba(223,169,73,.35);box-shadow:0 18px 50px -22px rgba(223,169,73,.25);}
.gp-vs-h{display:flex;align-items:center;gap:12px;margin-bottom:18px;}
.gp-vs-h .pill{font-size:11px;text-transform:uppercase;letter-spacing:1.1px;padding:4px 12px;border-radius:30px;font-weight:700;}
.gp-vs-h .pill.us{background:rgba(223,169,73,.16);color:var(--gold,#d4a64a);border:1px solid rgba(223,169,73,.35);}
.gp-vs-h .pill.them{background:rgba(255,255,255,.06);color:var(--text-mute,#a8a7c3);border:1px solid rgba(255,255,255,.1);}
.gp-vs-h h3{margin:0;font-size:20px;font-weight:700;letter-spacing:-.2px;}
.gp-vs-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:12px;}
.gp-vs-list li{font-size:14.5px;line-height:1.6;color:var(--text-soft,#c9c8e2);display:flex;gap:10px;align-items:flex-start;}
.gp-vs-list li i{flex:0 0 18px;margin-top:3px;}
.gp-vs-card.us .gp-vs-list li i{color:var(--gold,#d4a64a);}
.gp-vs-card.them .gp-vs-list li i{color:#d96c6c;}
.gp-vs-list li strong{color:#fff;}
.gp-claim-steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-top:34px;}
@media (max-width:880px){.gp-claim-steps{grid-template-columns:1fr;}}
.gp-claim-step{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:22px;text-align:center;}
.gp-claim-step .step-num{display:inline-block;width:38px;height:38px;line-height:38px;border-radius:50%;background:var(--gold,#d4a64a);color:#1a1535;font-weight:800;margin-bottom:10px;}
.gp-claim-step h4{margin:0 0 8px;font-size:16px;font-weight:700;color:#fff;}
.gp-claim-step p{margin:0;font-size:13.5px;line-height:1.6;color:var(--text-soft,#c9c8e2);}
.gp-fineprint{margin-top:30px;text-align:center;font-size:13px;color:var(--text-mute,#a8a7c3);line-height:1.65;max-width:760px;margin-left:auto;margin-right:auto;}
.gp-fineprint strong{color:#fff;}
</style>

<main>

<!-- HERO -->
<header class="svc-hero">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-shield-halved"></i> The 30-Day Right-Fit Promise</div>
    <h1 class="svc-h1">Hire a Virtual Teammate <em>zero-risk</em>. Period.</h1>
    <p class="svc-p">Three commitments. Published in writing. <strong>No fine print. No sales-call gotchas. No "but you signed" emails.</strong> If your teammate isn&rsquo;t the right fit inside the first 30 days, we make it right: at no charge, no friction, no contract trap.</p>
    <div class="svc-hero-ctas">
      <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book My Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#claim" class="btn-glass">How to Claim the Promise <i class="fa-solid fa-list-check"></i></a>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
    </div>
    <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>30-day window starts the day your teammate goes live</span>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-arrows-rotate"></i> No-Cost Replace</div>
    <div class="hv-chip c2"><i class="fa-solid fa-rotate-left"></i> Full Money-Back</div>
    <div class="hv-card">
      <img src="<?= $home_base ?>images/photos/healthcare/How-Our-Virtual-Teammate-Help-Reduce-Costs.webp" alt="Virtual Teammate 30-Day Right-Fit Promise — zero-risk VA hiring" loading="lazy"/>
    </div>
  </div>
</header>

<!-- STATS -->
<div class="svc-stats reveal">
  <div class="svc-stat"><div class="svc-stat-num">30</div><div class="svc-stat-lbl">Day Right-Fit Window</div></div>
  <div class="svc-stat"><div class="svc-stat-num">5</div><div class="svc-stat-lbl">Biz Days to Replacement</div></div>
  <div class="svc-stat"><div class="svc-stat-num">$0</div><div class="svc-stat-lbl">Replacement / Termination Fees</div></div>
  <div class="svc-stat"><div class="svc-stat-num">95%+</div><div class="svc-stat-lbl">Client Retention Rate</div></div>
</div>

<!-- THE PROMISE — reuse existing .guarantee block classes -->
<section class="sec guarantee" id="promise" aria-labelledby="g-h2">
  <div class="g-wrap reveal">
    <div class="g-seal" aria-hidden="true">
      <div class="g-seal-ring">
        <div class="g-seal-h">VT</div>
        <div class="g-seal-s">30-Day<br>Right-Fit<br>Promise</div>
      </div>
    </div>
    <div class="g-copy">
      <div class="sec-lbl"><i class="fa-solid fa-shield-halved"></i> The Three Commitments</div>
      <h2 class="sec-h2" id="g-h2">If it&rsquo;s not working in month one, <em>we make it right</em></h2>
      <p class="sec-sub">A staffing partner, not a contract trap. Here is exactly what you get the day your teammate goes live, and exactly what happens if anything goes sideways.</p>
      <div class="g-cards">
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-arrows-rotate"></i></span>
          <h3>No-Cost Replacement</h3>
          <p>Decide a teammate isn&rsquo;t the right fit inside the first 30 days? Tell us why. We deliver a curated re-shortlist within <strong>5 business days</strong>, onboard the new teammate at no charge, and <strong>pause your billing</strong> until the replacement is live and producing.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-rotate-left"></i></span>
          <h3>30-Day Money-Back Window</h3>
          <p>Not sure outsourcing fits your practice? Cancel any time in the first 30 days: we refund every billed day in full. <strong>No clawbacks, no termination fees, no minimum-term lock-in.</strong> A staffing partner, not a contract trap.</p>
        </div>
        <div class="g-card">
          <span class="ico-circle lg"><i class="fa-solid fa-user-shield"></i></span>
          <h3>Backup Coverage Built In</h3>
          <p>Sick day, PTO, family emergency? Your Client Success Manager (CSM) arranges a <strong>trained backup teammate within hours</strong>, at no extra cost, briefed on your workflows and EHR access. Coverage ships with every placement: it&rsquo;s not an upsell.</p>
        </div>
      </div>

      <div class="g-audit">
        <div class="g-audit-h"><i class="fa-solid fa-clipboard-check"></i> What the Practice Staffing Audit covers</div>
        <ul>
          <li><strong>Workflow inventory.</strong> We map the 8&ndash;12 admin and clinical workflows that drain the most provider time in your practice: intake, charts, refills, billing, scheduling, recall, prior auth.</li>
          <li><strong>Outsourcing priority list.</strong> You leave the call with a ranked list of what to delegate <em>first</em> for fastest ROI, and what to keep in-house.</li>
          <li><strong>Tier &amp; headcount recommendation.</strong> Specific call on Pro vs Specialist tier, full-time vs part-time, and how many teammates to start with for your specialty and patient volume.</li>
          <li><strong>Honest no-fit answer.</strong> If outsourcing isn&rsquo;t right for your practice, we&rsquo;ll tell you on the call. No follow-up sales sequence.</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- HOW TO CLAIM -->
<section class="svc-proc" id="claim">
  <div style="text-align:center;max-width:720px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-list-check"></i> How to Claim the Promise</div>
    <h2 class="svc-h2">Three simple steps: <em>no forms, no friction</em></h2>
    <p class="sec-sub">A real process, with real humans, that respects your time.</p>
  </div>
  <div class="gp-claim-steps">
    <div class="gp-claim-step reveal d1">
      <div class="step-num">1</div>
      <h4>Tell Your CSM</h4>
      <p>Email, call, or message your Client Success Manager (CSM). State the issue. No formal claim form, no &ldquo;30-day review board.&rdquo;</p>
    </div>
    <div class="gp-claim-step reveal d2">
      <div class="step-num">2</div>
      <h4>Pick the Path</h4>
      <p>Replacement, or refund. Your call. We outline both options the same day so you can decide with full information.</p>
    </div>
    <div class="gp-claim-step reveal d3">
      <div class="step-num">3</div>
      <h4>We Execute</h4>
      <p>Replacement shortlist in 5 business days <em>or</em> full refund of every billed day processed inside one billing cycle.</p>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- VT vs TYPICAL STAFFING -->
<section class="sec">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-scale-balanced"></i> Risk, Side by Side</div>
    <h2 class="svc-h2">Virtual Teammate vs. <em>typical VA staffing</em></h2>
    <p class="sec-sub" style="max-width:720px;margin:0 auto;">The same dollar of staffing spend, sitting on two very different risk profiles. Read it before you sign anything.</p>
  </div>

  <div class="gp-vs">
    <div class="gp-vs-card us reveal d1">
      <div class="gp-vs-h">
        <span class="pill us">Virtual Teammate</span>
        <h3>Risk on us. Outcomes on you.</h3>
      </div>
      <ul class="gp-vs-list">
        <li><i class="fa-solid fa-circle-check"></i><span><strong>30-day no-cost replacement</strong>: billing paused until live.</span></li>
        <li><i class="fa-solid fa-circle-check"></i><span><strong>30-day money-back window</strong>: refund every billed day, no clawbacks.</span></li>
        <li><i class="fa-solid fa-circle-check"></i><span><strong>Backup coverage included</strong>: trained sub teammate within hours.</span></li>
        <li><i class="fa-solid fa-circle-check"></i><span><strong>No minimum-term lock-in</strong>: month-to-month, cancel any time.</span></li>
        <li><i class="fa-solid fa-circle-check"></i><span><strong>Flat-rate pricing</strong>: no recruiter spread, no fee creep.</span></li>
        <li><i class="fa-solid fa-circle-check"></i><span><strong>Dedicated CSM</strong>: owns escalation, ownership ladder is clear.</span></li>
      </ul>
    </div>

    <div class="gp-vs-card them reveal d2">
      <div class="gp-vs-h">
        <span class="pill them">Typical VA Agency</span>
        <h3>Risk on you. Fee on us.</h3>
      </div>
      <ul class="gp-vs-list">
        <li><i class="fa-solid fa-circle-xmark"></i><span><strong>Replacement = restart-fee + 1-week downtime.</strong></span></li>
        <li><i class="fa-solid fa-circle-xmark"></i><span><strong>Multi-month minimum-term contract</strong> with early-termination fees.</span></li>
        <li><i class="fa-solid fa-circle-xmark"></i><span><strong>Backup coverage = paid upsell</strong> or "we&rsquo;ll see what we can do."</span></li>
        <li><i class="fa-solid fa-circle-xmark"></i><span><strong>Refunds gated</strong> behind 60-day notice + claim review.</span></li>
        <li><i class="fa-solid fa-circle-xmark"></i><span><strong>Recruiter spread + activation fee + ongoing markup.</strong></span></li>
        <li><i class="fa-solid fa-circle-xmark"></i><span><strong>No named owner</strong>: escalation goes to a shared inbox.</span></li>
      </ul>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- WHY THIS WORKS -->
<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-medal"></i> Why We Can Offer This</div>
    <h2 class="svc-h2">The promise holds because <em>the bench holds</em></h2>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-users-viewfinder"></i></span><h3>Pre-Vetted Global Bench</h3><p>2,000+ teammates already through the EFSET / IQ / Cultural Index / technical / IT pipeline. We don&rsquo;t recruit a replacement: we deploy one.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-user-tie"></i></span><h3>Dedicated CSM Per Account</h3><p>Your CSM watches the engagement from week one, so we usually catch fit issues before you do.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-arrows-spin"></i></span><h3>Backup Built In</h3><p>Every active engagement has a shadow-trained backup teammate available within hours, not an upsell.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>HIPAA &amp; PHI-Safe Default</h3><p>Every teammate, every backup, every CSM is HIPAA-trained and works in encrypted environments only.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-chart-line"></i></span><h3>Monthly KPI Scorecards</h3><p>Performance is measured against your KPIs: so &ldquo;not working&rdquo; becomes a number, not a vibe.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-handshake"></i></span><h3>95%+ Retention Rate</h3><p>The promise is a publishing of how the model already runs, not a one-off concession.</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq" style="padding-top:60px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> Right-Fit Promise FAQs</div><h2 class="svc-h2">Frequently asked questions</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> What does the Right-Fit Promise actually cover?</div><div class="faq-a">Three things, in writing: a no-cost teammate replacement inside 30 days, a full money-back window inside 30 days, and backup coverage with every placement at no extra charge.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-arrows-rotate"></i> How fast is a replacement?</div><div class="faq-a">Curated re-shortlist within 5 business days, onboarded at no charge. Billing pauses for the changeover and resumes only when the new teammate is live and producing.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-rotate-left"></i> Is the refund really full?</div><div class="faq-a">Yes: every billed day in the first 30 days, refunded in full inside one billing cycle. No clawbacks, no termination fees, no minimum-term lock-in.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-user-shield"></i> Do I have to pay extra for backup?</div><div class="faq-a">No. Trained backup coverage is included with every placement. When your teammate is on PTO, sick, or unavailable, your CSM arranges a backup within hours at no extra cost.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-circle-info"></i> Is there fine print?</div><div class="faq-a">Two reasonable conditions: the 30-day window starts the day your teammate goes live (not the contract date), and replacements/refunds are issued for the original engagement scope, not for scope changes added mid-month.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-handshake"></i> What if I just want to think about it?</div><div class="faq-a">Book the Practice Staffing Audit. We&rsquo;ll map your top workflows, recommend a tier and headcount, and tell you honestly if outsourcing is or isn&rsquo;t the right move: no follow-up sales sequence either way.</div></div>
  </div>

  <p class="gp-fineprint reveal">
    <strong>Fine print, in plain English:</strong> The 30-day window begins the day your placed teammate goes live on your account. Replacement and refund both apply to the originally scoped engagement; new scope added mid-month is honored under the new month&rsquo;s billing.
  </p>
</section>

<!-- FINAL CTA -->
<section class="svc-cta">
  <h2>Ready to hire <em style="color:var(--gold);font-style:normal;">without the risk</em>?</h2>
  <p>Book a 30-minute value strategy session. We&rsquo;ll map your top workflows, recommend a tier and headcount, and back the whole thing with the Right-Fit Promise: in writing, before you sign.</p>
  <div class="svc-cta-btns">
    <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book My Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
    <a href="<?= $home_base ?>case-studies/" class="btn-glass">See Real Client KPI Results <i class="fa-solid fa-chart-line"></i></a>
    <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
  </div>
</section>

</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
