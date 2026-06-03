<?php
/**
 * Reusable lead-capture band (sitewide funnel).
 *
 * Included automatically before the footer on every marketing page (see
 * includes/footer.php) unless the page sets $hide_lead_band = true. Posts to
 * /lead.php via the global data-lead-form handler in js/main.js → emails the
 * team (branded) + stores the lead in the portal DB.
 *
 * Pages may customise before the footer include (all optional):
 *   $lf_source, $lf_form, $lf_title, $lf_sub, $lf_thanks, $lf_cta,
 *   $lf_company_ph, $lf_msg_ph, $lf_roles (array → adds a role/department <select>)
 */
$hb = $home_base ?? './';
// Auto-attribute the lead to the current page when no explicit source is set.
if (!isset($lf_source)) {
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    $lf_source = $path === '' ? 'homepage' : $path;
}
$lf_form       = $lf_form       ?? $lf_source;
$lf_title      = $lf_title      ?? 'Get Matched With Your Virtual Teammate';
$lf_sub        = $lf_sub        ?? 'Tell us what you need and we\'ll hand-pick vetted, HIPAA-certified candidates by specialty, software and time zone — value-first, no obligation.';
$lf_thanks     = $lf_thanks     ?? 'Thanks! We\'ll reach out within 1 business day with your matched shortlist.';
$lf_cta        = $lf_cta        ?? 'Get my value-matched shortlist';
$lf_company_ph = $lf_company_ph ?? 'Practice / company';
$lf_msg_ph     = $lf_msg_ph     ?? 'What do you need help with?';
$lf_roles      = $lf_roles      ?? [];
$lfe = static fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
?>
<section class="sec lead-band" id="lead-form" aria-labelledby="lead-band-h">
  <div class="lead-band-card reveal">
    <div class="lead-band-l">
      <div class="sec-lbl"><i class="fa-solid fa-wand-magic-sparkles"></i> Value Matching</div>
      <h2 class="sec-h2" id="lead-band-h"><?= $lfe($lf_title) ?></h2>
      <p class="lead-band-sub"><?= $lfe($lf_sub) ?></p>
      <ul class="lead-band-points">
        <li><i class="fa-solid fa-check"></i> Curated shortlist within days</li>
        <li><i class="fa-solid fa-check"></i> Interview before you decide</li>
        <li><i class="fa-solid fa-check"></i> 30-Day Right-Fit Promise</li>
      </ul>
    </div>
    <form class="lead-band-form" method="post" action="<?= $hb ?>lead.php" data-lead-form
          data-lead-thanks="<?= $lfe($lf_thanks) ?>">
      <div class="cf-row">
        <input class="cf-field" name="first_name" placeholder="First name" required autocomplete="given-name">
        <input class="cf-field" name="last_name" placeholder="Last name" autocomplete="family-name">
      </div>
      <input class="cf-field" type="email" name="email" placeholder="Work email" required autocomplete="email" style="margin-bottom:14px;">
      <div class="cf-row">
        <input class="cf-field" type="tel" name="phone" placeholder="Phone (optional)" autocomplete="tel">
        <input class="cf-field" name="company" placeholder="<?= $lfe($lf_company_ph) ?>" autocomplete="organization">
      </div>
      <?php if (!empty($lf_roles)): ?>
        <select class="cf-field" name="role" required style="margin-bottom:14px;">
          <option value="">Select a role / department…</option>
          <?php foreach ($lf_roles as $r): ?><option><?= $lfe($r) ?></option><?php endforeach; ?>
        </select>
      <?php endif; ?>
      <textarea class="cf-field" name="message" rows="3" placeholder="<?= $lfe($lf_msg_ph) ?>" style="margin-bottom:14px;"></textarea>
      <input type="hidden" name="source" value="<?= $lfe($lf_source) ?>">
      <input type="hidden" name="form" value="<?= $lfe($lf_form) ?>">
      <input type="text" name="vt_hp" tabindex="-1" autocomplete="off" class="vtd-hp" aria-hidden="true">
      <button class="cf-submit" type="submit"><?= $lfe($lf_cta) ?> <i class="fa-solid fa-arrow-right"></i></button>
      <div class="cf-note" data-lead-note>No spam. We respond within 1 business day.</div>
    </form>
  </div>
</section>
<?php
// Reset so a later include on the same request can't inherit stale values.
unset($lf_source, $lf_form, $lf_title, $lf_sub, $lf_thanks, $lf_cta, $lf_company_ph, $lf_msg_ph, $lf_roles);
