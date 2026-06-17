<?php
/**
 * Reusable "Book a Meeting" scheduler modal (#cta-book).
 *
 * A large modal hosting the embedded HubSpot meeting scheduler — the same widget
 * used by the homepage Strategy Call & Jumpstart modal. Opened by any link to
 * #cta-book (CSS :target shows it, so it works with JS off). Included once by
 * includes/lead-form.php in its default "book" mode.
 *
 * Guarded so it is only emitted once per request.
 */
if (defined('VT_BOOK_MODAL_RENDERED')) { return; }
define('VT_BOOK_MODAL_RENDERED', true);
?>
<div class="cta-modal" id="cta-book" role="dialog" aria-modal="true" aria-labelledby="cbm-h">
  <a class="cta-modal-scrim" href="#" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card cta-modal-card--lg">
    <a class="cta-modal-x" href="#" aria-label="Close">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-calendar-check"></i> Book a Meeting</div>
    <h2 class="cta-modal-h" id="cbm-h">Book Time With a US-Based Client Success Manager</h2>
    <p class="cta-modal-sub">Pick a time that works for you, we&rsquo;ll scope the right roles, give you a transparent quote, and map next steps. No obligation, covered by the 30-Day Right-Fit Promise.</p>
    <div class="cta-book-embed">
      <!-- Start of Meetings Embed Script -->
      <div class="meetings-iframe-container" data-src="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin?embed=true"></div>
      <!-- End of Meetings Embed Script -->
    </div>
  </div>
</div>
<!-- HubSpot Meetings embed loader — a resize nudge on open sizes the iframe correctly. -->
<script type="text/javascript" src="https://static.hsappstatic.net/MeetingsEmbed/ex/MeetingsEmbedCode.js"></script>
<script>
window.addEventListener('hashchange', function () {
  if (location.hash === '#cta-book') {
    setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 80);
  }
});
</script>
