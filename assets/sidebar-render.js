// Renders #sidebar from window.BLACKFOX_SIDEBAR (set by /en/sidebar.js or
// /ru/sidebar.js, loaded before this script). Builds the same DOM shape the
// original BlackFox\Menu unit produced server-side (see BlackFox/units/Menu/
// views/Default.php), so menu.css applies unchanged. Also figures out which
// item is "current" by matching the end of location.pathname against each
// item's href - same idea as the original SearchActiveItemsRecursive(), just
// done in the browser instead of PHP.
//
// Each page sets window.SIDEBAR_BASE to the relative path back to its own
// language root before loading this script - "./" for /en/index.html,
// "../" for /en/classes/scrud.html, etc. Rendered links are that base +
// the href from the data file, so this works at any folder depth.
(function () {
	var container = document.getElementById('sidebar');
	var data = window.BLACKFOX_SIDEBAR;
	var base = window.SIDEBAR_BASE || './';
	if (!container || !data) return;

	var here = location.pathname;

	function isCurrent(href) {
		return href && here.slice(-href.length) === href;
	}

	function buildItem(item, level) {
		var hasChildren = Array.isArray(item.children) && item.children.length > 0;
		var current = isCurrent(item.href);
		var expand = true; // categories are always expanded, no collapse/toggle

		var li = document.createElement('li');
		li.setAttribute('data-menu-item', '');

		var div = document.createElement('div');
		div.className = 'item level-' + level + (current ? ' current' : '');

		var i = document.createElement('i');
		i.className = hasChildren
			? 'menu-point menu-point-category ' + (expand ? 'rotate-90' : 'rotate-0')
			: 'menu-point menu-point-item';
		div.appendChild(i);

		if (item.href && !hasChildren) {
			var a = document.createElement('a');
			a.href = base + item.href;
			a.textContent = item.name;
			div.appendChild(a);
		} else {
			var span = document.createElement('span');
			span.textContent = item.name;
			div.appendChild(span);
		}

		li.appendChild(div);

		if (hasChildren) {
			var ul = document.createElement('ul');
			ul.className = 'level-' + (level + 1) + (expand ? '' : ' collapse');
			item.children.forEach(function (child) {
				ul.appendChild(buildItem(child, level + 1));
			});
			li.appendChild(ul);
		}

		return li;
	}

	var rootUl = document.createElement('ul');
	rootUl.className = 'menu';
	data.forEach(function (category) {
		rootUl.appendChild(buildItem(category, 1));
	});

	container.innerHTML = '';
	container.appendChild(rootUl);
})();
