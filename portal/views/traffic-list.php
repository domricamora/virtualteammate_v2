<?php /** @var array $rows @var ?array $stats @var array $top_countries @var array $top_pages @var string $q @var bool $not_ready */
$pageTitle = 'Traffic';
$subtitle  = 'Marketing-site pageviews with IP + geolocation.';
$totalAll  = count($rows);
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

  <div class="card" data-list>
    <div class="card-h">
      <div class="list-toolbar">
        <input type="search" data-list-search placeholder="Search IP, country, city, path…" value="<?= e($q) ?>" autocomplete="off">
        <select data-list-pagesize>
          <option value="50" selected>50 / page</option>
          <option value="100">100 / page</option>
          <option value="250">250 / page</option>
          <option value="0">All</option>
        </select>
        <span class="list-counter">Showing latest <strong><?= (int) $totalAll ?></strong> of <strong><?= (int) $stats['total'] ?></strong> total &middot; <span data-list-counter>—</span></span>
      </div>
      <details class="hs-danger" style="margin-left:auto;">
        <summary class="btn-portal-danger btn-sm" style="cursor:pointer;list-style:none;"><i class="fa-solid fa-trash"></i> Clear log</summary>
        <form method="post" action="<?= e(portal_url('traffic.clear')) ?>" style="margin-top:10px;display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
          <?= csrf_field() ?>
          <select name="scope" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);color:#fff;padding:6px 10px;border-radius:6px;font-size:12.5px;">
            <option value="30d">Older than 30 days</option>
            <option value="all">ALL rows</option>
          </select>
          <input type="text" name="confirm" placeholder='Type "DELETE ALL"' style="background:rgba(255,255,255,.04);border:1px solid rgba(229,62,62,.4);color:#fff;padding:6px 10px;border-radius:6px;font-size:12.5px;">
          <button class="btn-portal-danger btn-sm" type="submit">Confirm</button>
        </form>
      </details>
    </div>

    <table class="data-table compact" data-paginate>
      <thead><tr><th>When</th><th>IP</th><th>Location</th><th>Page</th><th>Referrer</th><th>Device</th><th></th></tr></thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr data-empty><td colspan="7" class="muted">No traffic matches.</td></tr>
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
            <td class="muted small"><?= local_dt($r['created_at'], 'Y-m-d g:i a') ?></td>
            <td class="small"><?= e($r['ip'] ?: '—') ?></td>
            <td><?= e($loc) ?></td>
            <td class="small"><?= e($r['path'] ?: '/') ?></td>
            <td class="muted small"><?= e($r['referrer'] ? (parse_url($r['referrer'], PHP_URL_HOST) ?: $r['referrer']) : '—') ?></td>
            <td><span class="pill pill-default"><?= e($dev) ?></span></td>
            <td class="row-actions">
              <form method="post" action="<?= e(portal_url('traffic.delete')) ?>" class="inline-form" onsubmit="return confirm('Remove this row?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button class="btn-portal-danger btn-sm" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
    <div class="list-pager" data-list-pager></div>
  </div>

<?php endif; ?>
