require(['jquery', 'domReady!'], function($) {

    var form         = $('#js-aliases'),
        table        = $('.js-table'),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        rows         = table.find('tbody>tr'), lastselected;


    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();
        form.attr('action', $(this).data('action')).submit();
    })

    // select all checkbox
    .on('click', '.js-select-all:checkbox', function() {
        $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
        updateOnSelect();
    })

    .on('click', '.js-select', function() {
        updateOnSelect();
    })

    // select via row clicking
    .on('click', 'tr', function(e){

        var target = $(e.target), tr = $(this), select;

        if(!target.is('a, input, [data-action]') && !target.closest('[data-action]').length) {

            if (e.shiftKey && window.getSelection) {
                window.getSelection()[window.getSelection().empty ? 'empty':'removeAllRanges']();
            }

            select = tr.find('.js-select:first');

            if (select.length) {

                select.prop('checked', !select.prop('checked'));

                // shift select
                if (e.shiftKey && lastselected) {

                    var start = Math.min(tr.index(), lastselected.index()), end = Math.max(tr.index(), lastselected.index());

                    for(i = start; i <= end; i++) {
                        rows.eq(i).find('.js-select:first').prop('checked', true);
                    }
                }

                if (!e.shiftKey && select.prop('checked')) {
                    lastselected = tr;
                } else {
                    lastselected = false;
                }

                updateOnSelect();
            }
        }
    });

    function updateOnSelect() {
        var selected = form.find('.js-select:checked');
        showOnSelect[selected.length ? 'removeClass':'addClass']('uk-hidden');

        rows.removeClass('pk-table-selected');
        selected.closest('tr').addClass('pk-table-selected');

        if (!selected.length) {
            lastselected = false;
        }
    }

});