<?php
/**
 * Reusable "Request this teammate" modal (#cta-request) — the same flow the
 * homepage uses from its "Meet the Team" cards.
 *
 * A teammate card links to #cta-request with data-vt-id / data-vt-name; this
 * modal opens (CSS :target), prefills with that teammate (id + name in the
 * heading and hidden fields) and posts to lead.php, creating a "Teammate
 * Request" lead — identical to index.php's #cta-request.
 *
 * Self-contained & idempotent. Emits once per request (guard). Ships:
 *   - the modal markup (form posts to $home_base.'lead.php'),
 *   - a click handler that stamps the clicked card's vt id/name into the form,
 *   - a dedicated submit handler (fetch → thank-you swap),
 *   - generic .cta-modal scroll-lock / ESC / autofocus behavior, UNLESS the
 *     page already wires its modals — set $rm_skip_behavior = true to skip it
 *     (e.g. the business page, which has its own modal-behavior script).
 *
 * Set before include:
 *   $home_base        string  path back to webroot (e.g. '../../'). Required.
 *   $rm_skip_behavior bool    skip the bundled scroll-lock behavior (default false)
 */
if (defined('VT_REQUEST_MODAL_RENDERED')) { return; }
define('VT_REQUEST_MODAL_RENDERED', true);
$home_base        = $home_base ?? './';
$rm_skip_behavior = $rm_skip_behavior ?? false;
?>
<!-- Candidate-request modal. Opened from a teammate card; the script below copies
     the clicked profile's name + id into vt_interest / vt_id (and the visible
     heading) before it shows, so the lead records exactly who the visitor asked
     for. Posts to lead.php and creates a "Teammate Request" lead. -->
<div class="cta-modal" id="cta-request" role="dialog" aria-modal="true" aria-labelledby="rqm-h">
  <a class="cta-modal-scrim" href="#cta" aria-label="Close" tabindex="-1"></a>
  <div class="cta-modal-card">
    <a class="cta-modal-x" href="#cta" aria-label="Close form">&times;</a>
    <div class="cta-modal-tag"><i class="fa-solid fa-user-plus"></i> Request a teammate</div>
    <h2 class="cta-modal-h" id="rqm-h">Request <span data-vt-name-target>this teammate</span></h2>
    <p class="cta-modal-sub">Tell us where to reach you and your dedicated Client Success Manager will check <span data-vt-name-target>this teammate</span>&rsquo;s availability, then line up similar vetted matches for your specialty, software and time zone.</p>
    <form class="cta-modal-form" id="ctaRequestForm" method="post" action="<?= $home_base ?>lead.php"
          data-lead-thanks="Request received: your Client Success Manager will be in touch within one business day.">
      <input type="hidden" name="intent" value="request">
      <input type="hidden" name="form" value="teammate-request">
      <input type="hidden" name="source" value="Teammate Request">
      <input type="hidden" name="vt_id" value="">
      <input type="hidden" name="vt_interest" value="">
      <div class="cf-row" style="margin-bottom:16px;">
        <input class="cf-field" name="first_name" type="text" placeholder="First Name" required>
        <input class="cf-field" name="email" type="email" placeholder="Work Email" required>
      </div>
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit">Request this teammate <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>Diagnostic-first &middot; No obligation, covered by the 30-Day Right-Fit Promise</div>
    </form>
  </div>
</div>
<script>
(function () {
  var rm = document.getElementById('cta-request');
  if (!rm) { return; }

  // ── Card → modal prefill. Runs before navigation so the heading + hidden
  //    fields are set by the time the :target modal paints. ──
  function fillRequest(a) {
    var vname = a.getAttribute('data-vt-name') || '';
    var idEl  = rm.querySelector('input[name="vt_id"]');
    var nmEl  = rm.querySelector('input[name="vt_interest"]');
    if (idEl) { idEl.value = a.getAttribute('data-vt-id') || ''; }
    if (nmEl) { nmEl.value = vname; }
    rm.querySelectorAll('[data-vt-name-target]').forEach(function (t) {
      t.textContent = vname || 'this teammate';
    });
  }
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href$="#cta-request"]');
    if (a) { fillRequest(a); }
  });

  // ── Dedicated submit handler: fetch → thank-you swap (mirrors the homepage). ──
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
  function bindForm() {
    var form = document.getElementById('ctaRequestForm');
    if (!form || form.dataset.leadBound) { return; }
    form.dataset.leadBound = '1';
    form.addEventListener('submit', function (e) { e.preventDefault(); postLead(this); });
  }
  bindForm();
  document.addEventListener('DOMContentLoaded', bindForm);

<?php if (!$rm_skip_behavior): ?>
  // ── Generic .cta-modal behavior: scroll-lock, ESC-to-close, autofocus, and a
  //    jump-free open. Deferred to DOM-ready so it catches every .cta-modal on
  //    the page (e.g. the footer's #cta-book), wherever this include sits. Only
  //    pages WITHOUT their own modal handler reach this (else $rm_skip_behavior). ──
  function initBehavior() {
    if (window.__vtCtaModalBehavior) { return; }
    window.__vtCtaModalBehavior = true;
    var modals = {};
    document.querySelectorAll('.cta-modal').forEach(function (m) { modals['#' + m.id] = m; });
    if (!Object.keys(modals).length) { return; }
    var docEl = document.documentElement, body = document.body;
    var savedY = 0, locked = false;
    var pristine = [];
    document.querySelectorAll('.cta-modal .cta-modal-form').forEach(function (f) {
      pristine.push({ form: f, html: f.innerHTML });
    });
    function resetForms() {
      pristine.forEach(function (p) {
        if (p.form.innerHTML !== p.html) { p.form.innerHTML = p.html; bindForm(); }
        try { p.form.reset(); } catch (e) {}
      });
    }
    function lock() {
      if (locked) { return; }
      savedY = window.scrollY || window.pageYOffset || 0;
      body.style.top = (-savedY) + 'px';
      docEl.classList.add('cta-locked');
      locked = true;
    }
    function unlock() {
      if (!locked) { return; }
      docEl.classList.remove('cta-locked');
      body.style.top = '';
      window.scrollTo(0, savedY);
      locked = false;
    }
    function sync() {
      var m = modals[location.hash];
      if (m) {
        lock();
        var f = m.querySelector('input:not([type=hidden]):not(.vtd-hp), select, textarea');
        if (f) { try { f.focus({ preventScroll: true }); } catch (e) { f.focus(); } }
      } else {
        unlock();
        resetForms();
      }
    }
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href^="#cta-"]');
      if (a && modals[a.getAttribute('href')]) { lock(); }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modals[location.hash]) { location.hash = '#cta'; }
    });
    window.addEventListener('hashchange', sync);
    sync();
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBehavior);
  } else {
    initBehavior();
  }
<?php endif; ?>
})();
</script>
<?php
unset($rm_skip_behavior);
