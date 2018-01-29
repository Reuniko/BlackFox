$(function () {
    $('[data-datetimepicker]').flatpickr({
        locale: "ru",
        enableTime: true,
        dateFormat: "Y-m-d H:i:ss",
        allowInput: true,
    });
    $('[data-datepicker]').flatpickr({
        locale: "ru",
        enableTime: false,
        dateFormat: "Y-m-d",
        allowInput: true,
    });
});