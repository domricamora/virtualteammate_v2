<?php
/**
 * Reusable "Ways to Start" entry-point CTA block (#cta) — the same three-stage
 * funnel block used on the homepage. The modals it opens are provided site-wide
 * by includes/cta-modals.php (or inline on the homepage).
 *
 * The audit card/button targets $audit_modal (set in includes/nav.php):
 * #cta-practice-audit on the homepage, #cta-book on inner pages. Pages that
 * include this should also set $has_cta_section = true before nav.php so the
 * topbar "Get started" scrolls here instead of opening the Jumpstart modal.
 */
$audit_modal = $audit_modal ?? '#cta-book';
?>
<section class="sec cta-stages-section" id="cta" style="padding-top:80px;padding-bottom:110px;">
  <div class="cta-stages-h reveal">
    <div class="sec-lbl"><i class="fa-solid fa-paper-plane"></i> Three Ways to Start</div>
    <h2 class="cta-h2" style="font-size:36px;">Pick the entry point<br>that fits where you are</h2>
    <p class="cta-sub">Just exploring, ready to diagnose, or ready to scope: three ways in, same team on the other side.</p>
    <p class="avail-note avail-note-center"><i class="fa-solid fa-hourglass-half"></i> New-practice onboarding is capped monthly so every match gets a proper search. Booking your audit now reserves your place in the next intake.</p>
  </div>

  <div class="cta-stages-grid reveal d1">
    <article class="cta-stage" data-cta-intent="buyers-checklist">
      <div class="cta-stage-tag">Just exploring</div>
      <span class="ico-circle lg"><i class="fa-solid fa-file-lines"></i></span>
      <h3>HIPAA VA buyer&rsquo;s checklist</h3>
      <p class="cta-stage-lead">Key questions to ask any healthcare VA agency before you sign. Drop your email: we&rsquo;ll send the PDF.</p>
      <ul class="cta-stage-list">
        <li>Compliance, BAA, audit-trail questions</li>
        <li>Pricing-model traps to watch for</li>
        <li>Performance &amp; quality SLAs to demand</li>
      </ul>
      <a class="btn-cta-stage" href="#cta-buyers-checklist" data-cta-intent="buyers-checklist">Get the HIPAA VA buyer&rsquo;s checklist <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-mid" data-cta-intent="practice-audit">
      <div class="cta-stage-tag">Ready to diagnose</div>
      <span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span>
      <h3>20-min practice staffing audit</h3>
      <p class="cta-stage-lead">Diagnostic-only call. We map your top admin and clinical workflows and tell you what to delegate first.</p>
      <ul class="cta-stage-list">
        <li>Workflow inventory (8&ndash;12 mapped)</li>
        <li>Ranked outsourcing-priority list</li>
        <li>Tier + headcount recommendation</li>
      </ul>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      <a class="btn-cta-stage" href="<?= $audit_modal ?>" data-cta-intent="practice-audit">Book my practice staffing audit <i class="fa-solid fa-arrow-right"></i></a>
    </article>

    <article class="cta-stage cta-stage-high" data-cta-intent="strategy-call">
      <div class="cta-stage-tag">Ready to start</div>
      <span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span>
      <h3>Jumpstart call</h3>
      <p class="cta-stage-lead">30-min call. Map your needs, define the role, get a curated shortlist in 1&ndash;2 business days.</p>
      <ul class="cta-stage-list">
        <li>Role scope + EHR / specialty match</li>
        <li>Tailored candidate shortlist</li>
        <li>Onboarding plan + CSM intro</li>
      </ul>
      <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
      <a class="btn-cta-stage" href="#cta-strategy-call" data-cta-intent="strategy-call">Get started in 24 hours <i class="fa-solid fa-arrow-right"></i></a>
    </article>
  </div>

  <p class="cta-stages-foot reveal">Prefer to start with a diagnostic? <a href="<?= $audit_modal ?>" data-cta-intent="practice-audit">Book my practice staffing audit</a> and a Client Success Manager (CSM) will map it out with you.</p>
</section>
