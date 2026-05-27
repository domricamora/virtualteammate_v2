(function () {
  // Auto-dismiss flash messages after 5s.
  document.querySelectorAll('.portal-flash').forEach(function (el) {
    setTimeout(function () { el.style.transition = 'opacity .3s ease'; el.style.opacity = '0'; setTimeout(function(){ el.remove(); }, 350); }, 5000);
  });
})();
