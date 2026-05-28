<?php
$page_title       = 'Business & Non-Profit Virtual Assistants | Virtual Teammate';
$page_description = 'Virtual Teammate also staffs admin, sales, marketing, finance and customer-service VAs for businesses and non-profits. Same global network, same vetting, same flat-rate model.';
$og_title         = 'Business & Non-Profit Virtual Assistants — Virtual Teammate';
$og_description   = 'Admin, sales, marketing, finance & CX virtual assistants for businesses & non-profits — sourced globally, delivered in your time zone.';
$canonical        = 'https://virtualteammate.com/business/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',                       'url' => '/'],
  ['name' => 'Business & Non-Profit VAs',  'url' => '/business/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<script type="application/ld+json">
{
  "@context":"https://schema.org","@type":"Service",
  "serviceType":"Business Virtual Assistant Staffing",
  "name":"Business & Non-Profit Virtual Assistants",
  "description":"Administrative, sales, marketing, finance and customer-service virtual assistants for businesses and non-profit organizations — sourced from a global vetted network, matched to US time zones, billed at a transparent flat rate.",
  "provider":{"@type":"Organization","name":"Virtual Teammate","url":"https://virtualteammate.com/"},
  "areaServed":["US","CA","GB","AU"]
}
</script>

<main>
<header class="svc-hero">
  <div class="orb orb1"></div><div class="orb orb2"></div>
  <div class="svc-hero-inner reveal">
    <nav class="svc-bc" aria-label="Breadcrumb">
      <a href="<?= $home_base ?>">Home</a>
      <i class="fa-solid fa-chevron-right"></i>
      <span aria-current="page">Business &amp; Non-Profit VAs</span>
    </nav>
    <div class="svc-eyebrow"><i class="fa-solid fa-briefcase"></i> Business &amp; Non-Profit VAs</div>
    <h1 class="svc-h1">Beyond Healthcare: <em>Virtual Teammates</em> for Every Function</h1>
    <p class="svc-lead">Healthcare is our focus, but our network covers every back-office role a growing business or non-profit needs. <strong>Same global talent network, same multi-stage vetting, same transparent flat-rate model</strong> &mdash; matched to your US time zone and backed by the 30-Day Right-Fit Promise.</p>
    <div class="svc-trust">
      <div class="trust-item"><i class="fa-solid fa-globe"></i> 12+ Countries Sourced</div>
      <div class="trust-item"><i class="fa-solid fa-user-shield"></i> Background Checked</div>
      <div class="trust-item"><i class="fa-solid fa-rotate"></i> 30-Day Right-Fit Promise</div>
      <div class="trust-item"><i class="fa-solid fa-bolt"></i> Live in 1&ndash;2 Weeks</div>
    </div>
    <div class="svc-cta-row">
      <a href="<?= $home_base ?>#cta" class="btn-primary" data-cta-intent="strategy-call">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>#calculator" class="btn-glass">Calculate My Savings <i class="fa-solid fa-calculator"></i></a>
    </div>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-circle-check"></i> Multi-Stage Vetted</div>
    <div class="hv-chip c2"><i class="fa-solid fa-clock"></i> Your Time Zone</div>
    <div class="hv-card">
      <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=900&q=85" alt="Business virtual assistant working at laptop" loading="lazy"/>
    </div>
  </div>
</header>

<section class="svc-bens">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-list-check"></i> Roles We Staff</div>
    <h2 class="svc-h2">Pick a Function &mdash; <em>We&rsquo;ll Build the Bench</em></h2>
    <p class="sec-sub" style="max-width:680px;margin:0 auto;">From a single executive assistant to a full remote operations team, the same vetting standard that produces our HIPAA-certified healthcare placements applies here.</p>
  </div>
  <div class="svc-bens-grid">
    <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-clipboard"></i></span><h3>Administrative</h3><p>Executive assistants, calendar &amp; inbox managers, project coordinators, data entry, document control.</p></div>
    <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-bullseye"></i></span><h3>Sales &amp; SDR</h3><p>Inbound qualification, outbound prospecting, list research, CRM hygiene, appointment setting, follow-up sequences.</p></div>
    <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-bullhorn"></i></span><h3>Marketing</h3><p>Social-media management, content production, email automation, SEO assistants, paid-ad coordination, analytics reporting.</p></div>
    <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Finance &amp; Bookkeeping</h3><p>QuickBooks/Xero bookkeeping, AR &amp; AP, expense management, invoicing, budget reporting, payroll prep.</p></div>
    <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-headset"></i></span><h3>Customer Service</h3><p>Tier-1 support, ticket triage, live chat, refund &amp; returns, escalation handling &mdash; in your time zone, in your tone.</p></div>
    <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-hand-holding-heart"></i></span><h3>Non-Profit Operations</h3><p>Donor outreach, grant research, volunteer coordination, event logistics, social campaigns, board-meeting prep.</p></div>
  </div>
</section>

<section class="svc-cta" style="margin-top:60px;">
  <h2>Healthcare or Not, the Engine Is the Same</h2>
  <p>Curated shortlist in days, dedicated Client Success Manager from day one, transparent flat-rate pricing, 30-day right-fit guarantee. Tell us what you need &mdash; we&rsquo;ll build the bench.</p>
  <div class="svc-cta-btns">
    <a href="<?= $home_base ?>#cta" class="btn-primary" data-cta-intent="strategy-call">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
    <a href="<?= $home_base ?>#calculator" class="btn-glass">Calculate My Savings <i class="fa-solid fa-calculator"></i></a>
  </div>
</section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
