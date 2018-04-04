$(function () {

	/* COMMON */

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

	/* TYPE LIST */

	$('[data-list]').on('click', '[data-add]', function () {
		var $container = $(this).closest('[data-list]');
		var $clone = $container.find('[data-template]')
				.clone()
				.removeAttr('data-template')
				.attr('data-element', 'new')
				.find('input').removeAttr('disabled').end()
				.appendTo($container)
				.show()
			;
	});

	$('[data-list]').on('click', '[data-delete]', function () {
		$(this).closest('[data-element]').remove();
	});

	$('[data-list]').sortable({
		items: '> [data-element]',
		axis: 'y',
		handle: '[data-sort]',
	});

	$('[data-settings-select]').on('click', function () {
		$('#' + $(this).data('settings-select')).find('[type=checkbox]').prop('checked', true);
	});

	$('[data-settings-unselect]').on('click', function () {
		$('#' + $(this).data('settings-unselect')).find('[type=checkbox]').prop('checked', false);
	});

	$('[data-settings-sort]').on('click', function () {
		var $container = $('#' + $(this).data('settings-sort'));
		for (i = 0; i < $container.find('li').length; i++) {
			$container.find('[data-order=' + i + ']').insertAfter(
				$container.find('[data-order=' + (i - 1) + ']')
			);
		}
	});

	$('[data-wysiwyg]').each(function () {
		CKEDITOR.config.allowedContent = true;
		CKEDITOR.replace($(this).attr('id'));
	});

	$('[data-confirm]').click(function (e) {
		if (!confirm($(this).data('confirm'))) {
			e.preventDefault();
			return false;
		}
	})

});