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
        var $clone = $(this).closest('[data-list]').find('[data-template]')
            .clone()
            .removeAttr('data-template')
            .attr('data-element', 'new')
            .css('display', 'table')
        ;
        $(this).before($clone);
    });

    $('[data-list]').on('click', '[data-delete]', function () {
        $(this).closest('[data-element]').remove();
    });

    $('[data-list]').sortable({
        items: '> [data-element]',
        axis: 'y',
        handle: '[data-sort]',
    });

});