<?php
/**
 * Final CTA + live bench + "Other Business Roles" grid for /business/<slug>/ pages.
 * Parallel to service-cta.php, but wired to the business CTA funnel
 * (Operational Assessment / Buy Back Your Time) instead of the healthcare one.
 *
 * Set before include:
 *   $home_base   string  '../../' from /business/<slug>/index.php
 *   $biz_slug    string  current page slug (excluded from the related grid)
 *   $vtc_keywords array  (optional) role keywords to rank the bench preview
 */
$home_base = $home_base ?? '../../';
$biz_slug  = $biz_slug  ?? '';

$biz_all = [
  'administrative'        => ['title' => 'Administrative &amp; Executive', 'sub' => 'Inbox, calendar &amp; project coordination', 'icon' => 'fa-clipboard'],
  'sales-lead-generation' => ['title' => 'Sales &amp; Lead Generation',    'sub' => 'Prospecting, qualifying &amp; appointment setting', 'icon' => 'fa-bullseye'],
  'marketing'             => ['title' => 'Marketing &amp; Demand Gen',     'sub' => 'Social, email, SEO &amp; paid-ad support', 'icon' => 'fa-bullhorn'],
  'finance-bookkeeping'   => ['title' => 'Finance &amp; Bookkeeping',      'sub' => 'Bookkeeping, invoicing &amp; AR follow-up', 'icon' => 'fa-sack-dollar'],
  'customer-support'      => ['title' => 'Customer Service &amp; Support', 'sub' => 'Tier-1 support, live chat &amp; retention', 'icon' => 'fa-headset'],
  'non-profit'            => ['title' => 'Non-Profit Operations',          'sub' => 'Donor outreach, grants &amp; volunteers', 'icon' => 'fa-hand-holding-heart'],
];

/* Live teammate preview — every non-clinical role (excludes Medical & Dental),
   ranked toward this page's function via $vtc_keywords. Card CTA opens the
   site-wide "Request this teammate" modal (#cta-request, from cta-modals.php). */
$vtc_exclude_depts = ['Medical', 'Dental'];
$vtc_keywords      = $vtc_keywords ?? [];
$vtc_label         = 'Meet the Bench';
$vtc_heading       = 'Business-ready <em>Virtual Teammates</em>';
$vtc_sub           = 'A sample of real, vetted business teammates across admin, sales, marketing, finance and customer service, matched to your time zone and ready in 1&ndash;2 weeks.';
$vtc_cta_href      = '#cta-request';
$vtc_cta_intent    = 'request';
$vtc_cta_label     = 'Request this teammate';
$vtc_cta_vt        = true;   // stamp the chosen teammate into the request modal
include __DIR__ . '/vt-cards.php';
?>
<!-- FINAL CTA -->
<section class="svc-cta">
  <h2>Ready to buy back your team&rsquo;s <em style="color:var(--gold);font-style:normal;">time</em>?</h2>
  <p>Tell us the function eating your week and we&rsquo;ll build the bench: a curated shortlist within days, vetted talent matched to your time zone and tools, live in 1&ndash;2 weeks. No commitment, no recruiter fees.</p>
  <div class="svc-cta-btns">
    <a href="#cta-ops-assessment" class="btn-primary" data-cta-intent="ops-assessment">Schedule My Operational Assessment <i class="fa-solid fa-arrow-right"></i></a>
    <a href="#cta-buyback" class="btn-glass" data-cta-intent="buyback">Buy Back Your Company&rsquo;s Time <i class="fa-solid fa-clock"></i></a>
    <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
  </div>
</section>

<!-- RELATED BUSINESS ROLES -->
<section class="svc-related" aria-labelledby="biz-related-h">
  <div class="svc-related-h" id="biz-related-h">Explore Other Business Roles</div>
  <div class="svc-related-grid">
    <?php foreach ($biz_all as $slug => $biz): if ($slug === $biz_slug) continue; ?>
      <a class="svc-related-card" href="<?= $home_base ?>business/<?= $slug ?>/">
        <span class="ico-circle lg"><i class="fa-solid <?= $biz['icon'] ?>"></i></span>
        <div>
          <div class="svc-related-card-t"><?= $biz['title'] ?></div>
          <div class="svc-related-card-s"><?= $biz['sub'] ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php
// Business CTA modals (#cta-ops-assessment, #cta-buyback) + their lazy HubSpot embeds.
include __DIR__ . '/business-modals.php';
include __DIR__ . '/hubspot-loader.php';
