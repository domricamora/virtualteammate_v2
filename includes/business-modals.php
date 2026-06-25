<?php
/**
 * Business entry-point modals — Operational Assessment (#cta-ops-assessment) and
 * Buy Back Your Time (#cta-buyback). Shared by the /business/ hub and every
 * /business/<slug>/ inner page so the business CTA funnel works in place.
 *
 * Open/close/ESC/scroll-lock behavior is provided by the generic .cta-modal
 * handler in includes/request-modal.php (pulled in site-wide via footer →
 * cta-modals.php on non-home pages). The HubSpot Meetings embeds are lazy-loaded
 * by includes/hubspot-loader.php. Guarded so a page that includes it twice (or
 * already shipped its own copies) never double-renders.
 *
 * Set $home_base before include.
 */
if (defined('VT_BUSINESS_MODALS_RENDERED')) { return; }
define('VT_BUSINESS_MODALS_RENDERED', true);
$home_base = $home_base ?? '../../';
?>
<div class="cta-modal" id="cta-ops-assessment" role="dialog" aria-modal="true" aria-labelledby="bcm-oa-h">
  <a class="cta-modal-scrim" href="#" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-clipboard-check"></i> Operational Assessment</div>
    <h2 class="cta-modal-h" id="bcm-oa-h">Schedule Your Operational Assessment</h2>
    <p class="cta-modal-sub">Pick a time that works for you, a Dedicated Client Success Manager will map your busiest back-office workflows and tell you exactly what to delegate first. Diagnostic only, no obligation.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>

<div class="cta-modal" id="cta-buyback" role="dialog" aria-modal="true" aria-labelledby="bcm-bb-h">
  <a class="cta-modal-scrim" href="#" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-hourglass-half"></i> Buy Back Your Time</div>
    <h2 class="cta-modal-h" id="bcm-bb-h">Buy Back Your Company&rsquo;s Time</h2>
    <p class="cta-modal-sub">Pick a time below and we&rsquo;ll scope the function eating your team&rsquo;s week, match a vetted flat-rate teammate, and map your first steps: live in 1&ndash;2 weeks, covered by the 30-Day Right-Fit Promise.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>
