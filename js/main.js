(function(){
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Nav hamburger + dropdown accordion (tablet & mobile).
     We use a single matchMedia change listener for the 1280px breakpoint
     instead of a resize handler — fires only when the breakpoint is crossed,
     not on every pixel of a window-drag. */
  var navToggle = document.getElementById('navToggle');
  var navLinks  = document.getElementById('primaryNav');
  var navDrops  = document.querySelectorAll('.nav-drop');
  var navEl     = document.querySelector('.nav');
  var navMql    = matchMedia('(max-width:1280px)');

  function isMobileNav(){ return navMql.matches; }

  function closeNav(){
    if (!navToggle || !navLinks) return;
    navToggle.classList.remove('on');
    navToggle.setAttribute('aria-expanded', 'false');
    navLinks.classList.remove('open');
    navDrops.forEach(function(d){ d.classList.remove('open'); });
  }

  if (navToggle && navLinks){
    navToggle.addEventListener('click', function(){
      var open = !navLinks.classList.contains('open');
      navLinks.classList.toggle('open', open);
      navToggle.classList.toggle('on', open);
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  navDrops.forEach(function(d){
    var trigger = d.querySelector('.nav-drop-trigger');
    if (!trigger) return;
    trigger.addEventListener('click', function(e){
      if (!isMobileNav()) return; // desktop: pure CSS hover dropdown
      e.preventDefault();
      d.classList.toggle('open');
      trigger.setAttribute('aria-expanded', d.classList.contains('open') ? 'true' : 'false');
    });
  });

  // Close drawer when any link inside is followed (mobile UX).
  if (navLinks){
    navLinks.addEventListener('click', function(e){
      var a = e.target.closest('a');
      if (!a || !isMobileNav()) return;
      if (a.classList.contains('nav-drop-trigger')) return; // accordion toggle, not nav
      closeNav();
    });
  }

  // Close drawer when clicking outside the nav on mobile.
  document.addEventListener('click', function(e){
    if (!isMobileNav()) return;
    if (!navLinks || !navLinks.classList.contains('open')) return;
    if (navEl && !navEl.contains(e.target)) closeNav();
  });

  // Reset drawer state only when the breakpoint is crossed.
  function onBreakpointChange(){ if (!navMql.matches) closeNav(); }
  if (navMql.addEventListener) navMql.addEventListener('change', onBreakpointChange);
  else if (navMql.addListener) navMql.addListener(onBreakpointChange); // Safari < 14 fallback

  /* CTA intent switching — secondary CTAs deep-link with data-cta-intent,
     CTA form tabs swap heading/submit copy and a hidden intent input. */
  var INTENTS = {
    'strategy-call':    { h: 'Strategy Call &amp; Jumpstart',           s: '30-minute call. We map your needs, define the role, and match you to candidates within 5–7 business days.', btn: 'Book My Strategy Call' },
    'practice-audit':   { h: 'Free 20-min Practice Audit',              s: 'Diagnostic-only call. Workflow inventory + outsourcing priority list + tier and headcount recommendation.', btn: 'Book My Practice Audit' },
    'buyers-checklist': { h: 'HIPAA VA Buyer’s Checklist',              s: 'PDF emailed within one business day. The 22 questions to ask any healthcare VA agency before you sign.', btn: 'Send Me the Checklist' }
  };
  var ctaTabs     = document.querySelectorAll('.cta-intent');
  var ctaHeading  = document.getElementById('ctaHeading');
  var ctaSub      = document.getElementById('ctaSub');
  var ctaIntent   = document.getElementById('ctaIntent');
  var ctaSubmit   = document.getElementById('ctaSubmit');

  function setCtaIntent(key){
    var cfg = INTENTS[key];
    if (!cfg) return;
    if (ctaIntent)  ctaIntent.value = key;
    if (ctaHeading) ctaHeading.innerHTML = cfg.h;
    if (ctaSub)     ctaSub.textContent = cfg.s;
    if (ctaSubmit){
      ctaSubmit.innerHTML = cfg.btn + ' <i class="fa-solid fa-arrow-right"></i>';
    }
    ctaTabs.forEach(function(t){
      var on = t.getAttribute('data-intent') === key;
      t.classList.toggle('on', on);
      t.setAttribute('aria-selected', on ? 'true' : 'false');
    });
  }

  ctaTabs.forEach(function(t){
    t.addEventListener('click', function(){
      setCtaIntent(t.getAttribute('data-intent'));
    });
  });

  // Wire all secondary-CTA anchors across the page.
  document.querySelectorAll('a[data-cta-intent]').forEach(function(a){
    a.addEventListener('click', function(){
      setCtaIntent(a.getAttribute('data-cta-intent'));
    });
  });

  /* Scroll to top button */
  var scrollTopBtn = document.getElementById('scrollTop');
  if (scrollTopBtn){
    var showAfter = 600;
    var ticking = false;
    function syncScrollTop(){
      scrollTopBtn.classList.toggle('show', window.scrollY > showAfter);
      ticking = false;
    }
    window.addEventListener('scroll', function(){
      if (!ticking){ requestAnimationFrame(syncScrollTop); ticking = true; }
    }, { passive: true });
    scrollTopBtn.addEventListener('click', function(){
      window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
    });
    syncScrollTop();
  }

  /* Scroll reveal */
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && !reduce){
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(e){
        if (e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    revealEls.forEach(function(el){ io.observe(el); });
  } else {
    revealEls.forEach(function(el){ el.classList.add('in'); });
  }

  /* Count-up */
  function easeOutCubic(t){ return 1 - Math.pow(1-t, 3); }
  function countUp(el){
    if (el.dataset.done) return;
    el.dataset.done = '1';
    var target = +el.dataset.count || 0;
    var suffix = el.dataset.suffix || '';
    if (reduce){ el.textContent = target + suffix; return; }
    var start = performance.now(), dur = 1400;
    function frame(now){
      var t = Math.min(1, (now - start) / dur);
      var v = Math.round(target * easeOutCubic(t));
      el.textContent = v.toLocaleString() + suffix;
      if (t < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }
  var counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window){
    var cio = new IntersectionObserver(function(entries){
      entries.forEach(function(e){ if (e.isIntersecting){ countUp(e.target); cio.unobserve(e.target); }});
    }, { threshold: 0.4 });
    counters.forEach(function(el){ cio.observe(el); });
  } else {
    counters.forEach(countUp);
  }

  /* Reusable: append N logo items to a track as a DocumentFragment
     (avoids innerHTML parsing and any XSS risk from name/href strings). */
  function buildMarqueeFragment(list, makeItem){
    var frag = document.createDocumentFragment();
    for (var i = 0; i < list.length; i++) frag.appendChild(makeItem(list[i]));
    return frag;
  }

  /* Client logo marquee — list is generated server-side from
     images/clients/marquee/ and injected as window.VT_MARQUEE. */
  var clients = (window.VT_MARQUEE || []).map(function(src){ return { name:'Client', src:src }; });
  var track = document.getElementById('mqTrack');
  if (track && clients.length){
    function makeClientItem(c){
      var div = document.createElement('div');
      div.className = 'mq-logo';
      div.title = c.name;
      var img = document.createElement('img');
      img.src = c.src;
      img.alt = c.name;
      img.loading = 'lazy';
      div.appendChild(img);
      return div;
    }
    track.appendChild(buildMarqueeFragment(clients, makeClientItem));
    track.appendChild(buildMarqueeFragment(clients, makeClientItem));
  }

  /* Press / News logo marquee — assets mirrored locally from
     staging.virtualteammate.com into press/. Toned-down white silhouette
     by default, full color on hover (CSS-driven). */
  var press = [
    { name:'Business Insider',   src:'images/press/business-insider.png',   href:'https://markets.businessinsider.com/news/stocks/virtual-teammate-celebrates-a-year-of-game-changing-growth-1034951486' },
    { name:'Yahoo Finance',      src:'images/press/yahoo-finance.png',      href:'https://finance.yahoo.com/news/virtual-teammate-celebrates-game-changing-150000931.html' },
    { name:'Star Tribune',       src:'images/press/star-tribune.png',       href:'https://markets.financialcontent.com/startribune/article/accwirecq-2025-7-29-virtual-teammate-celebrates-a-year-of-game-changing-growth' },
    { name:'Miami Herald',       src:'images/press/miami-herald.png',       href:'https://www.miamiherald.com/press-releases/article311504009.html' },
    { name:'Courier Post',       src:'images/press/courier-post.png',       href:'https://hannibal.marketminute.com/article/accwirecq-2025-7-29-virtual-teammate-celebrates-a-year-of-game-changing-growth' },
    { name:'WRAL',               src:'images/press/wral.png',               href:'https://markets.financialcontent.com/wral/article/accwirecq-2025-7-29-virtual-teammate-celebrates-a-year-of-game-changing-growth' },
    { name:'Kansas City Star',   src:'images/press/kansas-city-star.png',   href:'https://www.kansascity.com/press-releases/article311504009.html' },
    { name:'Charlotte Observer', src:'images/press/charlotte-observer.png', href:'https://www.charlotteobserver.com/press-releases/article311504009.html' },
    { name:'Boston Herald',      src:'images/press/boston-herald.png',      href:'https://markets.financialcontent.com/bostonherald/article/accwirecq-2025-7-29-virtual-teammate-celebrates-a-year-of-game-changing-growth' },
    { name:'Sacramento Bee',     src:'images/press/sacramento-bee.png',     href:'https://www.sacbee.com/press-releases/article311504009.html' },
    { name:'KDVR',               src:'images/press/kdvr.png',               href:'https://www.kdvr.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth' },
    { name:'PIX11',              src:'images/press/pix11.png',              href:'https://www.pix11.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth' },
    { name:'News & Observer',    src:'images/press/news-observer.png',      href:'https://www.newsobserver.com/press-releases/article311504009.html' },
    { name:'WGN-TV',             src:'images/press/wgn-tv.png',             href:'https://wgntv.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth/' },
    { name:'FOX 2 St. Louis',    src:'images/press/fox2-st-louis.png',      href:'https://fox2now.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth/' },
    { name:'WAVY',               src:'images/press/wavy.png',               href:'https://www.wavy.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth/' },
    { name:'KTLA',               src:'images/press/ktla.png',               href:'https://ktla.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth/' },
    { name:'8 News Now',         src:'images/press/8-news-now.png',         href:'https://www.8newsnow.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth/' },
    { name:'FOX 8 Cleveland',    src:'images/press/fox8-cleveland.png',     href:'https://www.fox8.com/business/press-releases/accesswire/1052573/virtual-teammate-celebrates-a-year-of-game-changing-growth' }
  ];
  var newsTrack = document.getElementById('newsTrack');
  if (newsTrack){
    function makePressItem(p){
      var a = document.createElement('a');
      a.className = 'news-item';
      a.href = p.href;
      a.target = '_blank';
      a.rel = 'noopener';
      a.title = p.name;
      var img = document.createElement('img');
      img.src = p.src;
      img.alt = p.name;
      img.loading = 'lazy';
      a.appendChild(img);
      return a;
    }
    newsTrack.appendChild(buildMarqueeFragment(press, makePressItem));
    newsTrack.appendChild(buildMarqueeFragment(press, makePressItem));
  }

  /* ROI Calculator — matches live staging plugin BIWEEK rates */
  var BIWEEK = {
    pro:        { vt: { ft: 750,  pt: 400 }, us: { ft: 1800, pt: 960  } },
    specialist: { vt: { ft: 1000, pt: 600 }, us: { ft: 2475, pt: 1320 } }
  };

  var state = { tier: 'pro', sched: 'ft', count: 2 };

  var roleSel    = document.getElementById('calcRole');
  var countEl    = document.getElementById('calcCount');
  var countValEl = document.getElementById('calcCountVal');
  var tierBtns   = document.querySelectorAll('.calc-seg [data-tier]');
  var schedBtns  = document.querySelectorAll('.calc-seg [data-sched]');

  var $annual  = document.getElementById('calcAnnual');
  var $monthly = document.getElementById('calcMonthly');
  var $usAmt   = document.getElementById('calcUsAmt');
  var $vtAmt   = document.getElementById('calcVtAmt');
  var $usBar   = document.getElementById('calcUsBar');
  var $vtBar   = document.getElementById('calcVtBar');
  var $gauge   = document.getElementById('calcGaugeFg');
  var $pct     = document.getElementById('calcPct');
  var $3yr     = document.getElementById('calc3yr');
  var $perVa   = document.getElementById('calcPerVa');
  var $ctaAmt  = document.getElementById('calcCtaAmt');

  function fmt(n){
    var v = Math.round(Number(n) || 0);
    return '$' + v.toLocaleString();
  }

  /* Per-element tween state + token. The token mechanism is critical:
     dragging the count slider used to spawn a new 900ms RAF chain per input
     event with no cancellation — overlapping chains fought over textContent
     and burned CPU. We now bump the token on each tween; in-flight frames
     check their token against current and exit early if superseded. */
  var tweenState = new WeakMap(); // el -> { value: number, token: number }
  function tweenNum(el, to, opts){
    if (!el) return;
    opts = opts || {};
    var prefix = ('prefix' in opts) ? opts.prefix : '$';
    var suffix = opts.suffix || '';
    var dur = reduce ? 0 : (opts.dur || 900);
    var s = tweenState.get(el) || { value: 0, token: 0 };
    var from = s.value;
    var token = s.token + 1;
    if (dur === 0){
      tweenState.set(el, { value: to, token: token });
      el.textContent = prefix + Math.round(to).toLocaleString() + suffix;
      return;
    }
    // Seed with the new token so older frames see they're stale.
    tweenState.set(el, { value: from, token: token });
    var start = performance.now();
    function frame(now){
      var cur = tweenState.get(el);
      if (!cur || cur.token !== token) return; // superseded
      var t = Math.min(1, (now - start) / dur);
      var v = from + (to - from) * easeOutCubic(t);
      el.textContent = prefix + Math.round(v).toLocaleString() + suffix;
      tweenState.set(el, { value: v, token: token });
      if (t < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  /* Same cancellation pattern, scoped to the percentage element. */
  var pctToken = 0;
  function tweenPct(target){
    if (!$pct) return;
    if (reduce){ $pct.textContent = target + '%'; $pct.dataset.cur = target; return; }
    var startP = +($pct.dataset.cur || 0), durP = 800, t0 = performance.now();
    var token = ++pctToken;
    function fr(now){
      if (token !== pctToken) return; // superseded
      var k = Math.min(1, (now - t0) / durP);
      var v = Math.round(startP + (target - startP) * easeOutCubic(k));
      $pct.textContent = v + '%';
      if (k < 1) requestAnimationFrame(fr);
      else $pct.dataset.cur = target;
    }
    requestAnimationFrame(fr);
  }

  function setActiveBtn(group, key, dataAttr){
    group.forEach(function(b){
      b.classList.toggle('on', b.getAttribute(dataAttr) === key);
    });
  }

  /* recalc coalesced into a single RAF — multiple input events within the
     same frame (slider drag, button mashing) collapse to one DOM update batch. */
  var recalcPending = false;
  function scheduleRecalc(){
    if (recalcPending) return;
    recalcPending = true;
    requestAnimationFrame(function(){ recalcPending = false; recalc(); });
  }

  function recalc(){
    if (!$annual) return; // calculator not present on this page
    var t = BIWEEK[state.tier] || BIWEEK.pro;
    var vtBi = (t.vt[state.sched] || 0) * state.count;
    var usBi = (t.us[state.sched] || 0) * state.count;
    var saveBi = usBi - vtBi;

    var vtAnnual = vtBi * 26;
    var usAnnual = usBi * 26;
    var annualSave = saveBi * 26;
    var monthlySave = annualSave / 12;
    var pct = usAnnual > 0 ? Math.round((annualSave / usAnnual) * 100) : 0;

    tweenNum($annual, annualSave);
    tweenNum($monthly, monthlySave);
    tweenNum($usAmt,  usAnnual, { suffix: ' / yr' });
    tweenNum($vtAmt,  vtAnnual, { suffix: ' / yr' });
    tweenNum($3yr,    annualSave * 3);
    tweenNum($perVa,  state.count > 0 ? annualSave / state.count : 0);
    tweenPct(pct);

    if ($gauge){
      var C = 2 * Math.PI * 84;
      var dash = (Math.max(0, Math.min(100, pct)) / 100) * C;
      $gauge.setAttribute('stroke-dasharray', dash + ' ' + (C - dash));
    }

    var vtPct = usAnnual > 0 ? Math.max(4, (vtAnnual / usAnnual) * 100) : 0;
    if ($usBar) $usBar.style.width = '100%';
    if ($vtBar) $vtBar.style.width = vtPct + '%';

    if ($ctaAmt) $ctaAmt.textContent = fmt(annualSave) + ' / yr';
  }

  if (roleSel){
    roleSel.addEventListener('change', function(){
      var opt = roleSel.options[roleSel.selectedIndex];
      var t = opt && opt.dataset.tier;
      if (t){ state.tier = t; setActiveBtn(tierBtns, t, 'data-tier'); }
      scheduleRecalc();
    });
  }
  tierBtns.forEach(function(b){
    b.addEventListener('click', function(){
      state.tier = b.getAttribute('data-tier');
      setActiveBtn(tierBtns, state.tier, 'data-tier');
      scheduleRecalc();
    });
  });
  schedBtns.forEach(function(b){
    b.addEventListener('click', function(){
      state.sched = b.getAttribute('data-sched');
      setActiveBtn(schedBtns, state.sched, 'data-sched');
      scheduleRecalc();
    });
  });
  if (countEl){
    countEl.addEventListener('input', function(){
      state.count = parseInt(countEl.value, 10) || 1;
      countValEl.textContent = state.count;
      var pctFill = ((state.count - countEl.min) / (countEl.max - countEl.min)) * 100;
      countEl.style.background = 'linear-gradient(90deg, var(--gold) 0%, var(--gold) ' + pctFill + '%, rgba(255,255,255,0.12) ' + pctFill + '%)';
      scheduleRecalc();
    });
    // Seed initial fill + state without dispatching an input event
    // (avoids the double-recalc on load — scheduleRecalc below handles it).
    var initFill = ((state.count - countEl.min) / (countEl.max - countEl.min)) * 100;
    countEl.style.background = 'linear-gradient(90deg, var(--gold) 0%, var(--gold) ' + initFill + '%, rgba(255,255,255,0.12) ' + initFill + '%)';
    if (countValEl) countValEl.textContent = state.count;
  }
  if (roleSel){
    var opt0 = roleSel.options[roleSel.selectedIndex];
    if (opt0 && opt0.dataset.tier){
      state.tier = opt0.dataset.tier;
      setActiveBtn(tierBtns, state.tier, 'data-tier');
    }
  }
  recalc();

  /* ── Branded lead forms ──────────────────────────────────────────────
     Any <form data-lead-form> posts to its action (default lead.php) and
     swaps in an inline thank-you on success. Pair with a hidden honeypot
     input name="company_site" and hidden name="source"/"form". An optional
     [data-lead-note] element shows error text; [data-lead-thanks] overrides
     the success copy. */
  document.querySelectorAll('form[data-lead-form]').forEach(function(form){
    form.addEventListener('submit', function(e){
      e.preventDefault();
      var url  = form.getAttribute('action') || 'lead.php';
      var btn  = form.querySelector('[type=submit]');
      var note = form.querySelector('[data-lead-note]');
      if (note){ note.textContent = ''; note.classList.remove('is-err'); }
      function resetBtn(){ if (btn){ btn.disabled=false; btn.classList.remove('is-loading'); if(btn.dataset.orig!==undefined){ btn.innerHTML=btn.dataset.orig; } } }
      if (btn){
        btn.dataset.orig = btn.innerHTML;
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.innerHTML = '<span class="vtd-spinner" aria-hidden="true"></span> Sending…';
      }
      fetch(url, { method:'POST', body: new FormData(form), credentials:'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(res){
          if (res && res.ok){
            var msg = form.getAttribute('data-lead-thanks') || 'Thank you! We’ll be in touch within 1 business day.';
            form.innerHTML = '<div class="lead-thanks"><i class="fa-solid fa-circle-check"></i><p>' + msg + '</p></div>';
          } else {
            if (note){ note.textContent = (res && res.error) ? res.error : 'Something went wrong — please try again.'; note.classList.add('is-err'); }
            resetBtn();
          }
        })
        .catch(function(){
          if (note){ note.textContent = 'Network error — please try again.'; note.classList.add('is-err'); }
          resetBtn();
        });
    });
  });
})();
