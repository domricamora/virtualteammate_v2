<?php
/**
 * Final CTA + "Related VA Roles" grid for service pages.
 * Set $svc_slug before include to exclude the current page from the related grid.
 * $home_base must be set (e.g. '../../' from /services/<slug>/index.php).
 */
$home_base = $home_base ?? '../../';
$svc_slug  = $svc_slug  ?? '';

$svc_all = [
  'medical-administrative-support' => ['title' => 'Medical Administrative Support', 'sub' => 'Charts, intake & admin workflows', 'icon' => 'fa-clipboard-list'],
  'medical-receptionist'           => ['title' => 'Medical Receptionist',           'sub' => 'Front-desk calls & scheduling',     'icon' => 'fa-headset'],
  'medical-biller'                 => ['title' => 'Medical Biller',                 'sub' => 'Claims, AR & RCM',                   'icon' => 'fa-file-invoice-dollar'],
  'medical-scribe'                 => ['title' => 'Medical Scribe',                 'sub' => 'Real-time EHR documentation',        'icon' => 'fa-pen-clip'],
  'medical-assistant'              => ['title' => 'Medical Assistant',              'sub' => 'Clinical & admin support',           'icon' => 'fa-user-nurse'],
  'dental-admin'                   => ['title' => 'Dental Administrative Support', 'sub' => 'Records, verification & plan prep',  'icon' => 'fa-clipboard-list'],
  'dental-receptionist'            => ['title' => 'Dental Receptionist',            'sub' => 'Live front-desk & recall',           'icon' => 'fa-headset'],
  'dental-biller'                  => ['title' => 'Dental Biller',                  'sub' => 'Insurance billing & EOBs',           'icon' => 'fa-file-invoice-dollar'],
  'dental-scribe'                  => ['title' => 'Dental Scribe',                  'sub' => 'Real-time clinical charting',        'icon' => 'fa-pen-clip'],
  'dental-coordinator'             => ['title' => 'Dental Coordinator',             'sub' => 'Case acceptance & recall',           'icon' => 'fa-handshake-angle'],
];

/* Live teammate preview for this service — drawn from the matching department
   and ranked so the page's specific role bubbles to the top (see vt-cards.php).
   Card CTA funnels to the homepage Practice Staffing Audit modal. */
$svc_vtc = [
  'medical-administrative-support' => ['dept' => 'Medical', 'role' => 'Medical Administrative', 'kw' => ['administrative', 'admin', 'chart', 'intake', 'scheduling', 'eligibility']],
  'medical-receptionist'           => ['dept' => 'Medical', 'role' => 'Medical Receptionist',   'kw' => ['reception', 'front desk', 'front-desk', 'phone', 'scheduling']],
  'medical-biller'                 => ['dept' => 'Medical', 'role' => 'Medical Billing',        'kw' => ['biller', 'billing', 'rcm', 'claims', 'accounts receivable', 'ar']],
  'medical-scribe'                 => ['dept' => 'Medical', 'role' => 'Medical Scribe',          'kw' => ['scribe', 'scribing', 'documentation', 'charting']],
  'medical-assistant'              => ['dept' => 'Medical', 'role' => 'Medical Assistant',       'kw' => ['medical assistant', 'assistant', 'clinical']],
  'dental-admin'                   => ['dept' => 'Dental',  'role' => 'Dental Administrative',   'kw' => ['administrative', 'admin', 'verification', 'records']],
  'dental-receptionist'            => ['dept' => 'Dental',  'role' => 'Dental Receptionist',     'kw' => ['reception', 'front desk', 'front-desk', 'recall', 'phone']],
  'dental-biller'                  => ['dept' => 'Dental',  'role' => 'Dental Billing',          'kw' => ['biller', 'billing', 'insurance', 'eob', 'claims', 'receivable']],
  'dental-scribe'                  => ['dept' => 'Dental',  'role' => 'Dental Scribe',           'kw' => ['scribe', 'scribing', 'charting', 'documentation']],
  'dental-coordinator'             => ['dept' => 'Dental',  'role' => 'Dental Treatment Coordinator', 'kw' => ['coordinator', 'treatment', 'case', 'recall']],
];
if (isset($svc_vtc[$svc_slug])) {
    $sc = $svc_vtc[$svc_slug];
    $sce = static fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    $vtc_depts      = [$sc['dept']];
    $vtc_keywords   = $sc['kw'];
    $vtc_label      = 'Meet the Bench';
    $vtc_heading    = 'Meet Our <em>' . $sce($sc['role']) . '</em> Virtual Teammates';
    $vtc_sub        = 'A sample of real, vetted ' . $sce($sc['dept']) . ' teammates, interview-ready, matched to your time zone, and live in 1&ndash;2 weeks. ' . $sce($sc['role']) . ' specialists are shown first.';
    $vtc_cta_href   = $home_base . '#cta-practice-audit';
    $vtc_cta_intent = 'practice-audit';
    $vtc_cta_label  = 'Book My Staffing Audit';
    include __DIR__ . '/vt-cards.php';
}
?>
<!-- FINAL CTA -->
<section class="svc-cta">
  <h2>Ready to Meet Your Future <em style="color:var(--gold);font-style:normal;">Virtual Teammate</em>?</h2>
  <p>Get a curated shortlist of HIPAA-certified candidates within days, matched to your time zone, software stack, and workflow. No commitment, no recruiter fees.</p>
  <div class="svc-cta-btns">
    <a href="#cta-book" class="btn-primary" data-cta-intent="practice-audit">Book My Staffing Audit <i class="fa-solid fa-arrow-right"></i></a>
    <a href="<?= $home_base ?>#cta-buyers-checklist" class="btn-glass" data-cta-intent="buyers-checklist">Get the HIPAA VA Buyer&rsquo;s Checklist <i class="fa-solid fa-arrow-right"></i></a>
    <div class="cta-note"><i class="fa-solid fa-shield-halved"></i> Covered by our 30-Day Right-Fit Promise: free replacement or your money back.</div>
  </div>
</section>

<!-- RELATED SERVICES -->
<section class="svc-related" aria-labelledby="svc-related-h">
  <div class="svc-related-h" id="svc-related-h">Explore Related Virtual Assistant Roles</div>
  <div class="svc-related-grid">
    <?php foreach ($svc_all as $slug => $svc): if ($slug === $svc_slug) continue; ?>
      <a class="svc-related-card" href="<?= $home_base ?>services/<?= $slug ?>/">
        <span class="ico-circle lg"><i class="fa-solid <?= $svc['icon'] ?>"></i></span>
        <div>
          <div class="svc-related-card-t"><?= $svc['title'] ?></div>
          <div class="svc-related-card-s"><?= $svc['sub'] ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
