<?php /* Reusable VT profile modal. Include once on a page, then add any
   element with data-open-vt="<vtUserId>" to open it. Loads the full profile
   from the auth-gated index.php?p=vts.profile_json endpoint. */ ?>
<div class="vtm-modal" id="vtmModal" hidden>
  <div class="vtm-modal__backdrop" data-close></div>
  <div class="vtm-modal__panel" role="dialog" aria-modal="true" aria-labelledby="vtmName">
    <button class="vtm-modal__close" type="button" data-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <div class="vtm-modal__body" id="vtmBody"><div class="vtm-modal__loading" aria-live="polite">Loading…</div></div>
  </div>
</div>
<style>
.vtm-modal{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;padding:24px;}
.vtm-modal[hidden]{display:none;}
.vtm-modal__backdrop{position:absolute;inset:0;background:rgba(8,6,24,.78);backdrop-filter:blur(6px);}
.vtm-modal__panel{position:relative;background:#0f0c28;border:1px solid rgba(255,255,255,.08);
  border-radius:18px;max-width:960px;width:100%;max-height:90vh;overflow:hidden;
  box-shadow:0 40px 80px rgba(0,0,0,.7);display:flex;flex-direction:column;
  animation:vtmPanelIn .25s cubic-bezier(.2,.9,.3,1) both;}
@keyframes vtmPanelIn{from{transform:translateY(14px) scale(.97);opacity:0;}to{transform:none;opacity:1;}}
.vtm-modal__close{position:absolute;top:14px;right:14px;z-index:2;width:34px;height:34px;border-radius:50%;
  border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.06);color:#fff;font-size:14px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;transition:background .15s;}
.vtm-modal__close:hover{background:rgba(247,185,69,.18);border-color:rgba(247,185,69,.5);}
.vtm-modal__body{overflow:auto;padding:28px 30px 24px;}
.vtm-modal__loading{padding:60px 0;text-align:center;color:rgba(255,255,255,.6);}
.vtm-hero{display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;margin-bottom:18px;}
.vtm-hero-photo{width:118px;height:118px;border-radius:50%;object-fit:cover;flex:0 0 118px;
  border:3px solid rgba(247,185,69,.35);background:linear-gradient(135deg,#322a5a 0%,#4a4178 100%);
  display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:800;color:#fff;box-shadow:0 10px 26px rgba(0,0,0,.4);}
.vtm-hero-meta{flex:1;min-width:240px;}
.vtm-hero-name{margin:0 0 6px;font-size:22px;font-weight:800;color:#fff;line-height:1.15;}
.vtm-hero-role{margin:0 0 10px;font-size:13.5px;color:var(--gold-lt,#e9c879);}
.vtm-hero-row{display:flex;flex-wrap:wrap;gap:6px;}
.vtm-pill{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:999px;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);color:rgba(255,255,255,.85);}
.vtm-pill i{color:var(--gold,#d4a64a);}
.vtm-pill.is-active{background:rgba(78,196,126,.18);color:#bcf0d2;border-color:rgba(78,196,126,.35);}
.vtm-pill.is-paused{background:rgba(233,176,75,.18);color:#ffe2a8;border-color:rgba(233,176,75,.35);}
.vtm-kv{display:grid;grid-template-columns:140px 1fr;gap:8px 14px;font-size:13.5px;margin:12px 0;}
.vtm-kv dt{color:rgba(255,255,255,.55);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;padding-top:2px;}
.vtm-kv dd{color:rgba(255,255,255,.92);word-break:break-word;margin:0;}
.vtm-media{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;}
@media (max-width:780px){.vtm-media{grid-template-columns:1fr;}}
.vtm-media-card{background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:12px;}
.vtm-media-h{font-size:11px;font-weight:800;color:var(--gold,#d4a64a);text-transform:uppercase;letter-spacing:1.1px;margin-bottom:8px;display:flex;align-items:center;gap:6px;}
.vtm-video,.vtm-pdf{background:#000;border-radius:8px;overflow:hidden;aspect-ratio:16/9;}
.vtm-video video,.vtm-pdf embed{width:100%;height:100%;display:block;border:0;}
.vtm-pdf{aspect-ratio:8.5/11;min-height:360px;background:#1a1535;}
.vtm-media-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:34px 14px;
  color:rgba(255,255,255,.45);text-align:center;background:rgba(255,255,255,.02);border-radius:8px;aspect-ratio:16/9;}
.vtm-media-empty i{font-size:34px;color:rgba(247,185,69,.3);margin-bottom:8px;}
.vtm-media-foot{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;}
.vtm-section{margin-top:18px;}
.vtm-section h4{margin:0 0 8px;font-size:12px;font-weight:800;color:var(--gold,#d4a64a);text-transform:uppercase;letter-spacing:1.1px;}
.vtm-section p{margin:0;font-size:13.5px;color:rgba(255,255,255,.85);line-height:1.6;white-space:pre-wrap;}
.vtm-people{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:6px;}
.vtm-people li{padding:8px 12px;background:rgba(255,255,255,.03);border-radius:8px;font-size:13px;color:rgba(255,255,255,.85);}
.vtm-people li strong{color:#fff;}
.vtm-err{padding:30px 0;text-align:center;color:#ff8b8b;}
</style>
<script>
(function(){
  var modal  = document.getElementById('vtmModal');
  var bodyEl = document.getElementById('vtmBody');
  if (!modal || !bodyEl) return;
  function escapeHtml(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
  function fmt(v,fb){ v=(v==null?'':String(v)).trim(); return v!==''?escapeHtml(v):(fb||'<span style="color:rgba(255,255,255,.4);">—</span>'); }
  function initialsOf(vt){ var f=(vt.first_name||'').trim(),l=(vt.last_name||'').trim(); if(f||l)return (f.charAt(0)+l.charAt(0)).toUpperCase(); return (vt.email||'?').charAt(0).toUpperCase(); }
  function isPortalMediaUrl(u){ return /^index\.php\?p=media/.test(String(u||'')); }
  function render(data){
    var vt=data.vt||{}, clients=data.clients||[], csms=data.csms||[];
    var fullName=((vt.first_name||'')+' '+(vt.last_name||'')).trim()||vt.email||'Virtual Teammate';
    var roleStr=(vt.role_title||'').trim();
    var statusPill = vt.status==='hired' ? '<span class="vtm-pill is-active"><i class="fa-solid fa-check"></i> Hired</span>'
      : (vt.status==='onpool' ? '<span class="vtm-pill is-paused"><i class="fa-solid fa-clock"></i> On talent pool</span>'
                              : (vt.status?'<span class="vtm-pill">'+escapeHtml(vt.status)+'</span>':''));
    var photoBlock = vt.photo_url
      ? '<img class="vtm-hero-photo" src="'+escapeHtml(vt.photo_url)+'" alt="" onerror="this.onerror=null;this.outerHTML=\'<div class=&quot;vtm-hero-photo&quot;>'+escapeHtml(initialsOf(vt))+'</div>\';">'
      : '<div class="vtm-hero-photo">'+escapeHtml(initialsOf(vt))+'</div>';
    var pills=[]; if(statusPill)pills.push(statusPill);
    if(vt.country)pills.push('<span class="vtm-pill"><i class="fa-solid fa-location-dot"></i> '+escapeHtml(vt.country)+'</span>');
    if(vt.english_level)pills.push('<span class="vtm-pill"><i class="fa-solid fa-language"></i> '+escapeHtml(vt.english_level)+'</span>');
    if(vt.experience_years)pills.push('<span class="vtm-pill"><i class="fa-solid fa-briefcase"></i> '+escapeHtml(vt.experience_years)+' yrs exp</span>');
    if(vt.hipaa_certified)pills.push('<span class="vtm-pill"><i class="fa-solid fa-shield"></i> HIPAA '+escapeHtml(vt.hipaa_certified)+'</span>');
    function videoBlock(){
      if(!vt.video_url) return '<div class="vtm-media-empty"><i class="fa-solid fa-video-slash"></i><p>No intro video on file.</p></div>';
      var poster=vt.photo_url?' poster="'+escapeHtml(vt.photo_url)+'"':'';
      return '<div class="vtm-video"><video controls preload="metadata" playsinline'+poster+'><source src="'+escapeHtml(vt.video_url)+'">Your browser does not support inline video.</video></div>'+
        '<div class="vtm-media-foot"><a class="btn-portal-secondary btn-sm" href="'+escapeHtml(vt.video_url)+'" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> '+(isPortalMediaUrl(vt.video_url)?'Open':'External link')+'</a></div>';
    }
    function resumeBlock(){
      if(!vt.resume_url) return '<div class="vtm-media-empty"><i class="fa-solid fa-file-circle-xmark"></i><p>No resume on file.</p></div>';
      return '<div class="vtm-pdf"><embed src="'+escapeHtml(vt.resume_url)+'#toolbar=1&navpanes=0" type="application/pdf"></div>'+
        '<div class="vtm-media-foot"><a class="btn-portal-secondary btn-sm" href="'+escapeHtml(vt.resume_url)+'" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> '+(isPortalMediaUrl(vt.resume_url)?'Open':'External link')+'</a>'+
        (isPortalMediaUrl(vt.resume_url)?' <a class="btn-portal-secondary btn-sm" href="'+escapeHtml(vt.resume_url)+'" download><i class="fa-solid fa-download"></i> Download</a>':'')+'</div>';
    }
    var kv=[['Department',vt.department],['Role title',vt.role_title],['EHR / software',vt.ehr_software],['Email',vt.email],['Phone',vt.phone],['IQ band',vt.iq_band],['Technical band',vt.technical_band],['CI role',vt.ci_role],['DISC profile',vt.disc_profile]];
    var kvRows=kv.filter(function(r){return r[1];}).map(function(r){return '<dt>'+escapeHtml(r[0])+'</dt><dd>'+fmt(r[1])+'</dd>';}).join('');
    var clientsHtml=clients.length?'<ul class="vtm-people">'+clients.map(function(c){return '<li><strong>'+escapeHtml(c.company_name||'')+'</strong>'+(c.started_at?' &middot; <span class="muted small">started '+escapeHtml(String(c.started_at).slice(0,10))+'</span>':'')+'</li>';}).join('')+'</ul>':'<p style="color:rgba(255,255,255,.5);font-size:13px;">Not currently assigned to any client.</p>';
    var summary=(vt.summary||'').trim(), exp=(vt.experience_text||'').trim();
    bodyEl.innerHTML='<div class="vtm-hero">'+photoBlock+'<div class="vtm-hero-meta"><h3 class="vtm-hero-name" id="vtmName">'+escapeHtml(fullName)+'</h3>'+(roleStr?'<p class="vtm-hero-role">'+escapeHtml(roleStr)+'</p>':'')+'<div class="vtm-hero-row">'+pills.join('')+'</div></div></div>'+
      '<div class="vtm-media"><div class="vtm-media-card"><div class="vtm-media-h"><i class="fa-solid fa-video"></i> Intro video</div>'+videoBlock()+'</div>'+
      '<div class="vtm-media-card"><div class="vtm-media-h"><i class="fa-solid fa-file-pdf"></i> Resume</div>'+resumeBlock()+'</div></div>'+
      (kvRows?'<div class="vtm-section"><h4>Profile</h4><dl class="vtm-kv">'+kvRows+'</dl></div>':'')+
      (summary?'<div class="vtm-section"><h4>Summary</h4><p>'+escapeHtml(summary)+'</p></div>':'')+
      (exp?'<div class="vtm-section"><h4>Experience</h4><p>'+escapeHtml(exp)+'</p></div>':'')+
      '<div class="vtm-section"><h4>Assigned clients</h4>'+clientsHtml+'</div>';
  }
  function open(vid){
    if(!vid)return;
    bodyEl.innerHTML='<div class="vtm-modal__loading">Loading…</div>';
    modal.hidden=false; document.body.style.overflow='hidden';
    fetch('index.php?p=vts.profile_json&id='+encodeURIComponent(vid),{credentials:'same-origin'})
      .then(function(r){ if(!r.ok)throw new Error('HTTP '+r.status); return r.json(); })
      .then(function(data){ if(data&&data.error)throw new Error(data.error); render(data); })
      .catch(function(err){ bodyEl.innerHTML='<div class="vtm-err">Could not load profile: '+(err&&err.message||'unknown')+'</div>'; });
  }
  function close(){ modal.hidden=true; document.body.style.overflow=''; bodyEl.innerHTML=''; }
  document.addEventListener('click', function(e){
    var t=e.target.closest('[data-open-vt]'); if(!t)return; e.preventDefault(); open(t.getAttribute('data-open-vt'));
  });
  modal.querySelectorAll('[data-close]').forEach(function(el){ el.addEventListener('click', close); });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape'&&!modal.hidden) close(); });
})();
</script>
