$(function () {

	$('[data-tooltip]').each(function (e, i) {
		$(this).tooltip({
			title: $(this).data('tooltip'),
			placement: $(this).data('tooltip-placement')
		});
	});



});