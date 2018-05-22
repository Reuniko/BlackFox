$(function () {
	$('[data-menu-expander]').click(function () {
		$(this).closest('[data-menu-item]').children('[data-menu-children]').toggle();
		$(this).closest('[data-menu-item-body]').find('[data-menu-rotate]').toggleClass('rotate-90').toggleClass('rotate-0');
	});
});