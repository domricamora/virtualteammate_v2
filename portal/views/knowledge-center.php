<?php
/** @var array $topics  All topics, keyed by slug. @var array $topic  The current topic. @var string $slug */
$pageTitle = 'Knowledge Center';
$subtitle  = 'Internal training, SOPs and references for the team.';
$prefill   = trim((string) ($_GET['q'] ?? ''));

/**
 * Render one content block. Guarded so a second render() in the same request
 * (shouldn't happen) won't redeclare it.
 */
if (!function_exists('kc_render_block')) {
    function kc_render_block(array $b): void
    {
        switch ($b['type']) {
            case 'lessons':
                echo '<div class="kc-lessons">';
                foreach ($b['items'] as $it) {
                    $m = $it['media'] ?? ['kind' => 'soon', 'src' => ''];
                    echo '<article class="kc-lesson" data-find="' . e(strtolower($it['title'] . ' ' . ($it['desc'] ?? ''))) . '">';
                    if ($m['kind'] === 'video' && !empty($m['src'])) {
                        echo '<div class="kc-lesson-media"><video controls preload="metadata" playsinline src="' . e($m['src']) . '"></video></div>';
                    } elseif ($m['kind'] === 'pdf' && !empty($m['src'])) {
                        echo '<a class="kc-lesson-media kc-lesson-pdf" href="' . e($m['src']) . '" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i><span>Open PDF</span></a>';
                    } elseif ($m['kind'] === 'novideo') {
                        echo '<div class="kc-lesson-media kc-lesson-novideo"><i class="fa-solid fa-video-slash"></i><span>No video on file</span></div>';
                    } else {
                        echo '<div class="kc-lesson-media kc-lesson-soon"><i class="fa-solid fa-hourglass-half"></i></div>';
                    }
                    echo '<div class="kc-lesson-body">';
                    if (!empty($it['badge'])) { echo '<span class="kc-badge">' . e($it['badge']) . '</span>'; }
                    echo '<h3 class="kc-lesson-title">' . e($it['title']) . '</h3>';
                    if (!empty($it['desc'])) { echo '<p class="kc-lesson-desc">' . e($it['desc']) . '</p>'; }
                    if ($m['kind'] === 'pdf' && !empty($m['src'])) {
                        echo '<a class="kc-lesson-link" href="' . e($m['src']) . '" target="_blank" rel="noopener" download>Download PDF <i class="fa-solid fa-arrow-down"></i></a>';
                    }
                    echo '</div></article>';
                }
                echo '</div>';
                break;

            case 'summary':
                echo '<div class="kc-summary">';
                foreach ($b['items'] as $it) {
                    echo '<div class="kc-sum" data-find="' . e(strtolower($it['title'] . ' ' . ($it['sub'] ?? '') . ' ' . implode(' ', $it['bullets'] ?? []))) . '">';
                    echo '<h4 class="kc-sum-title">' . e($it['title']) . '</h4>';
                    if (!empty($it['sub'])) { echo '<div class="kc-sum-sub">' . e($it['sub']) . '</div>'; }
                    if (!empty($it['bullets'])) {
                        echo '<ul class="kc-bullets">';
                        foreach ($it['bullets'] as $li) { echo '<li>' . e($li) . '</li>'; }
                        echo '</ul>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                break;

            case 'accordion':
                echo '<div class="kc-acc">';
                foreach ($b['items'] as $it) {
                    $find = strtolower($it['title'] . ' ' . ($it['sub'] ?? '') . ' ' . implode(' ', $it['bullets'] ?? []) . ' ' . implode(' ', $it['tags'] ?? []));
                    echo '<div class="kc-acc-item" data-find="' . e($find) . '">';
                    echo '<button class="kc-acc-head" type="button">';
                    echo '<span class="kc-acc-titles"><span class="kc-acc-title">' . e($it['title']) . '</span>';
                    if (!empty($it['tags'])) {
                        echo '<span class="kc-acc-tags">';
                        foreach ($it['tags'] as $tg) { echo '<span class="kc-tag">' . e($tg) . '</span>'; }
                        echo '</span>';
                    }
                    echo '</span><i class="fa-solid fa-chevron-down kc-acc-chev"></i></button>';
                    echo '<div class="kc-acc-body">';
                    if (!empty($it['sub'])) { echo '<p class="kc-acc-sub">' . e($it['sub']) . '</p>'; }
                    if (!empty($it['bullets'])) {
                        echo '<ul class="kc-bullets">';
                        foreach ($it['bullets'] as $li) { echo '<li>' . e($li) . '</li>'; }
                        echo '</ul>';
                    }
                    if (!empty($it['note'])) { echo '<div class="kc-note">' . e($it['note']) . '</div>'; }
                    echo '</div></div>';
                }
                echo '</div>';
                break;

            case 'cards':
                echo '<div class="kc-cards">';
                foreach ($b['items'] as $it) {
                    $find = strtolower(($it['cat'] ?? '') . ' ' . $it['title'] . ' ' . ($it['summary'] ?? '') . ' ' . implode(' ', $it['bullets'] ?? []));
                    echo '<article class="kc-card" data-find="' . e($find) . '">';
                    if (!empty($it['cat'])) { echo '<span class="kc-card-cat">' . e($it['cat']) . '</span>'; }
                    echo '<h4 class="kc-card-title">' . e($it['title']) . '</h4>';
                    if (!empty($it['summary'])) { echo '<p class="kc-card-sum">' . e($it['summary']) . '</p>'; }
                    if (!empty($it['bullets'])) {
                        echo '<ul class="kc-bullets">';
                        foreach ($it['bullets'] as $li) { echo '<li>' . e($li) . '</li>'; }
                        echo '</ul>';
                    }
                    echo '</article>';
                }
                echo '</div>';
                break;

            case 'swatches':
                echo '<div class="kc-swatches">';
                foreach ($b['groups'] as $g) {
                    echo '<div class="kc-swgroup"><div class="kc-swlabel">' . e($g['label']) . '</div><div class="kc-swrow">';
                    foreach ($g['colors'] as $c) {
                        echo '<div class="kc-sw"><span class="kc-sw-chip" style="background:' . e($c) . '"></span><span class="kc-sw-hex">' . e($c) . '</span></div>';
                    }
                    echo '</div></div>';
                }
                echo '</div>';
                break;

            case 'qa':
                echo '<div class="kc-qa">';
                foreach ($b['items'] as $it) {
                    echo '<div class="kc-qa-item" data-find="' . e(strtolower($it['q'] . ' ' . $it['a'])) . '"><h4 class="kc-qa-q">' . e($it['q']) . '</h4><p class="kc-qa-a">' . e($it['a']) . '</p></div>';
                }
                echo '</div>';
                break;

            case 'captions':
                echo '<div class="kc-captions">';
                foreach ($b['items'] as $it) {
                    echo '<div class="kc-caption"><span class="kc-cap-label">' . e($it['label']) . '</span><p class="kc-cap-text">' . e($it['text']) . '</p></div>';
                }
                echo '</div>';
                break;

            case 'platforms':
                echo '<div class="kc-platforms">';
                foreach ($b['items'] as $it) {
                    echo '<div class="kc-plat"><span class="kc-plat-name">' . e($it['name']) . '</span><span class="kc-plat-note">' . e($it['note']) . '</span></div>';
                }
                echo '</div>';
                break;

            case 'prose':
                echo '<div class="kc-prose">';
                if (!empty($b['title'])) { echo '<h4>' . e($b['title']) . '</h4>'; }
                foreach (($b['paragraphs'] ?? []) as $p) { echo '<p>' . e($p) . '</p>'; }
                if (!empty($b['bullets'])) {
                    echo '<ul class="kc-bullets">';
                    foreach ($b['bullets'] as $li) { echo '<li>' . e($li) . '</li>'; }
                    echo '</ul>';
                }
                echo '</div>';
                break;

            case 'heading':
                echo '<h3 class="kc-section-h">' . e($b['text']) . '</h3>';
                break;

            case 'note':
                echo '<div class="kc-note kc-note-block">' . e($b['text']) . '</div>';
                break;

            case 'image':
                echo '<figure class="kc-figure"><img src="' . e($b['src']) . '" alt="' . e($b['alt'] ?? '') . '">';
                if (!empty($b['caption'])) { echo '<figcaption>' . e($b['caption']) . '</figcaption>'; }
                echo '</figure>';
                break;

            case 'ask':
                echo '<div class="kc-ask"><div class="kc-ask-body"><h4>' . e($b['title'] ?? 'Ask the Learning Team') . '</h4>';
                if (!empty($b['desc'])) { echo '<p>' . e($b['desc']) . '</p>'; }
                echo '</div><a class="btn-portal-primary" href="mailto:' . e($b['email']) . '"><i class="fa-solid fa-paper-plane"></i> Ask the Learning Team</a></div>';
                break;
        }
    }
}

$tabs        = $topic['tabs'];
$hasSubtabs  = count($tabs) > 1 && trim((string) ($tabs[0]['label'] ?? '')) !== '';
?>

<div class="kc">

  <section class="kc-hero">
    <?php if (!empty($topic['eyebrow'])): ?><div class="kc-hero-eyebrow"><?= e($topic['eyebrow']) ?></div><?php endif; ?>
    <h1 class="kc-hero-title">
      <?php foreach ($topic['h1'] as $i => $line): ?><span class="<?= $i === 0 ? 'kc-hl-1' : 'kc-hl-2' ?>"><?= e($line) ?></span> <?php endforeach; ?>
    </h1>
    <?php if (!empty($topic['intro'])): ?><p class="kc-hero-intro"><?= e($topic['intro']) ?></p><?php endif; ?>
    <?php if (!empty($topic['downloads'])): ?>
      <div class="kc-hero-dl">
        <?php foreach ($topic['downloads'] as $d): ?>
          <a class="kc-dl-btn" href="<?= e($d['url']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-download"></i> <?= e($d['label']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <nav class="kc-topics" aria-label="Knowledge Center topics">
    <a class="kc-back" href="<?= e(portal_url('resources')) ?>" title="Back to Resources"><i class="fa-solid fa-arrow-left"></i></a>
    <?php foreach ($topics as $t): ?>
      <a class="kc-topic<?= $t['slug'] === $slug ? ' is-on' : '' ?>" href="<?= e(portal_url('knowledge-center', ['topic' => $t['slug']])) ?>">
        <i class="fa-solid <?= e($t['icon']) ?>"></i> <span><?= e($t['label']) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <?php if (!empty($topic['search'])): ?>
    <div class="kc-find">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" id="kcFind" placeholder="Search this section…" value="<?= e($prefill) ?>" autocomplete="off">
      <span class="kc-find-empty" id="kcFindEmpty" hidden>No matches in this section.</span>
    </div>
  <?php endif; ?>

  <?php if ($hasSubtabs): ?>
    <div class="kc-subtabs" role="tablist">
      <?php foreach ($tabs as $i => $tab): ?>
        <button class="kc-subtab<?= $i === 0 ? ' is-on' : '' ?>" data-tab="<?= $i ?>" role="tab" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><?= e($tab['label']) ?></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php foreach ($tabs as $i => $tab): ?>
    <div class="kc-panel<?= $i === 0 ? ' is-on' : '' ?>" data-tab="<?= $i ?>">
      <?php foreach ($tab['blocks'] as $blk) { kc_render_block($blk); } ?>
    </div>
  <?php endforeach; ?>

</div>

<script>
(function () {
  var kc = document.querySelector('.kc');
  if (!kc) return;

  // Sub-tab switching
  var subtabs = Array.prototype.slice.call(kc.querySelectorAll('.kc-subtab'));
  var panels  = Array.prototype.slice.call(kc.querySelectorAll('.kc-panel'));
  function showTab(idx) {
    subtabs.forEach(function (b) {
      var on = b.getAttribute('data-tab') === String(idx);
      b.classList.toggle('is-on', on);
      b.setAttribute('aria-selected', String(on));
    });
    panels.forEach(function (p) { p.classList.toggle('is-on', p.getAttribute('data-tab') === String(idx)); });
    runFind();
  }
  subtabs.forEach(function (b) { b.addEventListener('click', function () { showTab(b.getAttribute('data-tab')); }); });

  // Accordion toggle (single click open/close)
  kc.querySelectorAll('.kc-acc-head').forEach(function (h) {
    h.addEventListener('click', function () { h.parentNode.classList.toggle('is-open'); });
  });

  // In-section search: filter findable items within the active panel
  var find = document.getElementById('kcFind');
  var findEmpty = document.getElementById('kcFindEmpty');
  function runFind() {
    if (!find) return;
    var q = find.value.trim().toLowerCase();
    var active = kc.querySelector('.kc-panel.is-on') || kc;
    var items = active.querySelectorAll('[data-find]');
    var shown = 0;
    items.forEach(function (el) {
      var hit = !q || (el.getAttribute('data-find') || '').indexOf(q) !== -1;
      el.style.display = hit ? '' : 'none';
      if (hit) { shown++; if (q && el.classList.contains('kc-acc-item')) el.classList.add('is-open'); }
    });
    if (findEmpty) findEmpty.hidden = !(q && shown === 0);
  }
  if (find) { find.addEventListener('input', runFind); if (find.value) runFind(); }
})();
</script>
