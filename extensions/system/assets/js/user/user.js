require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    var form  = $('#js-user'),
        table = $('.js-table-users'),
        doaction = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        },
        showOnSelect, lastselected, rows = table.find('tbody>tr');

    // selections
    showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden');

    form.on('click', '.js-select', function() {
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

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        var element = $(this);

        if (element.data("confirm")) {
            uikit.modal.confirm(element.data("confirm"), function() {
                doaction(element);
            });
        } else {
            doaction(element);
        }
    });

    // submit filters
    form.on('change', 'select[name^="filter"]', function() {
        form.submit();
    });

    // select all checkbox
    form.on('click', '.js-select-all:checkbox', function() {
        $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
        updateOnSelect();
    });

});