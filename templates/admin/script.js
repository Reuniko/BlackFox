$(function () {
	$('[data-datetimepicker]').flatpickr({
		locale: "ru",
		dateFormat: "Y-m-d H:i:S",
		enableTime: true,
		time_24hr: true,
		allowInput: true
	});
	$('[data-datepicker]').flatpickr({
		locale: "ru",
		enableTime: false,
		dateFormat: "Y-m-d",
		allowInput: true
	});

	if ($(window).width() < 576) {
		$('#sidebar').hide();
	}

	$('[data-toggle-sidebar]').click(function () {
		$('#sidebar').toggle();
	})
});

