<?php
/** @var array $apps */
$pageTitle = 'VTM Apps';
$subtitle  = 'Download and set up the tools you use day to day.';
?>
<div class="card">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-grip"></i> Apps &amp; Downloads</h3>
    <span class="muted small">Install these on your work computer to stay set up and in sync.</span>
  </div>
  <div class="apps-grid">
    <?php foreach ($apps as $a): ?>
      <div class="apps-card" style="--accent:<?= e($a['accent']) ?>">
        <span class="apps-ico"><i class="fa-brands <?= e($a['icon']) ?>"></i><i class="fa-solid <?= e($a['icon']) ?>"></i></span>
        <h4 class="apps-name"><?= e($a['name']) ?></h4>
        <p class="apps-desc"><?= e($a['desc']) ?></p>
        <a class="btn-portal-primary apps-btn" href="<?= e($a['url']) ?>" target="_blank" rel="noopener">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> <?= e($a['cta']) ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<style>
.apps-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;}
.apps-card{display:flex;flex-direction:column;align-items:flex-start;gap:10px;padding:20px;border:1px solid var(--line);border-top:3px solid var(--accent,var(--gold));border-radius:14px;background:rgba(255,255,255,.04);}
.apps-ico{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;background:linear-gradient(135deg,var(--accent,#3919BA),rgba(255,255,255,.12));box-shadow:0 8px 22px -10px var(--accent,#3919BA);}
/* Show whichever icon glyph resolves (fa-brands for slack, fa-solid otherwise) */
.apps-ico .fa-solid{display:none;}
.apps-ico .fa-brands.fa-slack + .fa-solid{display:none;}
.apps-ico .fa-brands:not(.fa-slack){display:none;}
.apps-ico .fa-brands:not(.fa-slack) + .fa-solid{display:inline-block;}
.apps-name{margin:0;font-size:17px;font-weight:800;color:#fff;}
.apps-desc{margin:0;font-size:13px;color:var(--text-mute);line-height:1.5;flex:1;}
.apps-btn{margin-top:6px;width:100%;justify-content:center;}
@media (max-width:680px){ .apps-grid{grid-template-columns:1fr;} }
</style>
