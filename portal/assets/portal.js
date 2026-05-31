(function () {
  // ── Mobile nav hamburger ────────────────────────────────────────────
  var ham   = document.getElementById('portalHamburger');
  var shell = document.getElementById('portalShell');
  if (ham && shell) {
    ham.addEventListener('click', function () {
      var open = shell.classList.toggle('nav-open');
      ham.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    // Collapse the menu after tapping a nav link (mobile).
    shell.querySelectorAll('.portal-nav-link, .portal-side-link').forEach(function (a) {
      a.addEventListener('click', function () {
        shell.classList.remove('nav-open');
        ham.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // ── Flash auto-dismiss ──────────────────────────────────────────────
  document.querySelectorAll('.portal-flash').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .3s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 350);
    }, 5000);
  });

  // ── Client-side table search + pagination + population counter ─────
  // Attaches to any <table data-paginate> by reading rows from <tbody>
  // and re-rendering pagination controls into siblings marked with
  // [data-list-toolbar] (top: search + counter) and [data-list-pager]
  // (bottom: page nav). Pure DOM filtering — no AJAX.
  function initPortalList(card) {
    // Accept either a <table data-paginate> (rows live in tbody>tr) OR a
    // generic <ul/ol/div data-paginate> with direct children as rows.
    var table = card.querySelector('table[data-paginate]');
    var listEl, rowSel, container;
    if (table) {
      container = table.querySelector('tbody');
      if (!container) return;
      listEl = container; rowSel = 'tr';
    } else {
      container = card.querySelector('[data-paginate]:not(table)');
      if (!container) return;
      listEl = container;
      // Pick the most common direct-child tag as the "row" element.
      var counts = {};
      Array.prototype.slice.call(container.children).forEach(function (c) {
        if (c.hasAttribute('data-empty')) return;
        var t = c.tagName.toLowerCase();
        counts[t] = (counts[t] || 0) + 1;
      });
      rowSel = '';
      var best = 0;
      Object.keys(counts).forEach(function (t) { if (counts[t] > best) { best = counts[t]; rowSel = t; } });
      if (!rowSel) return;
    }

    var allRows = Array.prototype.slice.call(listEl.querySelectorAll(':scope > ' + rowSel))
      .filter(function (r) { return !r.hasAttribute('data-empty'); });

    // Pre-compute lowercase search blobs for fast filtering.
    allRows.forEach(function (r) {
      r.setAttribute('data-search', (r.textContent || '').toLowerCase().replace(/\s+/g, ' ').trim());
    });

    var searchInput = card.querySelector('[data-list-search]');
    var pageSizeSel = card.querySelector('[data-list-pagesize]');
    var counter     = card.querySelector('[data-list-counter]');
    var pager       = card.querySelector('[data-list-pager]');

    var state = {
      query: '',
      pageSize: pageSizeSel ? parseInt(pageSizeSel.value, 10) || 25 : 25,
      page: 1,
      filtered: allRows.slice(),
    };

    function applyFilter() {
      var q = state.query.trim().toLowerCase();
      state.filtered = q === ''
        ? allRows.slice()
        : allRows.filter(function (r) { return r.getAttribute('data-search').indexOf(q) !== -1; });
      state.page = 1;
      render();
    }

    function render() {
      var total = allRows.length;
      var shown = state.filtered.length;
      var size  = state.pageSize > 0 ? state.pageSize : shown || 1;
      var pages = Math.max(1, Math.ceil(shown / size));
      if (state.page > pages) { state.page = pages; }
      var start = (state.page - 1) * size;
      var end   = Math.min(start + size, shown);

      // Hide all then show only the current slice.
      allRows.forEach(function (r) { r.style.display = 'none'; });
      for (var i = start; i < end; i++) { state.filtered[i].style.display = ''; }

      // Empty-state row (works for both table-row and non-table containers).
      var emptyEl = listEl.querySelector(':scope > [data-empty]');
      if (shown === 0) {
        if (!emptyEl) {
          if (table) {
            emptyEl = document.createElement('tr');
            emptyEl.setAttribute('data-empty', '');
            var cols = (table.tHead && table.tHead.rows[0] ? table.tHead.rows[0].cells.length : 1);
            emptyEl.innerHTML = '<td colspan="' + cols + '" class="muted" style="text-align:center;padding:24px;">No results match your search.</td>';
          } else {
            emptyEl = document.createElement(rowSel);
            emptyEl.setAttribute('data-empty', '');
            emptyEl.className = 'muted';
            emptyEl.style.padding = '24px';
            emptyEl.style.textAlign = 'center';
            emptyEl.textContent = 'No results match your search.';
          }
          listEl.appendChild(emptyEl);
        }
        emptyEl.style.display = '';
      } else if (emptyEl) {
        emptyEl.style.display = 'none';
      }

      // Counter.
      if (counter) {
        if (shown === 0) {
          counter.textContent = '0 of ' + total + ' shown';
        } else {
          var prefix = (start + 1) + '–' + end + ' of ' + shown;
          counter.textContent = (shown !== total)
            ? (prefix + ' (filtered from ' + total + ')')
            : (prefix + ' total');
        }
      }

      // Pager.
      if (pager) {
        pager.innerHTML = '';
        if (pages > 1) {
          var prev = pagerBtn('« Prev', state.page > 1, function () { state.page--; render(); });
          pager.appendChild(prev);

          // Compact page-number list: first, last, neighbours of current.
          var nums = pageRange(state.page, pages);
          var lastShown = 0;
          nums.forEach(function (n) {
            if (n - lastShown > 1) {
              var dots = document.createElement('span');
              dots.className = 'pager-dots';
              dots.textContent = '…';
              pager.appendChild(dots);
            }
            var b = pagerBtn(String(n), true, function () { state.page = n; render(); });
            if (n === state.page) { b.classList.add('is-active'); }
            pager.appendChild(b);
            lastShown = n;
          });

          var next = pagerBtn('Next »', state.page < pages, function () { state.page++; render(); });
          pager.appendChild(next);
        }
      }
    }

    function pagerBtn(label, enabled, onClick) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'pager-btn';
      b.textContent = label;
      if (!enabled) { b.disabled = true; }
      else { b.addEventListener('click', onClick); }
      return b;
    }

    function pageRange(cur, pages) {
      var out = new Set();
      out.add(1); out.add(pages);
      for (var d = -2; d <= 2; d++) {
        var n = cur + d;
        if (n >= 1 && n <= pages) { out.add(n); }
      }
      return Array.from(out).sort(function (a, b) { return a - b; });
    }

    if (searchInput) {
      var timer = null;
      searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { state.query = searchInput.value; applyFilter(); }, 120);
      });
      // Submitting the wrapping <form> would reload the page — short-circuit.
      var form = searchInput.closest('form');
      if (form) { form.addEventListener('submit', function (e) { e.preventDefault(); state.query = searchInput.value; applyFilter(); }); }
    }
    if (pageSizeSel) {
      pageSizeSel.addEventListener('change', function () {
        state.pageSize = parseInt(pageSizeSel.value, 10) || 25;
        state.page = 1;
        render();
      });
    }

    render();
  }

  document.querySelectorAll('[data-list]').forEach(initPortalList);
})();
