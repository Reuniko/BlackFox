$(function () {
	$('[data-menu-expander]').click(function () {
		$(this).closest('[data-menu-item]').find('[data-menu-children]').toggle();
		$(this).toggleClass('fa-rotate-90');
	});
});