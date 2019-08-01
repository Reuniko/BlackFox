$(function () {

    // visual auto-completion of file selection
    $(document).on('change', '[data-file] input:file', function (e) {
        var filename = $(this).val();
        if (filename.substring(3, 11) == 'fakepath') {
            filename = filename.substring(12);
        }
        $(this)
            .closest('[data-file]')
            .find('[data-file-name]')
            .text(filename);
    });

    // expand file selection when deleting it
    $(document).on('change', '[data-file-delete]', function (e) {
        if ($(this).prop('checked')) {
            $(this).closest('[data-file]')
                .find('[data-file-selector]').slideDown()
                .find('input:file').prop('disabled', false)
            ;
        } else {
            $(this).closest('[data-file]')
                .find('[data-file-selector]').slideUp()
                .find('input:file').prop('disabled', true)
            ;
        }
    });

});