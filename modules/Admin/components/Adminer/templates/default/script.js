$(function () {

	$('[data-tooltip]').each(function (e, i) {
		$(this).tooltip({
			title: $(this).data('tooltip'),
			placement: $(this).data('tooltip-placement')
		});
	});

	$('[data-connected-sortable]').each(function (i, e) {
		$(this).sortable({
			connectWith: "[data-connected-sortable=" + $(this).data('connected-sortable') + "]",
			placeholder: 'sortable-placeholder',
		});
		$(this).disableSelection();
	});

});