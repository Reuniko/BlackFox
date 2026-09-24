// Vanilla-JS port of docs/templates/docs/script.js (which used jQuery) -
// same behavior, no jQuery dependency.
document.addEventListener('DOMContentLoaded', function () {
	var sidebar = document.getElementById('sidebar');

	function syncSidebar() {
		if (!sidebar) return;
		sidebar.style.display = window.innerWidth < 768 ? 'none' : '';
	}
	syncSidebar();
	window.addEventListener('resize', syncSidebar);

	document.querySelectorAll('[data-toggle-sidebar]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (!sidebar) return;
			sidebar.style.display = (sidebar.style.display === 'none') ? '' : 'none';
		});
	});

	document.querySelectorAll('hint').forEach(function (hint) {
		var rel = hint.getAttribute('rel');
		if (!rel) return;
		var targets = document.querySelectorAll(rel);
		hint.addEventListener('mouseover', function () {
			targets.forEach(function (t) { t.classList.add('highlight'); });
		});
		hint.addEventListener('mouseout', function () {
			targets.forEach(function (t) { t.classList.remove('highlight'); });
		});
	});
});
