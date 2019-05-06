$(function () {
	$('[data-datetimepicker]').flatpickr({
		locale: lang,
		dateFormat: "Y-m-d H:i:S",
		enableTime: true,
		time_24hr: true,
		allowInput: true
	});
	$('[data-datepicker]').flatpickr({
		locale: lang,
		enableTime: false,
		dateFormat: "Y-m-d",
		allowInput: true
	});

	if ($(window).width() < 768) {
		$('#sidebar').hide();
	}
	$(window).on('resize', function () {
		if ($(window).width() < 768) {
			$('#sidebar').hide();
		} else {
			$('#sidebar').show();
		}
	});

	$('[data-toggle-sidebar]').click(function () {
		$('#sidebar').toggle();
	})
});

