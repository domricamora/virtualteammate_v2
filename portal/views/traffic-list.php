<?php /** @var array $rows @var ?array $stats @var array $top_countries @var array $top_pages @var string $q @var bool $not_ready */
$pageTitle = 'Traffic';
$subtitle  = 'Marketing-site pageviews with IP + geolocation.';
?>

<?php if (!empty($not_ready)): ?>
  <div class="card">
    <h3>Traffic logging not initialized</h3>
    <p class="muted">
      The <code>traffic</code> table doesn&rsquo;t exist in this environment&rsquo;s database yet.
      Re-run <code>/portal/install.php</code> to apply the latest schema, then visit the
      marketing site once to log the first hit.
    </p>
  </div>
<?php else: ?>

  <div class="stat-grid">
    <div class="stat-card" style="cursor:default;">
      <div class="stat-num"><?= (int) $stats['today'] ?></div>
      <div class="stat-lbl">Views today</div>
    </div>
    <div class="stat-card" style="cursor:default;">
      <div class="stat-num"><?= (int) $stats['views_7d'] ?></div>
      <div class="stat-lbl">Views (7d)</div>
    </div>
    <div class="stat-card" style="cursor:default;">
      <div class="stat-num"><?= (int) $stats['views_30d'] ?></div>
      <div class="stat-lbl">Views (30d)</div>
    </div>
    <div class="stat-card" style="cursor:default;">
      <div class="stat-num"><?= (int) $stats['visitors_30d'] ?></div>
      <div class="stat-lbl">Unique IPs (30d)</div>
    </div>
  </div>

  <div class="grid-2">
    <div class="card">
      <h3>Top countries (30d)</h3>
      <?php if (empty($top_countries)): ?>
        <p class="muted">No geo data yet.</p>
      <?php else: ?>
        <table class="data-table compact">
          <thead><tr><th>Country</th><th>Views</th><th>Visitors</th></tr></thead>
          <tbody>
            <?php foreach ($top_countries as $c): ?>
              <tr>
                <td><?= e($c['country']) ?></td>
                <td><strong><?= (int) $c['n'] ?></strong></td>
                <td class="muted"><?= (int) $c['visitors'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3>Top pages (30d)</h3>
      <?php if (empty($top_pages)): ?>
        <p class="muted">No pageviews yet.</p>
      <?php else: ?>
        <table class="data-table compact">
          <thead><tr><th>Path</th><th>Views</th></tr></thead>
          <tbody>
            <?php foreach ($top_pages as $p): ?>
              <tr>
                <td><?= e($p['path']) ?></td>
                <td><strong><?= (int) $p['n'] ?></strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-h">
      <form method="get" class="inline-filter">
        <input type="hidden" name="p" value="traffic">
        <input type="search" name="q" placeholder="Search IP, country, city, path…" value="<?= e($q) ?>">
        <button class="btn-portal-secondary btn-sm" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
      </form>
      <span class="muted small">Showing latest <?= count($rows) ?> of <?= (int) $stats['total'] ?> total</span>
    </div>

    <table class="data-table compact">
      <thead><tr><th>When</th><th>IP</th><th>Location</th><th>Page</th><th>Referrer</th><th>Device</th></tr></thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="muted">No traffic matches.</td></tr>
        <?php else: foreach ($rows as $r):
          $loc = trim(($r['city'] ?? '') . (($r['city'] && $r['region']) ? ', ' : '') . ($r['region'] ?? ''));
          $loc = trim($loc . (($loc && $r['country']) ? ' · ' : '') . ($r['country'] ?? '')) ?: '—';
          $ua  = (string) $r['user_agent'];
          $dev = 'Other';
          if (preg_match('/iphone|android.*mobile|windows phone/i', $ua)) { $dev = 'Mobile'; }
          elseif (preg_match('/ipad|android/i', $ua)) { $dev = 'Tablet'; }
          elseif (preg_match('/windows|macintosh|linux|cros/i', $ua)) { $dev = 'Desktop'; }
          elseif (preg_match('/bot|crawl|spider|slurp|bing|google/i', $ua)) { $dev = 'Bot'; }
        ?>
          <tr>
            <td class="muted small"><?= e(fmt_dt($r['created_at'], 'Y-m-d H:i')) ?></td>
            <td class="small"><?= e($r['ip'] ?: '—') ?></td>
            <td><?= e($loc) ?></td>
            <td class="small"><?= e($r['path'] ?: '/') ?></td>
            <td class="muted small"><?= e($r['referrer'] ? (parse_url($r['referrer'], PHP_URL_HOST) ?: $r['referrer']) : '—') ?></td>
            <td><span class="pill pill-default"><?= e($dev) ?></span></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>
