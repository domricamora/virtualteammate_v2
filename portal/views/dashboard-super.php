<?php /** @var array $user @var array $stats @var array $traffic @var array $trend @var array $cache @var array $ssl */
$pageTitle = 'Super Admin Dashboard';
$subtitle  = 'Operational overview of the VT portal.';
$traffic   = $traffic ?? ['recent' => [], 'top_countries' => [], 'top_pages' => []];
$trend     = $trend ?? ['labels' => [], 'leads' => [], 'traffic' => []];
$cache     = $cache ?? ['enabled' => true, 'version' => ''];
$ssl       = $ssl ?? ['enabled' => false];
?>

<div class="stat-grid">
  <a class="stat-card" href="<?= e(portal_url('users')) ?>">
    <div class="stat-num"><?= (int) $stats['users_total'] ?></div>
    <div class="stat-lbl">Total users</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'client'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_clients'] ?></div>
    <div class="stat-lbl">Clients</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'csm'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_csm'] ?></div>
    <div class="stat-lbl">CSMs</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'vt_hired'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_hired'] ?></div>
    <div class="stat-lbl">VTs hired</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('users', ['role' => 'vt_onpool'])) ?>">
    <div class="stat-num"><?= (int) $stats['users_onpool'] ?></div>
    <div class="stat-lbl">VTs on-pool</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('clients')) ?>">
    <div class="stat-num"><?= (int) $stats['clients_active'] ?></div>
    <div class="stat-lbl">Active clients</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('meetings')) ?>">
    <div class="stat-num"><?= (int) $stats['meetings_upcoming'] ?></div>
    <div class="stat-lbl">Upcoming meetings</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('eod')) ?>">
    <div class="stat-num"><?= (int) $stats['eod_today'] ?></div>
    <div class="stat-lbl">EOD reports today</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('traffic')) ?>">
    <div class="stat-num"><?= (int) ($stats['traffic_today'] ?? 0) ?></div>
    <div class="stat-lbl">Site views today</div>
  </a>
  <a class="stat-card" href="<?= e(portal_url('traffic')) ?>">
    <div class="stat-num"><?= (int) ($stats['traffic_visitors_7d'] ?? 0) ?></div>
    <div class="stat-lbl">Unique visitors (7d)</div>
  </a>
</div>

<div class="card tc-card">
  <div class="card-h">
    <h3><i class="fa-solid fa-chart-area" style="color:var(--gold);margin-right:8px;"></i> Leads &amp; Traffic <span class="muted small">&mdash; last 14 days</span></h3>
    <div class="tc-legend">
      <span class="tc-leg tc-leg-lead"><i></i> Leads <b data-tc-total="leads">0</b></span>
      <span class="tc-leg tc-leg-traf"><i></i> Site views <b data-tc-total="traffic">0</b></span>
    </div>
  </div>
  <div class="trend-chart" id="trendChart" data-trend="<?= e(json_encode($trend)) ?>">
    <div class="tc-tip" hidden></div>
  </div>
</div>

<style>
.tc-card{overflow:hidden;}
.tc-legend{display:flex;gap:16px;flex-wrap:wrap;}
.tc-leg{display:inline-flex;align-items:center;gap:7px;font-size:12.5px;color:var(--text-mute);font-weight:600;}
.tc-leg i{width:12px;height:12px;border-radius:3px;display:inline-block;}
.tc-leg b{color:#fff;font-weight:800;}
.tc-leg-lead i{background:linear-gradient(135deg,var(--gold,#dfa949),#f5e4b8);}
.tc-leg-traf i{background:linear-gradient(135deg,#7c3aed,#a974f5);}
.trend-chart{position:relative;width:100%;margin-top:6px;}
.tc-svg{width:100%;height:auto;display:block;overflow:visible;}
.tc-grid{stroke:rgba(255,255,255,.08);stroke-width:1;}
.tc-area{fill:url(#tcTraf);opacity:0;transition:opacity 1s ease .2s;}
.trend-chart.is-in .tc-area{opacity:1;}
.tc-line{fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;}
.tc-line-traf{stroke:#a974f5;filter:drop-shadow(0 4px 10px rgba(124,58,237,.5));}
.tc-line-lead{stroke:var(--gold,#dfa949);filter:drop-shadow(0 4px 10px rgba(223,169,73,.5));}
.tc-dot-lead{fill:#1a1330;stroke:var(--gold,#dfa949);stroke-width:2.5;opacity:0;transform-box:fill-box;transform-origin:center;animation:tcDot .4s ease forwards;animation-delay:var(--d,0s);}
.trend-chart.is-in .tc-dot-lead{}
@keyframes tcDot{from{opacity:0;transform:scale(0);}to{opacity:1;transform:scale(1);}}
.tc-xlbl{fill:rgba(255,255,255,.5);font-size:13px;font-family:inherit;}
.tc-cursor{stroke:rgba(255,255,255,.35);stroke-width:1;stroke-dasharray:3 3;opacity:0;transition:opacity .12s;}
.tc-cursor.on{opacity:1;}
.tc-hot{fill:transparent;}
.tc-tip{position:absolute;pointer-events:none;z-index:5;transform:translate(-50%,-110%);background:rgba(20,15,46,.96);border:1px solid var(--line-2);border-radius:10px;padding:8px 11px;font-size:12px;color:#fff;white-space:nowrap;box-shadow:0 12px 28px rgba(0,0,0,.5);backdrop-filter:blur(6px);}
.tc-tip-d{font-size:10.5px;color:rgba(255,255,255,.55);margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px;}
.tc-tip-row{display:flex;align-items:center;gap:6px;line-height:1.5;}
.tc-tip-row i{width:9px;height:9px;border-radius:2px;display:inline-block;}
.tc-tip-row b{margin-left:auto;font-weight:800;padding-left:10px;}
.tc-empty{padding:34px 8px;text-align:center;color:var(--text-mute);}
</style>

<script>
(function(){
  var host = document.getElementById('trendChart'); if (!host) return;
  var data; try { data = JSON.parse(host.getAttribute('data-trend')); } catch(e){ return; }
  var labels = (data && data.labels) || [], leads = (data && data.leads) || [], traffic = (data && data.traffic) || [];
  var n = labels.length;
  var sum = function(a){ return a.reduce(function(s,v){ return s + (+v||0); }, 0); };
  var total = { leads: sum(leads), traffic: sum(traffic) };

  // Count-up the legend totals.
  document.querySelectorAll('[data-tc-total]').forEach(function(el){
    var target = total[el.getAttribute('data-tc-total')] || 0, t0 = null, dur = 900;
    function step(ts){ if(!t0) t0 = ts; var p = Math.min(1, (ts-t0)/dur);
      el.textContent = Math.round(target * (1-Math.pow(1-p,3))).toLocaleString();
      if (p < 1) requestAnimationFrame(step); }
    requestAnimationFrame(step);
  });

  if (n === 0 || (total.leads === 0 && total.traffic === 0)) {
    host.innerHTML = '<div class="tc-empty">No leads or traffic recorded in this window yet.</div>';
    return;
  }

  var W = 1000, H = 300, padL = 14, padR = 14, padT = 16, padB = 30;
  var plotW = W - padL - padR, plotH = H - padT - padB;
  var leadsMax = Math.max(1, Math.max.apply(null, leads));
  var trafMax  = Math.max(1, Math.max.apply(null, traffic));
  function X(i){ return padL + (n <= 1 ? plotW/2 : plotW * i / (n-1)); }
  function Y(v, max){ return padT + plotH - plotH * (+v||0) / max; }
  function pts(arr, max){ return arr.map(function(v,i){ return [X(i), Y(v,max)]; }); }
  function smooth(p){
    if (!p.length) return '';
    if (p.length < 3) return 'M' + p.map(function(q){ return q[0]+','+q[1]; }).join(' L');
    var d = 'M' + p[0][0] + ',' + p[0][1];
    for (var i=0;i<p.length-1;i++){
      var p0=p[i-1]||p[i], p1=p[i], p2=p[i+1], p3=p[i+2]||p2;
      var c1x=p1[0]+(p2[0]-p0[0])/6, c1y=p1[1]+(p2[1]-p0[1])/6;
      var c2x=p2[0]-(p3[0]-p1[0])/6, c2y=p2[1]-(p3[1]-p1[1])/6;
      d += ' C'+c1x+','+c1y+' '+c2x+','+c2y+' '+p2[0]+','+p2[1];
    }
    return d;
  }
  var tp = pts(traffic, trafMax), lp = pts(leads, leadsMax);
  var trafLine = smooth(tp), leadLine = smooth(lp);
  var trafArea = trafLine + ' L'+X(n-1)+','+(padT+plotH)+' L'+X(0)+','+(padT+plotH)+' Z';

  var grid = '';
  for (var g=0; g<=3; g++){ var gy = padT + plotH*g/3; grid += '<line class="tc-grid" x1="'+padL+'" y1="'+gy+'" x2="'+(W-padR)+'" y2="'+gy+'"/>'; }
  var idxs = n <= 1 ? [0] : [0, Math.floor((n-1)/2), n-1];
  var xlabels = idxs.map(function(i){ return '<text class="tc-xlbl" x="'+X(i)+'" y="'+(H-9)+'" text-anchor="middle">'+labels[i].substr(5)+'</text>'; }).join('');
  var dots = lp.map(function(q,i){ return '<circle class="tc-dot-lead" cx="'+q[0]+'" cy="'+q[1]+'" r="3.4" style="--d:'+(0.5+i*0.045)+'s"/>'; }).join('');

  var svg =
    '<svg viewBox="0 0 '+W+' '+H+'" class="tc-svg" role="img" aria-label="Leads and site traffic over the last '+n+' days">'
    + '<defs><linearGradient id="tcTraf" x1="0" y1="0" x2="0" y2="1">'
    +   '<stop offset="0%" stop-color="rgba(124,58,237,.42)"/><stop offset="100%" stop-color="rgba(124,58,237,0)"/>'
    + '</linearGradient></defs>'
    + grid
    + '<path class="tc-area" d="'+trafArea+'"/>'
    + '<path class="tc-line tc-line-traf" d="'+trafLine+'"/>'
    + '<path class="tc-line tc-line-lead" d="'+leadLine+'"/>'
    + dots
    + '<line class="tc-cursor" x1="0" y1="'+padT+'" x2="0" y2="'+(padT+plotH)+'"/>'
    + xlabels
    + '<rect class="tc-hot" x="0" y="0" width="'+W+'" height="'+H+'"/>'
    + '</svg>';

  var tip = host.querySelector('.tc-tip');
  host.insertAdjacentHTML('afterbegin', svg);
  var svgEl = host.querySelector('.tc-svg');

  // Animate the line draw-in.
  host.querySelectorAll('.tc-line').forEach(function(p){
    var len = p.getTotalLength();
    p.style.strokeDasharray = len; p.style.strokeDashoffset = len;
    p.getBoundingClientRect();
    p.style.transition = 'stroke-dashoffset 1.25s cubic-bezier(.4,.1,.2,1)';
    p.style.strokeDashoffset = '0';
  });
  requestAnimationFrame(function(){ host.classList.add('is-in'); });

  // Hover tooltip + cursor.
  var cursor = host.querySelector('.tc-cursor');
  var hot = host.querySelector('.tc-hot');
  function locate(ev){
    var r = svgEl.getBoundingClientRect();
    var px = (ev.touches ? ev.touches[0].clientX : ev.clientX) - r.left;
    var i = Math.round((px / r.width) * (n-1));
    i = Math.max(0, Math.min(n-1, i));
    var ux = X(i);
    cursor.setAttribute('x1', ux); cursor.setAttribute('x2', ux); cursor.classList.add('on');
    tip.hidden = false;
    tip.innerHTML = '<div class="tc-tip-d">'+labels[i]+'</div>'
      + '<div class="tc-tip-row"><i style="background:var(--gold,#dfa949)"></i> Leads <b>'+(+leads[i]||0)+'</b></div>'
      + '<div class="tc-tip-row"><i style="background:#a974f5"></i> Views <b>'+(+traffic[i]||0)+'</b></div>';
    tip.style.left = (ux / W * host.clientWidth) + 'px';
    var topUserY = Math.min(Y(leads[i], leadsMax), Y(traffic[i], trafMax));
    tip.style.top = (topUserY / H * (svgEl.getBoundingClientRect().height)) + 'px';
  }
  function clear(){ cursor.classList.remove('on'); tip.hidden = true; }
  hot.addEventListener('mousemove', locate);
  hot.addEventListener('mouseleave', clear);
  hot.addEventListener('touchstart', locate, {passive:true});
  hot.addEventListener('touchmove', locate, {passive:true});
  hot.addEventListener('touchend', clear);
})();
</script>

<div class="grid-2">
  <div class="card">
    <div class="card-h">
      <h3><i class="fa-solid fa-chart-line" style="color:var(--gold);margin-right:8px;"></i> Recent site traffic</h3>
      <a class="btn-portal-secondary btn-sm" href="<?= e(portal_url('traffic')) ?>">View all <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <?php if (empty($traffic['recent'])): ?>
      <p class="muted">No traffic logged yet. Visits to the marketing site will appear here once the beacon fires (and the portal DB is installed in that environment).</p>
    <?php else: ?>
      <table class="data-table compact">
        <thead><tr><th>When</th><th>Location</th><th>IP</th><th>Page</th></tr></thead>
        <tbody>
          <?php foreach ($traffic['recent'] as $r):
            $loc = trim(($r['city'] ?? '') . ($r['city'] && $r['country'] ? ', ' : '') . ($r['country'] ?? '')) ?: '—';
          ?>
            <tr>
              <td class="muted small"><?= local_dt($r['created_at'], 'm-d g:i a') ?></td>
              <td><?= e($loc) ?></td>
              <td class="muted small"><?= e($r['ip'] ?: '—') ?></td>
              <td class="muted small"><?= e($r['path'] ?: '/') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3><i class="fa-solid fa-earth-americas" style="color:var(--gold);margin-right:8px;"></i> Top countries (30d)</h3>
    <?php if (empty($traffic['top_countries'])): ?>
      <p class="muted">No geo data yet.</p>
    <?php else: ?>
      <ul class="people-list">
        <?php foreach ($traffic['top_countries'] as $c): ?>
          <li style="flex-direction:row;align-items:center;justify-content:space-between;">
            <span class="people-name"><?= e($c['country']) ?></span>
            <span class="pill pill-default"><?= (int) $c['n'] ?> views</span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3>Quick actions</h3>
  <div class="actions-row">
    <a class="btn-portal-primary"   href="<?= e(portal_url('users.edit')) ?>"><i class="fa-solid fa-user-plus"></i> New user</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('clients.edit')) ?>"><i class="fa-solid fa-building"></i> New client</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('vts.edit')) ?>"><i class="fa-solid fa-user-tie"></i> New VT profile</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('assignments')) ?>"><i class="fa-solid fa-diagram-project"></i> Edit assignments</a>
    <a class="btn-portal-secondary" href="<?= e(portal_url('audit')) ?>"><i class="fa-solid fa-list-check"></i> Audit log</a>
  </div>
</div>

<div class="card">
  <div class="card-h">
    <h3><i class="fa-solid fa-bolt" style="color:var(--gold);margin-right:8px;"></i> Site cache</h3>
    <span class="pill <?= $cache['enabled'] ? 'pill-active' : 'pill-paused' ?>">
      <?= $cache['enabled'] ? 'Caching ON' : 'Caching OFF' ?>
    </span>
  </div>
  <p class="muted small" style="margin:0 0 16px;">
    Controls browser caching of the marketing site's CSS &amp; JS.
    <strong>On</strong> — visitors cache assets until the next deploy or flush (fastest).
    <strong>Off</strong> — every visit re-fetches assets (useful while iterating on design).
    <strong>Flush</strong> forces all visitors to pull fresh assets on their next load.
    <?php if ($cache['version'] !== ''): ?>
      <br><span class="muted">Last flushed: <code><?= e($cache['version']) ?></code> (UTC).</span>
    <?php endif; ?>
  </p>
  <div class="actions-row">
    <?php if ($cache['enabled']): ?>
      <form method="post" action="<?= e(portal_url('cache.toggle')) ?>" style="display:inline;">
        <?= csrf_field() ?>
        <input type="hidden" name="enabled" value="0">
        <button type="submit" class="btn-portal-secondary"><i class="fa-solid fa-toggle-off"></i> Disable caching</button>
      </form>
    <?php else: ?>
      <form method="post" action="<?= e(portal_url('cache.toggle')) ?>" style="display:inline;">
        <?= csrf_field() ?>
        <input type="hidden" name="enabled" value="1">
        <button type="submit" class="btn-portal-secondary"><i class="fa-solid fa-toggle-on"></i> Enable caching</button>
      </form>
    <?php endif; ?>
    <form method="post" action="<?= e(portal_url('cache.flush')) ?>" style="display:inline;"
          onsubmit="return confirm('Flush the site cache? All visitors will re-fetch CSS &amp; JS on their next page load.');">
      <?= csrf_field() ?>
      <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-broom"></i> Flush cache</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-h">
    <h3><i class="fa-solid fa-lock" style="color:var(--gold);margin-right:8px;"></i> Force HTTPS</h3>
    <span class="pill <?= $ssl['enabled'] ? 'pill-active' : 'pill-paused' ?>">
      <?= $ssl['enabled'] ? 'HTTPS forced' : 'Not forced' ?>
    </span>
  </div>
  <p class="muted small" style="margin:0 0 16px;">
    Redirects every <code>http://</code> request to <code>https://</code> across the marketing site and portal (301).
    <strong>On</strong> — secure by default. <strong>Off</strong> — pages load on whatever scheme they arrive on.
    Localhost and already-secure requests are never redirected.
    <br><span class="muted">Off by default. Only turn it on once a valid SSL certificate is active on the domain, or pages will fail to load. If you ever get locked out, delete <code>data/force_ssl.on</code> on the server (via FTP) to turn it back off.</span>
  </p>
  <div class="actions-row">
    <?php if ($ssl['enabled']): ?>
      <form method="post" action="<?= e(portal_url('ssl.toggle')) ?>" style="display:inline;"
            onsubmit="return confirm('Disable Force HTTPS? Pages will load on http or https as requested.');">
        <?= csrf_field() ?>
        <input type="hidden" name="enabled" value="0">
        <button type="submit" class="btn-portal-secondary"><i class="fa-solid fa-toggle-off"></i> Disable Force HTTPS</button>
      </form>
    <?php else: ?>
      <form method="post" action="<?= e(portal_url('ssl.toggle')) ?>" style="display:inline;"
            onsubmit="return confirm('Enable Force HTTPS? Make sure a valid SSL certificate is active first.');">
        <?= csrf_field() ?>
        <input type="hidden" name="enabled" value="1">
        <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-toggle-on"></i> Enable Force HTTPS</button>
      </form>
    <?php endif; ?>
  </div>
</div>
