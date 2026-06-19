<?php
$page_title       = 'Careers at Virtual Teammate: Remote VA Jobs (Healthcare, Admin, Sales, Finance) | Apply Now';
$page_description = 'Remote virtual assistant careers at Virtual Teammate. Roles across healthcare, dental, admin, customer service, sales, marketing, finance and business intelligence. 8-step hiring process, flexible work, competitive pay. Apply today.';
$og_title         = 'Careers at Virtual Teammate: Create Your Future. Live Your Best Life.';
$og_description   = 'Join 2,000+ virtual teammates already placed. Remote roles, supportive culture, competitive pay, and a transparent 8-step hiring process.';
$canonical        = 'https://virtualteammate.com/careers/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',    'url' => '/'],
  ['name' => 'Careers', 'url' => '/careers/'],
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
      "@id":"https://virtualteammate.com/careers/#webpage",
      "url":"https://virtualteammate.com/careers/",
      "name":"Careers at Virtual Teammate",
      "description":"Remote virtual assistant career opportunities. Healthcare, dental, admin, sales, marketing, finance & business intelligence roles.",
      "isPartOf":{"@id":"https://virtualteammate.com/#website"}
    },
    {
      "@type":"Organization",
      "@id":"https://virtualteammate.com/#org",
      "name":"Virtual Teammate",
      "url":"https://virtualteammate.com/",
      "logo":"https://virtualteammate.com/images/logo.webp",
      "sameAs":["https://virtualteammate-1719208705.teamtailor.com/"]
    },
    {
      "@type":"FAQPage",
      "mainEntity":[
        {"@type":"Question","name":"What roles does Virtual Teammate hire for?","acceptedAnswer":{"@type":"Answer","text":"We hire across healthcare (medical scribes, billers, receptionists, assistants), dental (admin, billing, scribes, coordinators, reception), administrative support, customer service, financial services, marketing, sales, and business intelligence."}},
        {"@type":"Question","name":"Is the work fully remote?","acceptedAnswer":{"@type":"Answer","text":"Yes. Every Virtual Teammate role is fully remote. You work from home, on your country's hardware, with a stable internet connection — we verify the setup before placement."}},
        {"@type":"Question","name":"What is the hiring process?","acceptedAnswer":{"@type":"Answer","text":"Eight steps: apply → application review → initial assessment (EFSET, IQ, Cultural Index) → interview → IT check → orientation → endorsement to talent pool → client selection and onboarding."}},
        {"@type":"Question","name":"What are the working hours?","acceptedAnswer":{"@type":"Answer","text":"You are matched to a client's US business hours. Interviews are typically held in the 4 PM–9 PM PHT window for Philippines-based applicants."}},
        {"@type":"Question","name":"Do I need healthcare experience?","acceptedAnswer":{"@type":"Answer","text":"For medical and dental roles, prior healthcare or dental administrative experience is preferred. Other roles (admin, sales, marketing, finance, BI) require general professional experience and strong English."}}
      ]
    }
  ]
}
</script>

<main>

<!-- HERO -->
<header class="svc-hero">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-rocket"></i> Careers at Virtual Teammate</div>
    <h1 class="svc-h1">Create your future. <em>Live your best life.</em></h1>
    <p class="svc-p">Join <strong>2,000+ virtual teammates</strong> already placed with growing US healthcare practices and businesses. Fully remote work, transparent hiring, competitive pay, and a culture built on positive energy, resilience, and value creation, not micromanagement.</p>
    <div class="svc-hero-ctas">
      <a href="https://virtualteammate-1719208705.teamtailor.com/" target="_blank" rel="noopener" class="btn-primary">Explore Open Positions <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#hiring" class="btn-glass">See the Hiring Process <i class="fa-solid fa-list-check"></i></a>
    </div>
    <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Applications reviewed within 1&ndash;2 business days</span>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-house-laptop"></i> 100% Remote</div>
    <div class="hv-chip c2"><i class="fa-solid fa-graduation-cap"></i> Career Growth</div>
    <div class="hv-card">
      <img src="<?= $home_base ?>images/photos/healthcare/Your-Virtual-Assistant-Every-Day.webp" alt="A virtual teammate working remotely from home on a fully remote VT career" loading="lazy"/>
    </div>
  </div>
</header>

<!-- STATS -->
<div class="svc-stats reveal">
  <div class="svc-stat"><div class="svc-stat-num">2,000+</div><div class="svc-stat-lbl">Teammates Placed</div></div>
  <div class="svc-stat"><div class="svc-stat-num">8</div><div class="svc-stat-lbl">Roles &amp; Departments</div></div>
  <div class="svc-stat"><div class="svc-stat-num">100%</div><div class="svc-stat-lbl">Remote Work</div></div>
  <div class="svc-stat"><div class="svc-stat-num">1&ndash;2</div><div class="svc-stat-lbl">Wks to Get Hired</div></div>
</div>

<!-- ROLES -->
<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-briefcase"></i> Roles We Hire For</div>
    <h2 class="svc-h2">Eight departments. <em>One career you&rsquo;ll actually want to stay in.</em></h2>
    <p class="sec-sub" style="max-width:720px;margin:0 auto;">Whether you&rsquo;re a clinical-side admin or a back-office BI analyst, we have a seat for you.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-user-doctor"></i></span><h3>Healthcare VA</h3><p>Medical scribes, billers, receptionists, administrative support, medical assistants: HIPAA-trained.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-tooth"></i></span><h3>Dental VA</h3><p>Dental admin, billers, scribes, treatment coordinators, receptionists: Dentrix / Open Dental.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-clipboard-list"></i></span><h3>Administrative support</h3><p>Calendar, inbox, data entry, document mgmt, executive assistance for SMBs and non-profits.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-headset"></i></span><h3>Customer service</h3><p>Inbound &amp; outbound support, ticket triage, live chat, CSAT, for service-heavy operators.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-coins"></i></span><h3>Financial services</h3><p>Bookkeeping, AR/AP, payroll support, financial reporting and compliance documentation.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-bullhorn"></i></span><h3>Marketing</h3><p>Content, social, email ops, SEO, paid media support, marketing automation.</p></div>
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-handshake"></i></span><h3>Sales</h3><p>Lead gen, CRM hygiene, outbound prospecting, appointment setting, account research.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-chart-line"></i></span><h3>Business intelligence</h3><p>Reporting, dashboard ops, KPI analysis, light data engineering and analytics support.</p></div>
  </div>
  <div style="text-align:center;margin-top:30px;" class="reveal">
    <a href="https://virtualteammate-1719208705.teamtailor.com/" target="_blank" rel="noopener" class="btn-primary">Explore Open Positions <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<div class="divider"></div>

<!-- BENEFITS -->
<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-gift"></i> Discover the Benefits of Joining Virtual Teammate</div>
    <h2 class="svc-h2">We believe in <em>empowering our team</em> to thrive</h2>
    <p class="sec-sub" style="max-width:720px;margin:0 auto;">At Virtual Teammate, we believe in empowering our team members to thrive in a supportive and dynamic environment.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-house-laptop"></i></span><h3>Flexible work environment</h3><p>100% remote roles. Work from home, on your own setup, matched to a US client time zone you can sustain.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-graduation-cap"></i></span><h3>Professional growth</h3><p>HIPAA &amp; EHR training, role-specific upskilling, mentorship from senior teammates, and clear paths to lead roles.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-people-group"></i></span><h3>Supportive community</h3><p>Real onboarding, real coaching, real backup: never alone in a chat with a client at 2 AM.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Competitive compensation</h3><p>Transparent, market-aligned pay tied to role band and experience: reviewed regularly, paid on time.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-shield-halved"></i></span><h3>Stable engagements</h3><p>Clients commit; we commit. We aim for long-running matches, not hire-and-rotate.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-heart-pulse"></i></span><h3>Healthy culture</h3><p>Positive-energy values, ethical decision-making, and zero tolerance for ghosting: ours or yours.</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- MISSION & VALUES -->
<section class="svc-split">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-bullseye"></i> Mission &amp; Values</div>
    <h2 class="svc-h2">Building value <em>together</em></h2>
    <p class="svc-p">Our mission is to bring a value-creation culture to the forefront, connecting high-performing virtual teammates with our incredible community of clients. We hire on these seven values, and we live by them.</p>
    <ul class="svc-checks">
      <li><i class="fa-solid fa-check"></i><span><strong>Positive Energy</strong>: fuels growth and productivity.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Edge &amp; Resiliency</strong>: relentless spirit of possibility.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Executing to Win</strong>: transparency, integrity, real value.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Alignment of Passion</strong>: matching talent to client need.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Value-Creation Leadership</strong>: team and client equally prioritized.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Trust</strong>: keeping commitments with kindness and accountability.</span></li>
      <li><i class="fa-solid fa-check"></i><span><strong>Ethical Decision-Making</strong>: principles guide every decision.</span></li>
    </ul>
    <a href="<?= $home_base ?>about/" class="btn-glass">More About Virtual Teammate <i class="fa-solid fa-arrow-right"></i></a>
  </div>
  <div class="svc-side-img reveal d2">
    <img src="<?= $home_base ?>images/photos/healthcare/Why-the-Healthcare-Industry-Is-Turning-to-Virtual-Assistants.webp" alt="Virtual Teammate culture — building value together" loading="lazy"/>
  </div>
</section>

<div class="divider"></div>

<!-- HIRING PROCESS / FUNNEL -->
<section class="svc-proc" id="hiring">
  <div style="text-align:center;max-width:720px;margin:0 auto;" class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-route"></i> Our Application &amp; Orientation Process</div>
    <h2 class="svc-h2">Eight steps. <em>Transparent. No ghosting.</em></h2>
    <p class="sec-sub">We respect your time. Every applicant gets a response, and every hire walks into Day 1 ready.</p>
  </div>
  <div class="proc-steps">
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">01</div><i class="fa-solid fa-file-lines pstep-ico"></i></div><h3 class="pstep-title">Apply for a position</h3><p class="pstep-desc">Browse our open roles and submit your application with your resume and target role.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">02</div><i class="fa-solid fa-magnifying-glass pstep-ico"></i></div><h3 class="pstep-title">Application review</h3><p class="pstep-desc">Our recruitment team reviews qualifications and confirms fit with the role spec.</p></div>
    <div class="pstep reveal d3"><div class="pstep-head"><div class="pstep-num">03</div><i class="fa-solid fa-clipboard-check pstep-ico"></i></div><h3 class="pstep-title">Initial assessment</h3><p class="pstep-desc">Three short assessments: EFSET English language test, IQ test, and Cultural Index.</p></div>
    <div class="pstep reveal d4"><div class="pstep-head"><div class="pstep-num">04</div><i class="fa-solid fa-headset pstep-ico"></i></div><h3 class="pstep-title">Interview call</h3><p class="pstep-desc">Scheduled in the 4 PM&ndash;9 PM PHT window. Includes a technical skills assessment for your role.</p></div>
    <div class="pstep reveal d5"><div class="pstep-head"><div class="pstep-num">05</div><i class="fa-solid fa-laptop pstep-ico"></i></div><h3 class="pstep-title">IT check</h3><p class="pstep-desc">We verify your hardware, internet stability, headset, and workspace meet our remote-work standards.</p></div>
    <div class="pstep reveal d6"><div class="pstep-head"><div class="pstep-num">06</div><i class="fa-solid fa-graduation-cap pstep-ico"></i></div><h3 class="pstep-title">Orientation</h3><p class="pstep-desc">Comprehensive onboarding: VT culture, role-specific training, HIPAA (for healthcare roles), tools.</p></div>
    <div class="pstep reveal d1"><div class="pstep-head"><div class="pstep-num">07</div><i class="fa-solid fa-users-viewfinder pstep-ico"></i></div><h3 class="pstep-title">Endorsement to talent pool</h3><p class="pstep-desc">Your profile is circulated to qualified clients. Interview opportunities follow within days.</p></div>
    <div class="pstep reveal d2"><div class="pstep-head"><div class="pstep-num">08</div><i class="fa-solid fa-rocket pstep-ico"></i></div><h3 class="pstep-title">Client selection &amp; onboarding</h3><p class="pstep-desc">Kick-off call with your assigned client. You start work, supported by your CSM from day one.</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- TESTIMONIALS -->
<section class="sec">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-quote-left"></i> What Our Teammates Say</div>
    <h2 class="svc-h2">Real voices, <em>real experiences</em></h2>
  </div>
  <div class="svc-bens-grid" style="margin-top:30px;">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-quote-left"></i></span><h3>Jessavel</h3><p>&ldquo;The culture of collaboration and cooperation makes it easy to deliver your best work.&rdquo;</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-quote-left"></i></span><h3>Junaline</h3><p>&ldquo;The interviewer&rsquo;s professionalism and friendly manner created a comfortable, respectful environment.&rdquo;</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-quote-left"></i></span><h3>John Carlo</h3><p>&ldquo;I appreciate how the team assisted me from application to client matching: nothing was opaque.&rdquo;</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-quote-left"></i></span><h3>April</h3><p>&ldquo;The admin team is very supportive, and to me, that matters most when you work remote.&rdquo;</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-quote-left"></i></span><h3>Vince</h3><p>&ldquo;It&rsquo;s great that a company doesn&rsquo;t ghost applicants. Kudos for a streamlined recruitment process.&rdquo;</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-quote-left"></i></span><h3>Charlie</h3><p>&ldquo;Jessa is doing great. Right where I would expect: well-matched, well-supported, ready on Day 1.&rdquo;</p></div>
  </div>
</section>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq" style="padding-top:70px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> Applicant FAQs</div><h2 class="svc-h2">Frequently asked questions</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-briefcase"></i> What roles do you hire for?</div><div class="faq-a">Healthcare (medical scribes, billers, receptionists, assistants), dental (admin, billing, scribes, coordinators, reception), administrative support, customer service, financial services, marketing, sales, and business intelligence.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-house-laptop"></i> Is the work fully remote?</div><div class="faq-a">Yes. Every role is 100% remote. You work from home with a stable internet connection: we verify the setup before placement.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-clock"></i> What hours will I work?</div><div class="faq-a">You&rsquo;re matched to a client&rsquo;s US business hours (typically aligned to a US time zone). Interviews are held in the 4 PM&ndash;9 PM PHT window for Philippines-based applicants.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-user-doctor"></i> Do I need healthcare experience?</div><div class="faq-a">For medical and dental roles, prior healthcare/dental administrative experience is preferred. Other roles (admin, sales, marketing, finance, BI) require general professional experience and strong English.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Will I be HIPAA trained?</div><div class="faq-a">Yes, all healthcare and dental teammates complete full HIPAA training during orientation, before client placement.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-comments"></i> How long does the hiring process take?</div><div class="faq-a">Most candidates complete the process within 1&ndash;2 weeks from application to talent-pool endorsement, then client interviews begin shortly after.</div></div>
  </div>
</section>

<div class="divider"></div>

<!-- FINAL CTA -->
<section class="svc-cta">
  <h2>Ready to <em style="color:var(--gold);font-style:normal;">apply</em>?</h2>
  <p>We are always on the lookout for talented individuals who align with our values and mission. Explore our open positions and find your fit at Virtual Teammate.</p>
  <div class="svc-cta-btns">
    <a href="https://virtualteammate-1719208705.teamtailor.com/" target="_blank" rel="noopener" class="btn-primary">Explore Open Positions <i class="fa-solid fa-arrow-right"></i></a>
    <a href="<?= $home_base ?>about/" class="btn-glass">About Virtual Teammate <i class="fa-solid fa-building"></i></a>
  </div>
</section>

</main>
<?php
$lf_mode       = 'form';   // job application — keep the inline form, not the booking CTA
$lf_source     = 'careers';
$lf_form       = 'careers-application';
$lf_title      = 'Start your application';
$lf_sub        = 'Tell us about yourself and the role you\'re after. Our talent team reviews every application within 1–2 business days.';
$lf_cta        = 'Submit application';
$lf_thanks     = 'Thank you for applying! Our talent team will review your application and reach out within 1–2 business days.';
$lf_company_ph = 'City / country';
$lf_msg_ph     = 'Tell us about your experience and the role you want.';
$lf_roles      = ['Healthcare VA', 'Dental VA', 'Administrative Support', 'Customer Service', 'Financial Services', 'Marketing', 'Sales', 'Business Intelligence'];
include __DIR__ . '/../includes/footer.php';
?>
