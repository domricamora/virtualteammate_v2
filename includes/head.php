<?php
// HTML pages must never be cached by browsers or intermediaries — otherwise
// content edits stay invisible until a hard refresh. The .htaccess sets
// `text/html access plus 0 seconds` via mod_expires, but not every host has
// mod_expires loaded; explicit headers here guarantee the no-cache promise.
// Static assets keep their own long-lived cache; they're invalidated via
// `?v={mtime}` query strings on the <link>/<script> tags below.
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}
// Asset-cache version helper (?v= on CSS/JS) — controlled by the super-admin
// portal's Site Cache toggle/flush. Safe default = filemtime-busting as before.
require_once __DIR__ . '/cache.php';
/**
 * Site head partial — meta, schema, font loads, CSS link.
 * Set the following BEFORE including this file to override defaults:
 *   $page_title          string  full <title> text
 *   $page_description    string  meta description
 *   $canonical           string  absolute canonical URL
 *   $og_title            string  OG / Twitter title (defaults to $page_title)
 *   $og_description      string  OG / Twitter description (defaults to $page_description)
 *   $is_homepage         bool    when true, emits the org + FAQ JSON-LD
 *   $body_class          string  optional class string for the <body>
 *   $breadcrumbs         array   [['name' => 'Services', 'url' => '/services/'], ...]
 *                                 emits BreadcrumbList JSON-LD on inner pages
 *   $faqs                array   [['q' => 'Question?', 'a' => 'Answer.'], ...]
 *                                 emits FAQPage JSON-LD on inner pages (homepage emits
 *                                 its own FAQ block, so this is ignored there). Keep the
 *                                 text identical to the visible FAQ section on the page.
 *   $robots              string  override default "index,follow,..." (e.g. "noindex" for utility pages)
 */
$site_url         = 'https://virtualteammate.com';
$page_title       = $page_title       ?? 'Virtual Teammate';
$page_description = $page_description ?? 'HIPAA-compliant medical & dental virtual assistants.';
$canonical        = $canonical        ?? $site_url . '/';
$og_title         = $og_title         ?? $page_title;
$og_description   = $og_description   ?? $page_description;
$is_homepage      = $is_homepage      ?? false;
// Indexing is allowed on every host (including localhost + staging). A page can
// still force its own directive by setting $robots before this include (e.g.
// 'noindex,nofollow' for utility pages).
$__vt_host        = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$__vt_nonprod     = str_contains($__vt_host, 'localhost') || str_starts_with($__vt_host, '127.0.0.1') || str_contains($__vt_host, 'staging');
$robots           = $robots           ?? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';
// Non-prod hosts get a self-referential canonical (and matching og:url) so that
// staging/localhost pages are validly self-canonical and can be indexed in their
// own right, instead of pointing cross-domain at production and being skipped.
if ($__vt_nonprod) {
    $__vt_scheme = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';
    $__vt_uri    = strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?');
    $canonical   = $__vt_scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $__vt_uri;
}
$breadcrumbs      = $breadcrumbs      ?? null;
$faqs             = $faqs             ?? null;
// Relative URL prefix back to site root. Homepage uses './'; subpages override
// (e.g. /services/<slug>/index.php sets '../../') so asset and link refs resolve
// correctly under both dev (localhost/vtnew/) and prod (virtualteammate.com/).
$home_base        = $home_base        ?? './';
$h = function ($v) { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="theme-color" content="#1a1535"/>
<?php /* Google Analytics 4 (gtag.js). Emitted on production only — $__vt_nonprod
        suppresses it on localhost/staging so dev traffic stays out of the property. */ ?>
<?php if (empty($__vt_nonprod)): ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9VTZ5Q9J6X"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-9VTZ5Q9J6X');
</script>
<?php endif; ?>
<title><?= $h($page_title) ?></title>
<meta name="description" content="<?= $h($page_description) ?>"/>
<meta name="robots" content="<?= $h($robots) ?>"/>
<meta name="googlebot" content="<?= $h($robots) ?>"/>
<meta name="format-detection" content="telephone=no"/>
<meta name="author" content="Virtual Teammate"/>
<meta name="google-site-verification" content="aFmbyBXzDf9yIsiP5XQuJ6zTTyCVdlZRZdrAvQgsNl0"/>
<link rel="canonical" href="<?= $h($canonical) ?>"/>

<!-- Open Graph -->
<meta property="og:type" content="website"/>
<meta property="og:site_name" content="Virtual Teammate"/>
<meta property="og:title" content="<?= $h($og_title) ?>"/>
<meta property="og:description" content="<?= $h($og_description) ?>"/>
<meta property="og:url" content="<?= $h($canonical) ?>"/>
<meta property="og:image" content="<?= $site_url ?>/images/logo.webp"/>

<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:title" content="<?= $h($og_title) ?>"/>
<meta name="twitter:description" content="<?= $h($og_description) ?>"/>
<meta name="twitter:image" content="<?= $site_url ?>/images/logo.webp"/>

<link rel="icon" type="image/png" sizes="32x32" href="<?= $home_base ?>images/favicon-32x32.png"/>
<link rel="icon" type="image/png" sizes="192x192" href="<?= $home_base ?>images/favicon-192x192.png"/>
<link rel="apple-touch-icon" sizes="180x180" href="<?= $home_base ?>images/apple-touch-icon.png"/>
<meta name="msapplication-TileImage" content="<?= $site_url ?>/images/favicon-192x192.png"/>
<link rel="preload" as="image" href="<?= $home_base ?>images/vt-logo.webp" fetchpriority="high"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<!-- Font Awesome loaded async so it doesn't block first paint; icons swap in on load. -->
<link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" onload="this.onload=null;this.rel='stylesheet'"/>
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/></noscript>
<link rel="stylesheet" href="<?= $home_base ?>css/style.css?v=<?= vt_asset_ver(__DIR__ . '/../css/style.css') ?>"/>

<?php if ($is_homepage): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebSite",
      "@id": "<?= $site_url ?>/#website",
      "url": "<?= $site_url ?>/",
      "name": "Virtual Teammate",
      "publisher": { "@id": "<?= $site_url ?>/#org" },
      "inLanguage": "en-US"
    },
    {
      "@type": "MedicalBusiness",
      "@id": "<?= $site_url ?>/#org",
      "name": "Virtual Teammate",
      "url": "<?= $site_url ?>/",
      "logo": "<?= $site_url ?>/images/logo.webp",
      "image": "<?= $site_url ?>/images/logo.webp",
      "telephone": "+1-480-847-2498",
      "email": "clientsuccess@virtualteammate.com",
      "priceRange": "$$",
      "description": "HIPAA-compliant medical and dental virtual assistant staffing: sourced from a global talent network, delivered in your time zone.",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "2425 East Camelback Road, Suite 400",
        "addressLocality": "Phoenix",
        "addressRegion": "AZ",
        "postalCode": "85016",
        "addressCountry": "US"
      },
      "areaServed": ["US","CA","GB","AU"],
      "medicalSpecialty": ["PrimaryCare","Dentistry","Surgical","Geriatric","Cardiovascular"],
      "aggregateRating": { "@type":"AggregateRating", "ratingValue":"4.9", "reviewCount":"200" },
      "review": [
        {
          "@type":"Review",
          "author":{"@type":"Organization","name":"Cancer Center"},
          "reviewRating":{"@type":"Rating","ratingValue":"5","bestRating":"5","worstRating":"1"},
          "reviewBody":"Our virtual teammate cleared insurance verifications 30% above goal and payment posting 48% over target, turning a challenging AR into one of the practice's strongest on record."
        },
        {
          "@type":"Review",
          "author":{"@type":"Organization","name":"Multi-Specialty Clinic"},
          "reviewRating":{"@type":"Rating","ratingValue":"5","bestRating":"5","worstRating":"1"},
          "reviewBody":"A dedicated billing teammate achieved pre-certs 60% over target and claims volume 47% above plan, keeping authorizations ahead of schedule and clean claims moving."
        },
        {
          "@type":"Review",
          "author":{"@type":"Organization","name":"Primary Care Group"},
          "reviewRating":{"@type":"Rating","ratingValue":"5","bestRating":"5","worstRating":"1"},
          "reviewBody":"Payment posting landed 40% over target and insurance verifications 44% over, streamlining the revenue cycle so claims go out clean and cash comes in faster."
        },
        {
          "@type":"Review",
          "author":{"@type":"Organization","name":"Endodontics & Oral Surgery Group"},
          "reviewRating":{"@type":"Rating","ratingValue":"5","bestRating":"5","worstRating":"1"},
          "reviewBody":"A specialty-billing teammate cleared claims 33% above target for an endodontics and oral-surgery group, a full surgical schedule billed on time."
        }
      ]
    },
    {
      "@type": "Service",
      "serviceType": "Medical Virtual Assistant Staffing",
      "provider": { "@id": "<?= $site_url ?>/#org" },
      "areaServed": ["US","CA","GB","AU"],
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Healthcare Virtual Assistant Services",
        "itemListElement": [
          { "@type":"Offer","itemOffered":{"@type":"Service","name":"Medical Billing Virtual Assistant"}},
          { "@type":"Offer","itemOffered":{"@type":"Service","name":"Medical Scribe Virtual Assistant"}},
          { "@type":"Offer","itemOffered":{"@type":"Service","name":"Patient Scheduling Virtual Assistant"}},
          { "@type":"Offer","itemOffered":{"@type":"Service","name":"Insurance Verification & Prior Authorization"}},
          { "@type":"Offer","itemOffered":{"@type":"Service","name":"Dental Billing Virtual Assistant"}},
          { "@type":"Offer","itemOffered":{"@type":"Service","name":"Dental Front-Desk Virtual Assistant"}}
        ]
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        { "@type":"Question","name":"Are your healthcare teammates HIPAA compliant?","acceptedAnswer":{"@type":"Answer","text":"Yes. Every healthcare and dental teammate completes HIPAA compliance training and certification before placement."}},
        { "@type":"Question","name":"How quickly can I get a teammate started?","acceptedAnswer":{"@type":"Answer","text":"Most clients receive a curated shortlist within days. Onboarding is typically complete within 1–2 weeks."}},
        { "@type":"Question","name":"Where are your virtual assistants based?","acceptedAnswer":{"@type":"Answer","text":"Virtual Teammate sources talent from a global network spanning the Philippines, Latin America, Africa, and South Asia: every teammate is matched to your US time zone."}},
        { "@type":"Question","name":"How much does a medical virtual assistant cost?","acceptedAnswer":{"@type":"Answer","text":"Transparent flat-rate pricing, typically 60–73% less than an equivalent in-house hire when factoring salary, benefits and overhead."}}
      ]
    }
  ]
}
</script>
<?php endif; ?>

<?php if (!empty($breadcrumbs) && is_array($breadcrumbs)): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
<?php foreach ($breadcrumbs as $i => $bc): ?>
    {
      "@type": "ListItem",
      "position": <?= $i + 1 ?>,
      "name": "<?= $h($bc['name']) ?>",
      "item": "<?= $h(strpos($bc['url'], 'http') === 0 ? $bc['url'] : $site_url . $bc['url']) ?>"
    }<?= $i < count($breadcrumbs) - 1 ? ',' : '' ?>
<?php endforeach; ?>
  ]
}
</script>
<?php endif; ?>

<?php if (!$is_homepage): /* Organization node — the homepage emits its own #org graph */ ?>
<script type="application/ld+json">
<?= json_encode([
  '@context'    => 'https://schema.org',
  '@type'       => 'Organization',
  '@id'         => $site_url . '/#org',
  'name'        => 'Virtual Teammate',
  'url'         => $site_url . '/',
  'logo'        => $site_url . '/images/logo.webp',
  'image'       => $site_url . '/images/logo.webp',
  'telephone'   => '+1-480-847-2498',
  'email'       => 'clientsuccess@virtualteammate.com',
  'description' => 'HIPAA-compliant medical and dental virtual assistant staffing: sourced from a global talent network, delivered in your time zone.',
  'address'     => [
    '@type'           => 'PostalAddress',
    'streetAddress'   => '2425 East Camelback Road, Suite 400',
    'addressLocality' => 'Phoenix',
    'addressRegion'   => 'AZ',
    'postalCode'      => '85016',
    'addressCountry'  => 'US',
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>

<?php if (!$is_homepage && !empty($faqs) && is_array($faqs)): /* FAQPage — homepage emits its own */ ?>
<script type="application/ld+json">
<?= json_encode([
  '@context'   => 'https://schema.org',
  '@type'      => 'FAQPage',
  'mainEntity' => array_map(static function ($f) {
    return [
      '@type'          => 'Question',
      'name'           => $f['q'],
      'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ];
  }, $faqs),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>
</head>
<body<?= !empty($body_class) ? ' class="' . $h($body_class) . '"' : '' ?>>
