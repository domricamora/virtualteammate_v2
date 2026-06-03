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
?>
<!-- FINAL CTA -->
<section class="svc-cta">
  <h2>Ready to Meet Your Future <em style="color:var(--gold);font-style:normal;">Virtual Teammate</em>?</h2>
  <p>Get a curated shortlist of HIPAA-certified candidates within days &mdash; matched to your time zone, software stack, and workflow. No commitment, no recruiter fees.</p>
  <div class="svc-cta-btns">
    <a href="<?= $home_base ?>#cta-strategy-call" class="btn-primary" data-cta-intent="strategy-call">Book My Strategy Call <i class="fa-solid fa-arrow-right"></i></a>
    <a href="<?= $home_base ?>#calculator" class="btn-glass">Calculate My Savings <i class="fa-solid fa-calculator"></i></a>
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
