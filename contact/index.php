<?php
$page_title       = 'Contact Virtual Teammate — Phoenix HQ + Global VA Network | (480) 847-2498';
$page_description = 'Reach Virtual Teammate at 2425 East Camelback Road, Phoenix, AZ 85016, or call (480) 847-2498. Email clientsuccess@virtualteammate.com. US-owned HQ with a globally vetted VA bench and a US-based Client Success Manager on every engagement.';
$og_title         = 'Contact Virtual Teammate — Phoenix HQ + Global VA Network';
$og_description   = 'US-owned office in Phoenix, AZ. Global bench across 5 continents. Talk to a real US-based Client Success Manager today.';
$canonical        = 'https://virtualteammate.com/contact/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',    'url' => '/'],
  ['name' => 'Contact', 'url' => '/contact/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@graph":[
    {
      "@type":"ContactPage",
      "@id":"https://virtualteammate.com/contact/#contactpage",
      "url":"https://virtualteammate.com/contact/",
      "name":"Contact Virtual Teammate",
      "description":"Phoenix HQ, phone, email, and global VA network coverage.",
      "isPartOf":{"@id":"https://virtualteammate.com/#website"}
    },
    {
      "@type":"LocalBusiness",
      "@id":"https://virtualteammate.com/#org",
      "name":"Virtual Teammate",
      "url":"https://virtualteammate.com/",
      "logo":"https://virtualteammate.com/images/logo.webp",
      "image":"https://virtualteammate.com/images/logo.webp",
      "priceRange":"$$",
      "address":{
        "@type":"PostalAddress",
        "streetAddress":"2425 East Camelback Road",
        "addressLocality":"Phoenix",
        "addressRegion":"AZ",
        "postalCode":"85016",
        "addressCountry":"US"
      },
      "geo":{
        "@type":"GeoCoordinates",
        "latitude":"33.5097",
        "longitude":"-112.0307"
      },
      "contactPoint":[
        {
          "@type":"ContactPoint",
          "telephone":"+1-480-847-2498",
          "contactType":"customer service",
          "email":"clientsuccess@virtualteammate.com",
          "areaServed":["US","CA","GB","AU"],
          "availableLanguage":["English"]
        },
        {
          "@type":"ContactPoint",
          "telephone":"+1-480-847-2498",
          "contactType":"sales",
          "email":"clientsuccess@virtualteammate.com",
          "areaServed":["US","CA","GB","AU"]
        }
      ],
      "areaServed":["US","CA","GB","AU"],
      "openingHoursSpecification":{
        "@type":"OpeningHoursSpecification",
        "dayOfWeek":["Monday","Tuesday","Wednesday","Thursday","Friday"],
        "opens":"08:00",
        "closes":"18:00"
      }
    }
  ]
}
</script>

<style>
/* Contact page — scoped overrides. */
.ct-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-top:34px;}
@media (max-width:980px){.ct-grid{grid-template-columns:1fr;}}
.ct-card{background:var(--glass-bg,rgba(255,255,255,0.04));backdrop-filter:blur(var(--glass-blur,18px));-webkit-backdrop-filter:blur(var(--glass-blur,18px));border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:28px 24px;text-align:center;transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease;}
.ct-card:hover{transform:translateY(-4px);border-color:rgba(223,169,73,.45);box-shadow:0 18px 50px -22px rgba(223,169,73,.35);}
.ct-card .ico-circle{margin:0 auto 14px;}
.ct-card h3{font-size:18px;font-weight:700;margin:0 0 6px;letter-spacing:-.2px;}
.ct-card .ct-lbl{font-size:11px;text-transform:uppercase;letter-spacing:1.1px;color:var(--gold,#d4a64a);font-weight:600;margin-bottom:14px;display:block;}
.ct-card a.ct-link{display:inline-block;color:#fff;font-size:17px;font-weight:600;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.22);padding-bottom:3px;margin-bottom:10px;transition:border-color .2s,color .2s;}
.ct-card a.ct-link:hover{color:var(--gold,#d4a64a);border-color:var(--gold,#d4a64a);}
.ct-card .ct-meta{font-size:13px;color:var(--text-mute,#a8a7c3);line-height:1.55;}
.ct-card .ct-cta{display:inline-flex;align-items:center;gap:8px;margin-top:14px;font-size:13px;font-weight:600;color:var(--gold,#d4a64a);text-decoration:none;}
.ct-card .ct-cta:hover{text-decoration:underline;}
.ct-map-wrap{margin-top:34px;border-radius:20px;overflow:hidden;border:1px solid rgba(255,255,255,.08);box-shadow:0 30px 80px -40px rgba(0,0,0,.6);position:relative;background:#0a0918;}
.ct-map-wrap iframe{display:block;width:100%;height:480px;border:0;filter:saturate(.85) contrast(.95);}
@media (max-width:768px){.ct-map-wrap iframe{height:360px;}}
.ct-map-meta{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;background:linear-gradient(180deg,rgba(15,12,40,.7),rgba(10,9,24,.95));flex-wrap:wrap;gap:10px;}
.ct-map-meta .ct-map-addr{font-size:14px;color:var(--text-soft,#c9c8e2);}
.ct-map-meta .ct-map-addr strong{color:#fff;}
.ct-map-meta a{color:var(--gold,#d4a64a);text-decoration:none;font-weight:600;font-size:13px;display:inline-flex;align-items:center;gap:6px;}
.ct-map-meta a:hover{text-decoration:underline;}
.ct-quick{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-top:34px;}
@media (max-width:980px){.ct-quick{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media (max-width:520px){.ct-quick{grid-template-columns:1fr;}}
.ct-quick-card{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:18px;text-align:center;}
.ct-quick-card .ico{font-size:22px;color:var(--gold,#d4a64a);margin-bottom:8px;}
.ct-quick-card .nm{font-size:14px;font-weight:600;color:#fff;margin-bottom:4px;}
.ct-quick-card .sub{font-size:12px;color:var(--text-mute,#a8a7c3);line-height:1.5;}
</style>

<main>

<!-- HERO -->
<header class="svc-hero">
  <div class="reveal">
    <div class="sec-lbl"><i class="fa-solid fa-headset"></i> Contact Virtual Teammate</div>
    <h1 class="svc-h1">Talk to a <em>Real Person</em> in Our Phoenix HQ &mdash; Backed by a Global Bench</h1>
    <p class="svc-p">Whether you&rsquo;re scoping a healthcare engagement, asking about pricing, or chasing a specific role spec &mdash; you&rsquo;ll reach a <strong>US-based Client Success Manager (CSM)</strong>, not an offshore queue. Most inbound requests get a reply within <strong>one business day</strong>.</p>
    <div class="svc-hero-ctas">
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book My Practice Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
      <a href="tel:+14808472498" class="btn-glass">Call (480) 847-2498 <i class="fa-solid fa-phone"></i></a>
    </div>
    <span class="btn-mini-note"><i class="fa-solid fa-clock" style="color:var(--gold);margin-right:6px;"></i>Reply within 1 business day &middot; Mon&ndash;Fri, 8am&ndash;6pm MST</span>
  </div>
  <div class="svc-hero-vis reveal d2" aria-hidden="true">
    <div class="hv-chip c1"><i class="fa-solid fa-location-dot"></i> Phoenix, AZ HQ</div>
    <div class="hv-chip c2"><i class="fa-solid fa-earth-americas"></i> Global Bench</div>
    <div class="hv-card">
      <img src="<?= $home_base ?>images/photos/healthcare/Healthcare-Virtual-Assistants-for-Efficient-Operations.webp" alt="Virtual Teammate US-based Client Success Manager ready to talk to you" loading="lazy"/>
    </div>
  </div>
</header>

<!-- STATS -->
<div class="svc-stats reveal">
  <div class="svc-stat"><div class="svc-stat-num">1</div><div class="svc-stat-lbl">Business-Day Reply</div></div>
  <div class="svc-stat"><div class="svc-stat-num">100%</div><div class="svc-stat-lbl">US-Based CSMs</div></div>
  <div class="svc-stat"><div class="svc-stat-num">5</div><div class="svc-stat-lbl">Continents Covered</div></div>
  <div class="svc-stat"><div class="svc-stat-num">4</div><div class="svc-stat-lbl">Time Zones Served (US/CA/GB/AU)</div></div>
</div>

<!-- 3-CHANNEL CONTACT -->
<section class="sec" id="reach" style="padding-top:60px;">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-comments"></i> Three Ways to Reach Us</div>
    <h2 class="svc-h2">Pick the Channel <em>That Fits Your Question</em></h2>
    <p class="sec-sub" style="max-width:720px;margin:0 auto;">Every channel routes to a US-based teammate. No call trees, no offshore queues, no &ldquo;we&rsquo;ll get back to you.&rdquo;</p>
  </div>

  <div class="ct-grid">
    <div class="ct-card reveal d1">
      <span class="ico-circle lg"><i class="fa-solid fa-phone"></i></span>
      <span class="ct-lbl">Phone</span>
      <h3>Call Us Directly</h3>
      <a class="ct-link" href="tel:+14808472498">(480) 847-2498</a>
      <div class="ct-meta">Live answer during business hours.<br>Voicemail returned same business day.</div>
      <a class="ct-cta" href="tel:+14808472498">Tap to call <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="ct-card reveal d2">
      <span class="ico-circle lg"><i class="fa-solid fa-envelope"></i></span>
      <span class="ct-lbl">Email</span>
      <h3>Email Client Success</h3>
      <a class="ct-link" href="mailto:clientsuccess@virtualteammate.com">clientsuccess@<wbr/>virtualteammate.com</a>
      <div class="ct-meta">Reply within 1 business day.<br>Use this for scope, pricing or RFPs.</div>
      <a class="ct-cta" href="mailto:clientsuccess@virtualteammate.com">Send us a note <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="ct-card reveal d3">
      <span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span>
      <span class="ct-lbl">Book a Call</span>
      <h3>Value Strategy Session</h3>
      <a class="ct-link" href="#cta-book" data-cta-intent="book">30-min consult</a>
      <div class="ct-meta">No commitment. Walk out with a scope, a price and a timeline.</div>
      <a class="ct-cta" href="#cta-book" data-cta-intent="book">Pick a time <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>

  <!-- QUICK BAR — hours / address / portal -->
  <div class="ct-quick">
    <div class="ct-quick-card reveal d1">
      <div class="ico"><i class="fa-solid fa-clock"></i></div>
      <div class="nm">Business Hours</div>
      <div class="sub">Mon &ndash; Fri<br>8:00am &ndash; 6:00pm MST</div>
    </div>
    <div class="ct-quick-card reveal d2">
      <div class="ico"><i class="fa-solid fa-location-dot"></i></div>
      <div class="nm">HQ Address</div>
      <div class="sub">2425 East Camelback Road<br>Phoenix, AZ 85016</div>
    </div>
    <div class="ct-quick-card reveal d3">
      <div class="ico"><i class="fa-solid fa-globe"></i></div>
      <div class="nm">Service Area</div>
      <div class="sub">United States, Canada,<br>United Kingdom, Australia</div>
    </div>
    <div class="ct-quick-card reveal d4">
      <div class="ico"><i class="fa-solid fa-lock"></i></div>
      <div class="nm">Existing Clients</div>
      <div class="sub"><a href="/portal/" style="color:var(--gold,#d4a64a);text-decoration:none;font-weight:600;">Portal login &raquo;</a></div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- MAP -->
<section class="sec" id="visit" style="padding-top:60px;">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl"><i class="fa-solid fa-map-location-dot"></i> Visit Our Phoenix HQ</div>
    <h2 class="svc-h2">2425 East Camelback Road, <em>Phoenix, AZ 85016</em></h2>
    <p class="sec-sub" style="max-width:720px;margin:0 auto;">A real office, a real US team, and a real address you can put in a procurement form.</p>
  </div>

  <div class="ct-map-wrap reveal">
    <iframe
      src="https://www.google.com/maps?q=2425+East+Camelback+Road,+Phoenix,+AZ+85016&output=embed"
      width="100%" height="480" style="border:0;"
      allowfullscreen
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      title="Virtual Teammate Phoenix HQ — 2425 East Camelback Road"></iframe>
    <div class="ct-map-meta">
      <div class="ct-map-addr">
        <strong>Virtual Teammate HQ</strong> &middot; 2425 East Camelback Road, Phoenix, AZ 85016
      </div>
      <a href="https://www.google.com/maps/dir/?api=1&destination=2425+East+Camelback+Road,+Phoenix,+AZ+85016" target="_blank" rel="noopener">
        <i class="fa-solid fa-diamond-turn-right"></i> Get Directions
      </a>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- GLOBAL BENCH — copied from homepage -->
<section class="global global-compact" id="global" aria-labelledby="global-h">
  <div class="global-grid">
    <div class="global-l reveal">
      <div class="sec-lbl"><i class="fa-solid fa-earth-americas"></i> Global Coverage &middot; Local Cadence</div>
      <h2 class="sec-h2" id="global-h">A <em>Global Bench</em> of HIPAA-Certified Medical &amp; Dental Talent &mdash; On Your Time Zone</h2>
      <p class="sec-sub">A global recruiting reach plus a single US point of contact. Your dedicated Client Success Manager (CSM) runs the bench so you don&rsquo;t have to think about it.</p>

      <ul class="global-benefits">
        <li><span class="ico-circle"><i class="fa-solid fa-clock"></i></span><span><strong>24/7 coverage in your US time zone.</strong> Morning, afternoon, evening or overnight shifts &mdash; one vendor, no compromises.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-check"></i></span><span><strong>Best-fit talent, not least-cost talent.</strong> A bigger bench means we recruit for your specialty, EHR, language and accent &mdash; not just availability.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-sack-dollar"></i></span><span><strong>Up to 73% lower staffing cost</strong> than a US in-house hire. Flat rate, all-in &mdash; no benefits, payroll tax, recruiter fees or PTO.</span></li>
        <li><span class="ico-circle"><i class="fa-solid fa-user-tie"></i></span><span><strong>A named Client Success Manager (CSM) from day one.</strong> Owns onboarding, quality, backup coverage and quarterly reviews. It&rsquo;s how we hold 95%+ retention.</span></li>
      </ul>
    </div>

    <div class="reveal d2">
      <div class="world" aria-label="Virtual Teammate global coverage — one anchor per continent">
        <div class="world-grid"></div>
        <img class="world-map-img" src="<?= $home_base ?>images/world-map.svg" alt="World map showing Virtual Teammate global talent coverage" loading="lazy">
        <svg class="world-arc" viewBox="0 0 1000 500" preserveAspectRatio="none" aria-hidden="true">
          <defs>
            <linearGradient id="arcGrad" x1="0" x2="1" y1="0" y2="0">
              <stop offset="0%" stop-color="rgba(223,169,73,0.7)"/>
              <stop offset="100%" stop-color="rgba(57,25,186,0.7)"/>
            </linearGradient>
          </defs>
          <!-- One connection from US HQ (Arizona) to each populated continent -->
          <path d="M160,270 Q210,330 300,380"/>
          <path d="M160,270 Q300,150 460,205"/>
          <path d="M160,270 Q360,370 525,345"/>
          <path d="M160,270 Q470,150 810,320"/>
        </svg>
        <!-- One anchor pin per continent (calibrated to the map projection) -->
        <div class="world-pin" style="top:54%;left:16%;"><div class="pin-lbl">North America &middot; HQ</div></div>
        <div class="world-pin" style="top:76%;left:30%;"><div class="pin-lbl">South America</div></div>
        <div class="world-pin" style="top:41%;left:46%;"><div class="pin-lbl">Europe</div></div>
        <div class="world-pin" style="top:69%;left:52%;"><div class="pin-lbl">Africa</div></div>
        <div class="world-pin" style="top:64%;left:81%;"><div class="pin-lbl">Asia</div></div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- FAQ -->
<section class="sec" id="faq" style="padding-top:60px;">
  <div class="reveal"><div class="sec-lbl"><i class="fa-solid fa-circle-question"></i> Pre-Contact FAQs</div><h2 class="svc-h2">Before You Reach Out &mdash; Common Questions</h2></div>
  <div class="faq-grid">
    <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-clock"></i> How quickly will I get a response?</div><div class="faq-a">Most inbound requests get a personal reply within one business day. Phone calls during MST business hours are answered live; voicemails are returned same day.</div></div>
    <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-user-tie"></i> Who will I talk to?</div><div class="faq-a">A US-based Client Success Manager (CSM) &mdash; not an offshore queue and not a chatbot. For new healthcare engagements, our founder Chris McShanag often joins the strategy call personally.</div></div>
    <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-handshake"></i> Do I have to commit to anything?</div><div class="faq-a">No. The strategy session is a 30-minute value scoping call. You walk out with a clear engagement scope, a transparent price, and a timeline &mdash; no obligation, no sales pressure.</div></div>
    <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Can we talk about HIPAA / BAA right away?</div><div class="faq-a">Yes &mdash; every healthcare engagement is BAA-compatible, and we can share our HIPAA compliance documentation and BAA template during or right after the strategy call.</div></div>
    <div class="faq-item reveal d5"><div class="faq-q"><i class="fa-solid fa-location-dot"></i> Are you really a US company?</div><div class="faq-a">Yes &mdash; US-owned, headquartered at 2425 East Camelback Road, Phoenix, AZ 85016. We recruit globally so you get the best fit, not the cheapest fit.</div></div>
    <div class="faq-item reveal d6"><div class="faq-q"><i class="fa-solid fa-comments"></i> What should I prepare for the first call?</div><div class="faq-a">Bring the one workflow you most want to hand off, the KPIs you want to move, and any constraints (EHR, time zone, language). We&rsquo;ll do the rest.</div></div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="svc-cta">
  <h2>Ready to <em style="color:var(--gold);font-style:normal;">Start the Conversation</em>?</h2>
  <p>Pick the channel that fits. We&rsquo;ll reply within one business day with a clear scope, a price, and a timeline &mdash; no commitment required.</p>
  <div class="svc-cta-btns">
    <a href="#cta-book" data-cta-intent="practice-audit" class="btn-primary">Book My Practice Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
    <a href="tel:+14808472498" class="btn-glass">Call (480) 847-2498 <i class="fa-solid fa-phone"></i></a>
  </div>
</section>

</main>
<?php
$lf_title = 'Prefer to Talk It Through? <em>Book a Meeting</em>';
$lf_sub   = 'Questions about scope, pricing or a custom engagement? Grab a time below and a US-based Client Success Manager will walk you through it &mdash; no obligation, reply within one business day either way.';
include __DIR__ . '/../includes/footer.php';
?>
