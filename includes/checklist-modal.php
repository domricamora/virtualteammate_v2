<?php
/**
 * Reusable "HIPAA VA buyer's checklist" modal (#cta-buyers-checklist).
 *
 * Opens on the SAME page via the #cta-buyers-checklist hash (CSS :target, so it
 * works with JS off). Posts to lead.php and creates a "buyers-checklist" lead.
 * Mirrors the homepage's inline checklist modal so inner-page CTAs no longer have
 * to jump to the homepage.
 *
 * Self-contained & idempotent (guard). Ships the markup + a dedicated submit
 * handler (fetch -> thank-you swap). Generic .cta-modal scroll-lock / ESC /
 * autofocus behavior is provided elsewhere on the page (request-modal.php on
 * inner pages, or the homepage's own script).
 *
 * Set $home_base before include.
 */
if (defined('VT_CHECKLIST_MODAL_RENDERED')) { return; }
define('VT_CHECKLIST_MODAL_RENDERED', true);
$home_base = $home_base ?? './';
?>
<div class="cta-modal" id="cta-buyers-checklist" role="dialog" aria-modal="true" aria-labelledby="cbm-bc-h">
  <a class="cta-modal-scrim" href="#" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-file-lines"></i> Just exploring</div>
    <h2 class="cta-modal-h" id="cbm-bc-h">Grab the HIPAA VA buyer&rsquo;s checklist</h2>
    <p class="cta-modal-sub">Unlock new levels of productivity and patient care. Enter your email to receive our HIPAA VA buyer&rsquo;s checklist and learn how to choose the right virtual staffing partner for long-term success.</p>
    <form class="cta-modal-form" id="ctaChecklistForm" method="post" action="<?= $home_base ?>lead.php"
          data-lead-thanks="Check your inbox: your checklist is on the way.">
      <input type="hidden" name="intent" value="buyers-checklist">
      <input type="hidden" name="form" value="checklist">
      <input type="hidden" name="source" value="HIPAA VA Buyer&rsquo;s Checklist">
      <div class="cf-row" style="grid-template-columns:1fr;margin-bottom:16px;">
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Send me the checklist <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>No spam &middot; Just the checklist and the occasional helpful tip</div>
    </form>
  </div>
</div>
<script>
(function () {
  // Dedicated transport for the checklist form only. The listener lives on the
  // <form> element, so a behavior-layer innerHTML reset keeps it wired.
  function postLead(form) {
    var url  = form.getAttribute('action') || 'lead.php';
    var btn  = form.querySelector('[type=submit]');
    var note = form.querySelector('[data-lead-note]');
    if (note) { note.textContent = ''; note.classList.remove('is-err'); }
    function resetBtn() {
      if (btn) {
        btn.disabled = false;
        btn.classList.remove('is-loading');
        if (btn.dataset.orig !== undefined) { btn.innerHTML = btn.dataset.orig; }
      }
    }
    if (btn) {
      btn.dataset.orig = btn.innerHTML;
      btn.disabled = true;
      btn.classList.add('is-loading');
      btn.innerHTML = '<span class="vtd-spinner" aria-hidden="true"></span> Sending…';
    }
    fetch(url, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res && res.ok) {
          var msg = form.getAttribute('data-lead-thanks') || 'Thank you! We’ll be in touch within 1 business day.';
          form.innerHTML = '<div class="lead-thanks"><i class="fa-solid fa-circle-check"></i><p>' + msg + '</p></div>';
        } else {
          if (note) { note.textContent = (res && res.error) ? res.error : 'Something went wrong — please try again.'; note.classList.add('is-err'); }
          resetBtn();
        }
      })
      .catch(function () {
        if (note) { note.textContent = 'Network error — please try again.'; note.classList.add('is-err'); }
        resetBtn();
      });
  }
  function bind() {
    var form = document.getElementById('ctaChecklistForm');
    if (!form || form.dataset.leadBound) { return; }
    form.dataset.leadBound = '1';
    form.addEventListener('submit', function (e) { e.preventDefault(); postLead(this); });
  }
  bind();
  document.addEventListener('DOMContentLoaded', bind);
})();
</script>
