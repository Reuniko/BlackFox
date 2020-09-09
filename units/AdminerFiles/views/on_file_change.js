$(function () {
    $(document).on('change', ':file', function () {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        $(this).siblings('span').text(label);
    });
});