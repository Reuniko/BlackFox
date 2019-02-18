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
    });

    // визуальное автодополнение выбора файлов
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
    // развертывание выбора файла при его удалении
    $(document).on('change', '[data-file-delete]', function (e) {
        if ($(this).prop('checked')) {
            $(this).closest('[data-file]').find('[data-file-selector]').slideDown();
        } else {
            $(this).closest('[data-file]').find('[data-file-selector]').slideUp();
        }
    });

    /* TYPE OUTER */
    $('[data-type=OUTER]').select2({
        theme: "bootstrap",
        language: "ru",
        width: 'auto',
        ajax: {
            url: window.location.href,
            dataType: 'json',
            data: function (params) {
                return {
                    ACTION: 'SearchOuter',
                    code: $(this).data('code'),
                    search: params.term,
                    page: params.page || 1,
                };
            }
        }
    });

    $('[data-type=OUTER]').on('select2:select', function (e) {
        var data = e.params.data;
        var link = $(this).closest('[data-outer]').find('[data-outer-link]');
        link.text('№' + data.id);
        link.attr('href', data.link);
    });

    $('[data-outer-clean]').click(function () {
        $(this)
            .closest('[data-outer]')
            .find('select')
            .val(null)
            .trigger('change')
        ;
        $(this)
            .closest('[data-outer]')
            .find('[data-outer-link]')
            .text('...')
            .attr('href', 'javascript:void(0);')
        ;
    });

    $('[data-outer-multiple]').click(function () {
        $(this)
            .closest('[data-outer]')
            .removeClass('d-flex')
            .addClass('d-none')

            .siblings('[data-outer-multiple]')
            .removeClass('d-none')

            .find('select')
            .prop('disabled', false)
        ;
    });


});