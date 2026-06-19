<?php
/**
 * Reusable "Strategy Call & Jumpstart" scheduler modal (#cta-strategy-call).
 *
 * The same HubSpot scheduler widget as #cta-book. It relies on the MeetingsEmbed
 * loader emitted by includes/book-modal.php, so include book-modal.php BEFORE this
 * (the cta-modals.php hub does exactly that). Opened via the #cta-strategy-call
 * hash (CSS :target). Guarded so it renders once per request.
 */
if (defined('VT_JUMPSTART_MODAL_RENDERED')) { return; }
define('VT_JUMPSTART_MODAL_RENDERED', true);
?>
<div class="cta-modal" id="cta-strategy-call" role="dialog" aria-modal="true" aria-labelledby="jsm-h">
  <a class="cta-modal-scrim" href="#" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#" aria-label="Close">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-calendar-check"></i> Strategy Call &amp; Jumpstart</div>
    <h2 class="cta-modal-h" id="jsm-h">Book my strategy call</h2>
    <p class="cta-modal-sub">Pick a time below and we&rsquo;ll scope your needs, define the role, and map your first 30 days: so your teammate is productive fast. No commitment, covered by the 30-Day Right-Fit Promise.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>
<script>
window.addEventListener('hashchange', function () {
  if (location.hash === '#cta-strategy-call') {
    setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 80);
  }
});
</script>
