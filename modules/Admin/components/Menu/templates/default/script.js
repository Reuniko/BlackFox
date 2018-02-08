$(function () {
	$('[data-menu-expander]').click(function () {
		$(this).closest('[data-menu-item]').children('[data-menu-children]').toggle();
		$(this).toggleClass('rotate-90').toggleClass('rotate-0');
	});
});