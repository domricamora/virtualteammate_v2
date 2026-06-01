<?php /** @var array $user @var array $data */
$pageTitle = 'CSM Dashboard';
$subtitle  = 'Your client portfolio and recent meetings.';
$trend = $data['trend'] ?? ['labels' => [], 'eod' => [], 'tasks' => [], 'meetings' => []];
?>

<div class="card ct-card">
  <div class="card-h">
    <h3 style="margin:0;"><i class="fa-solid fa-chart-area" style="color:var(--gold);margin-right:8px;"></i> Activity <span class="muted small">&mdash; last 14 days</span></h3>
    <div class="ct-legend">
      <span class="ct-leg" data-k="eod"><i style="background:#dfa949"></i> EOD <b data-tot="eod">0</b></span>
      <span class="ct-leg" data-k="tasks"><i style="background:#a974f5"></i> Assignments <b data-tot="tasks">0</b></span>
      <span class="ct-leg" data-k="meetings"><i style="background:#4ec47e"></i> Meetings <b data-tot="meetings">0</b></span>
    </div>
  </div>
  <div class="ct-chart" id="csmTrend" data-trend="<?= e(json_encode($trend)) ?>"><div class="tc-tip" hidden></div></div>
</div>

<style>
.ct-legend{display:flex;gap:16px;flex-wrap:wrap;}
.ct-leg{display:inline-flex;align-items:center;gap:7px;font-size:12.5px;color:var(--text-mute);font-weight:600;}
.ct-leg i{width:11px;height:11px;border-radius:3px;display:inline-block;}
.ct-leg b{color:#fff;font-weight:800;}
.ct-chart{position:relative;width:100%;margin-top:6px;}
.ct-svg{width:100%;height:auto;display:block;overflow:visible;}
.ct-grid{stroke:rgba(255,255,255,.08);stroke-width:1;}
.ct-line{fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;}
.ct-dot{opacity:0;transform-box:fill-box;transform-origin:center;animation:ctDot .4s ease forwards;animation-delay:var(--d,0s);}
@keyframes ctDot{from{opacity:0;transform:scale(0);}to{opacity:1;transform:scale(1);}}
.ct-xlbl{fill:rgba(255,255,255,.5);font-size:13px;font-family:inherit;}
.ct-cursor{stroke:rgba(255,255,255,.35);stroke-width:1;stroke-dasharray:3 3;opacity:0;}
.ct-cursor.on{opacity:1;}
.ct-empty{padding:34px 8px;text-align:center;color:var(--text-mute);}
.tc-tip{position:absolute;pointer-events:none;z-index:5;transform:translate(-50%,-110%);background:rgba(20,15,46,.96);
  border:1px solid var(--line-2);border-radius:10px;padding:9px 12px;font-size:12px;color:#fff;white-space:nowrap;box-shadow:0 12px 28px rgba(0,0,0,.5);}
.tc-tip-d{font-size:10.5px;color:rgba(255,255,255,.55);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px;}
.tc-tip-row{display:flex;align-items:center;gap:6px;line-height:1.6;}
.tc-tip-row i{width:9px;height:9px;border-radius:2px;display:inline-block;}
.tc-tip-row b{margin-left:auto;font-weight:800;padding-left:12px;}
</style>
<script>
(function(){
  var host = document.getElementById('csmTrend'); if (!host) return;
  var data; try { data = JSON.parse(host.getAttribute('data-trend')); } catch(e){ return; }
  var labels = data.labels||[];
  var series = [
    {k:'eod',      label:'EOD Reports',    color:'#dfa949', data:data.eod||[]},
    {k:'tasks',    label:'VT Assignments', color:'#a974f5', data:data.tasks||[]},
    {k:'meetings', label:'Meetings',       color:'#4ec47e', data:data.meetings||[]}
  ];
  var n = labels.length;
  var sum = function(a){ return a.reduce(function(s,v){return s+(+v||0);},0); };
  document.querySelectorAll('[data-tot]').forEach(function(el){
    var key = el.getAttribute('data-tot'); var s = series.filter(function(x){return x.k===key;})[0];
    var target = s ? sum(s.data) : 0, t0=null;
    function step(ts){ if(!t0)t0=ts; var p=Math.min(1,(ts-t0)/800); el.textContent=Math.round(target*(1-Math.pow(1-p,3))); if(p<1)requestAnimationFrame(step); }
    requestAnimationFrame(step);
  });
  var totalAll = series.reduce(function(s,x){return s+sum(x.data);},0);
  if (n === 0 || totalAll === 0){ host.innerHTML = '<div class="ct-empty">No activity recorded across your accounts in this window yet.</div>'; return; }

  var W=1000,H=300,padL=14,padR=14,padT=16,padB=30, plotW=W-padL-padR, plotH=H-padT-padB;
  var max=1; series.forEach(function(s){ s.data.forEach(function(v){ if((+v||0)>max)max=+v; }); });
  function X(i){ return padL+(n<=1?plotW/2:plotW*i/(n-1)); }
  function Y(v){ return padT+plotH-plotH*(+v||0)/max; }
  function smooth(p){ if(!p.length)return''; if(p.length<3)return 'M'+p.map(function(q){return q[0]+','+q[1];}).join(' L');
    var d='M'+p[0][0]+','+p[0][1];
    for(var i=0;i<p.length-1;i++){ var p0=p[i-1]||p[i],p1=p[i],p2=p[i+1],p3=p[i+2]||p2;
      d+=' C'+(p1[0]+(p2[0]-p0[0])/6)+','+(p1[1]+(p2[1]-p0[1])/6)+' '+(p2[0]-(p3[0]-p1[0])/6)+','+(p2[1]-(p3[1]-p1[1])/6)+' '+p2[0]+','+p2[1]; }
    return d; }
  var grid=''; for(var g=0;g<=3;g++){ var gy=padT+plotH*g/3; grid+='<line class="ct-grid" x1="'+padL+'" y1="'+gy+'" x2="'+(W-padR)+'" y2="'+gy+'"/>'; }
  var idxs=n<=1?[0]:[0,Math.floor((n-1)/2),n-1];
  var xlabels=idxs.map(function(i){ return '<text class="ct-xlbl" x="'+X(i)+'" y="'+(H-9)+'" text-anchor="middle">'+labels[i].substr(5)+'</text>'; }).join('');
  var paths='', dots='';
  series.forEach(function(s){
    var pts=s.data.map(function(v,i){ return [X(i),Y(v)]; });
    paths+='<path class="ct-line ct-line-'+s.k+'" d="'+smooth(pts)+'" stroke="'+s.color+'"/>';
    dots+=pts.map(function(q,i){ return '<circle class="ct-dot" cx="'+q[0]+'" cy="'+q[1]+'" r="3" fill="'+s.color+'" style="--d:'+(0.5+i*0.03)+'s"/>'; }).join('');
  });
  var svg='<svg viewBox="0 0 '+W+' '+H+'" class="ct-svg" role="img" aria-label="Activity over the last '+n+' days">'
    +grid+paths+dots
    +'<line class="ct-cursor" x1="0" y1="'+padT+'" x2="0" y2="'+(padT+plotH)+'"/>'
    +xlabels+'<rect x="0" y="0" width="'+W+'" height="'+H+'" fill="transparent"/></svg>';
  var tip=host.querySelector('.tc-tip');
  host.insertAdjacentHTML('afterbegin', svg);
  var svgEl=host.querySelector('.ct-svg');
  host.querySelectorAll('.ct-line').forEach(function(p){
    var len=p.getTotalLength(); p.style.strokeDasharray=len; p.style.strokeDashoffset=len; p.getBoundingClientRect();
    p.style.transition='stroke-dashoffset 1.2s cubic-bezier(.4,.1,.2,1)'; p.style.strokeDashoffset='0';
  });
  var cursor=host.querySelector('.ct-cursor'), hot=svgEl.querySelector('rect');
  function locate(ev){
    var r=svgEl.getBoundingClientRect();
    var px=(ev.touches?ev.touches[0].clientX:ev.clientX)-r.left;
    var i=Math.max(0,Math.min(n-1,Math.round(px/r.width*(n-1))));
    var ux=X(i); cursor.setAttribute('x1',ux); cursor.setAttribute('x2',ux); cursor.classList.add('on');
    tip.hidden=false;
    tip.innerHTML='<div class="tc-tip-d">'+labels[i]+'</div>'+series.map(function(s){
      return '<div class="tc-tip-row"><i style="background:'+s.color+'"></i> '+s.label+' <b>'+(+s.data[i]||0)+'</b></div>'; }).join('');
    tip.style.left=(ux/W*host.clientWidth)+'px';
    var top=Math.min.apply(null,series.map(function(s){return Y(s.data[i]);}));
    tip.style.top=(top/H*svgEl.getBoundingClientRect().height)+'px';
  }
  function clear(){ cursor.classList.remove('on'); tip.hidden=true; }
  hot.addEventListener('mousemove',locate); hot.addEventListener('mouseleave',clear);
  hot.addEventListener('touchstart',locate,{passive:true}); hot.addEventListener('touchmove',locate,{passive:true}); hot.addEventListener('touchend',clear);
})();
</script>

<div class="card">
  <div class="card-h">
    <h3>My clients (<?= count($data['clients']) ?>)</h3>
    <a class="btn-portal-primary btn-sm" href="<?= e(portal_url('meetings.edit')) ?>"><i class="fa-solid fa-plus"></i> Schedule meeting</a>
  </div>
  <?php if (empty($data['clients'])): ?>
    <p class="muted">No clients assigned to you yet. Ask a super admin to assign you via the Assignments page.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>Company</th><th>Primary contact</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($data['clients'] as $c): ?>
          <tr>
            <td><?= e($c['company_name']) ?></td>
            <td>
              <?php $name = trim(($c['c_fn'] ?? '') . ' ' . ($c['c_ln'] ?? '')); ?>
              <?php if ($name): ?><div><?= e($name) ?></div><?php endif; ?>
              <div class="muted"><?= e($c['c_email'] ?? $c['company_email']) ?></div>
            </td>
            <td><span class="pill pill-<?= e($c['contract_status']) ?>"><?= e($c['contract_status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="card">
  <h3>Recent meetings</h3>
  <?php if (empty($data['meetings'])): ?>
    <p class="muted">No meetings yet.</p>
  <?php else: ?>
    <table class="data-table">
      <thead><tr><th>When</th><th>Client</th><th>With</th><th>Topic</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($data['meetings'] as $m): ?>
          <tr>
            <td><?= e(fmt_dt($m['scheduled_at'], 'Y-m-d H:i')) ?></td>
            <td><?= e($m['company_name']) ?></td>
            <td><?= e(ucfirst($m['meeting_with_role'])) ?></td>
            <td><?= e($m['topic']) ?></td>
            <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($m['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
