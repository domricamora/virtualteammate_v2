<?php
/** @var array $resources @var array $playbook */
$pageTitle = 'Resources';
$subtitle  = 'Client playbook, worksheets and downloadable references.';
?>
<div class="card" style="background:linear-gradient(135deg,rgba(57,25,186,0.18),rgba(247,185,69,0.08) 60%,rgba(255,255,255,0.02));border:1px solid rgba(247,185,69,.3);">
  <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
    <div style="flex:0 0 60px;width:60px;height:60px;border-radius:14px;background:linear-gradient(135deg,#3919BA,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;"><i class="fa-solid fa-book-open"></i></div>
    <div style="flex:1;min-width:240px;">
      <div class="cd-hero-eyebrow"><span style="display:inline-block;width:8px;height:8px;background:var(--gold,#d4a64a);border-radius:50%;vertical-align:middle;margin-right:6px;"></span>Recommended first step</div>
      <h2 style="margin:0 0 4px;color:#fff;font-size:22px;font-weight:800;"><?= e($playbook['title']) ?></h2>
      <p class="muted" style="margin:0;font-size:13.5px;line-height:1.55;"><?= e($playbook['desc']) ?></p>
    </div>
    <a class="btn-portal-primary" href="<?= e($playbook['url']) ?>" target="_blank" rel="noopener">Open playbook <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</div>

<div class="card" style="margin-top:18px;">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-download"></i> Downloads</h3>
    <span class="muted small">Fast implementation tools: define measurable outcomes, build delegation systems, remove time drains.</span>
  </div>
  <div class="cd-dl-grid">
    <?php foreach ($resources as $r): ?>
      <a class="cd-dl-tile" href="<?= e($r['url']) ?>" target="_blank" rel="noopener" download style="--accent:<?= e($r['accent']) ?>">
        <span class="cd-dl-ico"><i class="fa-solid fa-file-pdf"></i></span>
        <div class="cd-dl-meta">
          <div class="cd-dl-title"><?= e($r['title']) ?></div>
          <div class="cd-dl-desc muted small"><?= e($r['description']) ?></div>
        </div>
        <span class="cd-dl-arrow"><i class="fa-solid fa-arrow-down"></i></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
