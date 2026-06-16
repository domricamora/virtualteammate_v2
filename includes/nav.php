<?php
/**
 * Topbar + main navigation with cascading Healthcare dropdown.
 *
 * Anchor links use $home_base (set per-page; defaults to './') so they resolve
 * correctly from any page depth — homepage uses './', /services/<slug>/ uses '../../'.
 * Service pages live at $home_base . 'services/<slug>/' and are linked the same way.
 */
$home_base = $home_base ?? './';
?>
<!-- TOPBAR -->
<div class="topbar" role="complementary">
  <i class="fa-solid fa-hospital" aria-hidden="true"></i> HIPAA-certified medical &amp; dental virtual assistants: vetted, trained on your systems, live in 1&ndash;2 weeks.
  &nbsp;&bull;&nbsp;
  <a href="<?= $home_base ?>#cta-practice-audit" data-cta-intent="practice-audit">Book My Staffing Audit <i class="fa-solid fa-arrow-right" style="font-size:11px;" aria-hidden="true"></i></a>
</div>

<!-- NAV -->
<nav class="nav" aria-label="Primary">
  <a href="<?= $home_base ?>" class="logo" aria-label="Virtual Teammate home">
    <img src="https://staging.virtualteammate.com/wp-content/uploads/2025/10/BLACK-AND-VIOLET-LOGO-1.png" alt="Virtual Teammate" width="180" height="53"/>
  </a>
  <button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-controls="primaryNav" aria-expanded="false" type="button">
    <span class="nav-toggle-bar"></span>
    <span class="nav-toggle-bar"></span>
    <span class="nav-toggle-bar"></span>
  </button>
  <div class="nav-links" id="primaryNav">
    <div class="nav-drop">
      <a href="<?= $home_base ?>#specialties" class="nav-drop-trigger" aria-haspopup="true" aria-expanded="false">
        Healthcare
        <span class="nav-badge">Medical &middot; Dental</span>
        <i class="fa-solid fa-chevron-down nav-caret" aria-hidden="true"></i>
      </a>
      <div class="nav-mega" role="menu" aria-label="Healthcare services">
        <div class="nav-mega-col">
          <div class="nav-mega-h"><span class="ico-circle sm"><i class="fa-solid fa-user-doctor"></i></span> Medical</div>
          <a class="nav-mega-link" href="<?= $home_base ?>services/medical-administrative-support/" role="menuitem">
            <i class="fa-solid fa-clipboard-list"></i>
            <span><strong>Medical Administrative Support</strong><em>Charts, intake, records &amp; admin workflows</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/medical-receptionist/" role="menuitem">
            <i class="fa-solid fa-headset"></i>
            <span><strong>Medical Receptionist</strong><em>Front-desk calls, scheduling &amp; intake</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/medical-biller/" role="menuitem">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span><strong>Medical Biller</strong><em>Claims, AR follow-up &amp; RCM</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/medical-scribe/" role="menuitem">
            <i class="fa-solid fa-pen-clip"></i>
            <span><strong>Medical Scribe</strong><em>Real-time EHR documentation</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/medical-assistant/" role="menuitem">
            <i class="fa-solid fa-user-nurse"></i>
            <span><strong>Medical Assistant</strong><em>Clinical &amp; administrative support</em></span>
          </a>
        </div>
        <div class="nav-mega-col">
          <div class="nav-mega-h"><span class="ico-circle sm"><i class="fa-solid fa-tooth"></i></span> Dental</div>
          <a class="nav-mega-link" href="<?= $home_base ?>services/dental-admin/" role="menuitem">
            <i class="fa-solid fa-clipboard-list"></i>
            <span><strong>Dental Administrative Support</strong><em>Records, verification &amp; treatment-plan prep</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/dental-receptionist/" role="menuitem">
            <i class="fa-solid fa-headset"></i>
            <span><strong>Dental Receptionist</strong><em>Live front-desk calls, scheduling &amp; recall</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/dental-biller/" role="menuitem">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span><strong>Dental Biller</strong><em>Insurance billing &amp; EOB posting</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/dental-scribe/" role="menuitem">
            <i class="fa-solid fa-pen-clip"></i>
            <span><strong>Dental Scribe</strong><em>Real-time clinical &amp; perio charting</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>services/dental-coordinator/" role="menuitem">
            <i class="fa-solid fa-handshake-angle"></i>
            <span><strong>Dental Coordinator</strong><em>Case acceptance, recall &amp; scheduling</em></span>
          </a>
        </div>
        <div class="nav-mega-foot">
          <div class="nav-mega-foot-txt">
            <i class="fa-solid fa-circle-nodes"></i>
            <span><strong>Not sure which role you need?</strong> We staff medical <em>and</em> dental &mdash; front desk to back office, all HIPAA-certified.</span>
          </div>
          <a href="<?= $home_base ?>#cta-practice-audit" class="nav-mega-foot-btn" data-cta-intent="practice-audit">Book My Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
          <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise &mdash; free replacement or your money back.</div>
        </div>
      </div>
    </div>
    <a href="<?= $home_base ?>virtual-teammates/">Virtual Teammates</a>
    <a href="<?= $home_base ?>case-studies/">Case Studies</a>
    <div class="nav-drop">
      <a href="<?= $home_base ?>about/" class="nav-drop-trigger" aria-haspopup="true" aria-expanded="false">
        About
        <i class="fa-solid fa-chevron-down nav-caret" aria-hidden="true"></i>
      </a>
      <div class="nav-mega nav-mega-sm" role="menu" aria-label="Company">
        <div class="nav-mega-col">
          <div class="nav-mega-h"><span class="ico-circle sm"><i class="fa-solid fa-building"></i></span> Company</div>
          <a class="nav-mega-link" href="<?= $home_base ?>about/" role="menuitem">
            <i class="fa-solid fa-circle-info"></i>
            <span><strong>About Virtual Teammate</strong><em>Founder, leadership &amp; mission</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>guarantee/" role="menuitem">
            <i class="fa-solid fa-shield-halved"></i>
            <span><strong>30-Day Right-Fit Promise</strong><em>Our no-risk hiring guarantee</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>careers/" role="menuitem">
            <i class="fa-solid fa-rocket"></i>
            <span><strong>Careers</strong><em>Remote VA jobs &middot; apply now</em></span>
          </a>
          <a class="nav-mega-link" href="<?= $home_base ?>contact/" role="menuitem">
            <i class="fa-solid fa-headset"></i>
            <span><strong>Contact Us</strong><em>Phoenix HQ &middot; (480) 847-2498</em></span>
          </a>
        </div>
      </div>
    </div>
    <a href="<?= $home_base ?>#calculator">ROI Calculator</a>
  </div>
  <div class="nav-right">
    <span class="hipaa-badge hipaa-badge--nav" title="HIPAA-certified virtual assistants"><img class="hipaa-badge-logo" src="<?= $home_base ?>images/hipaa-certified.webp" alt="" aria-hidden="true" width="16" height="16"> HIPAA Certified</span>
    <a href="tel:+14808472498" class="nav-phone" aria-label="Call (480) 847-2498">
      <i class="fa-solid fa-phone" aria-hidden="true"></i>(480) 847-2498
    </a>
    <a href="<?= $home_base ?>#cta-practice-audit" data-cta-intent="practice-audit" class="btn-nav">Book My Staffing Audit</a>
  </div>
</nav>
