$(function () {
	$('[data-menu-category]').click(function () {
		$(this).siblings('[data-menu-children]').slideToggle();
		$(this).find('[data-menu-rotator]').toggleClass('fa-rotate-90');
	});
});