$(function () {
	$('[data-menu-category]').click(function () {
		$(this).siblings('[data-menu-children]').slideToggle();
	});
});