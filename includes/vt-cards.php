<?php
/**
 * Reusable "Virtual Teammates" cards section (live, from the portal DB).
 *
 * Renders a preview grid of real, vetted teammates drawn from one or more
 * departments, optionally ranked so the ones whose role/skills match the
 * current page bubble to the top. Anonymous-safe: public name (first + last
 * initial), role, country and a few skills only — no PII, no media. The full
 * roster with profiles/videos/résumés lives at /virtual-teammates/.
 *
 * Graceful no-op if the DB is missing or no teammates match.
 *
 * Set before include (all optional except $home_base + $vtc_depts):
 *   $home_base      string  e.g. '../' or '../../' (path back to webroot)
 *   $vtc_depts      array   departments to draw from (e.g. ['Medical'])
 *   $vtc_keywords   array   role/skill keywords to prioritise (the page role)
 *   $vtc_limit      int     max cards (default 6)
 *   $vtc_label      string  eyebrow label (default 'Meet the Bench')
 *   $vtc_heading    string  H2 (HTML allowed, e.g. with <em>)
 *   $vtc_sub        string  sub copy under the H2
 *   $vtc_cta_href   string  card button href (default $home_base.'#cta-request-teammate')
 *   $vtc_cta_intent string  data-cta-intent on the card button (default 'request-teammate')
 *   $vtc_cta_label  string  card button text (default 'Request this teammate')
 *   $vtc_cta_vt     bool    add data-vt-id/data-vt-name for on-page form prefill (default false)
 *   $vtc_browse     bool    show the "Browse all Virtual Teammates" footer line (default true)
 */

$home_base = $home_base ?? './';
$vtc_depts = $vtc_depts ?? [];
if (!$vtc_depts) { return; }

$vtc_keywords   = $vtc_keywords   ?? [];
$vtc_limit      = $vtc_limit      ?? 6;
$vtc_label      = $vtc_label      ?? 'Meet the Bench';
$vtc_heading    = $vtc_heading    ?? 'Meet Your <em>Virtual Teammates</em>';
$vtc_sub        = $vtc_sub        ?? 'A sample of real, vetted teammates — matched to your time zone and ready to start in 1&ndash;2 weeks.';
$vtc_cta_href   = $vtc_cta_href   ?? ($home_base . '#cta-request-teammate');
$vtc_cta_intent = $vtc_cta_intent ?? 'request-teammate';
$vtc_cta_label  = $vtc_cta_label  ?? 'Request this teammate';
$vtc_cta_vt     = $vtc_cta_vt     ?? false;
$vtc_browse     = $vtc_browse     ?? true;

/* ── Load candidates from the portal DB ── */
$vtc_rows = [];
$vtc_dbp  = __DIR__ . '/../data/portal.sqlite';
if (is_file($vtc_dbp)) {
    try {
        $vtc_pdo = new PDO('sqlite:' . $vtc_dbp);
        $vtc_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ph  = implode(',', array_fill(0, count($vtc_depts), '?'));
        $st  = $vtc_pdo->prepare(
            "SELECT u.id, u.first_name, u.last_name, u.country, u.photo_url,
                    p.department, p.role_title, p.primary_skills, p.experience_years
             FROM vt_profiles p
             JOIN users u ON u.id = p.user_id
             WHERE u.role IN ('vt_hired','vt_onpool') AND u.active = 1
               AND u.email NOT LIKE 'demo-%'
               AND p.department IN ($ph)"
        );
        $st->execute(array_values($vtc_depts));
        $vtc_rows = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $_) { $vtc_rows = []; }
}
if (!$vtc_rows) { return; }

/* ── Rank: page-role keyword match first, then teammates with a photo, then
 *    by department + name. Keeps cards relevant but always fills the grid. ── */
$vtc_kw = array_values(array_filter(array_map(
    static fn($k) => trim(mb_strtolower((string) $k)),
    $vtc_keywords
)));
$vtc_score = static function (array $r) use ($vtc_kw): int {
    if (!$vtc_kw) { return 0; }
    $hay = mb_strtolower(($r['role_title'] ?? '') . ' ' . ($r['primary_skills'] ?? ''));
    foreach ($vtc_kw as $k) { if ($k !== '' && str_contains($hay, $k)) { return 1; } }
    return 0;
};
usort($vtc_rows, static function ($a, $b) use ($vtc_score) {
    $sa = $vtc_score($a); $sb = $vtc_score($b);
    if ($sa !== $sb) { return $sb <=> $sa; }
    $pa = trim((string) ($a['photo_url'] ?? '')) !== '' ? 1 : 0;
    $pb = trim((string) ($b['photo_url'] ?? '')) !== '' ? 1 : 0;
    if ($pa !== $pb) { return $pb <=> $pa; }
    return [$a['department'], $a['first_name']] <=> [$b['department'], $b['first_name']];
});
$vtc_total = count($vtc_rows);
$vtc_show  = array_slice($vtc_rows, 0, max(1, (int) $vtc_limit));

/* ── Helpers ── */
$vtc_h = static fn($s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$vtc_split = static function (string $s): array {
    $parts = preg_split('/[\r\n;,|•·]+/u', $s) ?: [];
    $out = [];
    foreach ($parts as $p) { $p = trim($p); if ($p !== '') { $out[$p] = true; } }
    return array_keys($out);
};
?>
<section class="sec vtd-directory vt-cards" aria-labelledby="vtc-h">
  <div class="reveal" style="text-align:center;">
    <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-id-badge"></i> <?= $vtc_h($vtc_label) ?></div>
    <h2 class="svc-h2" id="vtc-h"><?= $vtc_heading /* HTML allowed */ ?></h2>
    <p class="sec-sub" style="max-width:700px;margin:0 auto;"><?= $vtc_sub /* HTML allowed */ ?></p>
  </div>

  <div class="vtd-grid" style="margin-top:34px;">
    <?php foreach ($vtc_show as $i => $v):
        $first   = trim((string) $v['first_name']);
        $lastIni = ($ln = trim((string) $v['last_name'])) !== '' ? mb_strtoupper(mb_substr($ln, 0, 1)) . '.' : '';
        $name    = trim($first . ' ' . $lastIni) ?: 'Virtual Teammate';
        $dept    = trim((string) $v['department']);
        $role    = trim((string) $v['role_title']) ?: ($dept ?: 'Virtual Assistant');
        $country = trim((string) $v['country']);
        $skills  = array_slice($vtc_split((string) ($v['primary_skills'] ?? '')), 0, 4);
        $hasPhoto= trim((string) ($v['photo_url'] ?? '')) !== '';
        $ini     = $vtc_h(mb_strtoupper(mb_substr($name, 0, 1)) ?: '?');
        $delay   = 'd' . (($i % 3) + 1);
    ?>
    <article class="vtd-card reveal <?= $delay ?>">
      <div class="vtd-photo">
        <?php if ($hasPhoto): ?>
          <img src="<?= $home_base ?>talent-photo.php?id=<?= (int) $v['id'] ?>&amp;thumb=1" alt="<?= $vtc_h($name . ' — ' . $role) ?>" decoding="async" loading="lazy" width="92" height="92"
               onerror="this.outerHTML='<span class=&quot;vtd-photo-ph&quot;><?= $ini ?></span>';">
        <?php else: ?>
          <span class="vtd-photo-ph"><?= $ini ?></span>
        <?php endif; ?>
      </div>
      <div class="vtd-name"><?= $vtc_h($name) ?></div>
      <?php if ($dept !== ''): ?><div class="vtd-dept"><?= $vtc_h($dept) ?></div><?php endif; ?>
      <div class="vtd-role"><?= $vtc_h($role) ?></div>
      <?php if ($country !== ''): ?><div class="vtd-loc"><i class="fa-solid fa-location-dot"></i> <?= $vtc_h($country) ?></div><?php endif; ?>
      <?php if ($skills): ?>
        <div class="vtd-skills-h">Skills</div>
        <div class="vtd-skills">
          <?php foreach ($skills as $sk): ?><span class="vtd-skill"><?= $vtc_h($sk) ?></span><?php endforeach; ?>
        </div>
      <?php endif; ?>
      <a class="vtd-view" href="<?= $vtc_h($vtc_cta_href) ?>" data-cta-intent="<?= $vtc_h($vtc_cta_intent) ?>"<?php if ($vtc_cta_vt): ?> data-vt-id="<?= (int) $v['id'] ?>" data-vt-name="<?= $vtc_h($name . ' — ' . $role) ?>"<?php endif; ?>>
        <i class="fa-solid fa-user-plus"></i> <?= $vtc_h($vtc_cta_label) ?>
      </a>
    </article>
    <?php endforeach; ?>
  </div>

  <?php if ($vtc_browse): ?>
  <p class="cta-stages-foot reveal" style="margin-top:28px;">
    <?php if ($vtc_total > count($vtc_show)): ?>Showing <?= count($vtc_show) ?> of <?= (int) $vtc_total ?> on the bench. <?php endif; ?>
    <a href="<?= $home_base ?>virtual-teammates/">Browse all Virtual Teammates</a> to filter by department and skill and see full profiles, intro videos &amp; résumés.
  </p>
  <?php endif; ?>
</section>
<?php
// Reset so a later include on the same request can't inherit stale values.
unset($vtc_depts, $vtc_keywords, $vtc_limit, $vtc_label, $vtc_heading, $vtc_sub,
      $vtc_cta_href, $vtc_cta_intent, $vtc_cta_label, $vtc_cta_vt, $vtc_browse);
