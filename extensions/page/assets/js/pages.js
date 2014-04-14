require(['jquery','uikit!pagination' , 'domReady!'], function($, uikit) {

    var form = $('#js-pages'), table = $('.js-table', form), pagination = $('[data-uk-pagination]', form), search, prev, showOnSelect, rows = table.find('tbody>tr'), lastselected;

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $.post($(this).data('action'), form.serialize(), function(data) {
            uikit.notify(data.message, data.error ? 'danger' : 'success');
            updateTable();
            updateOnSelect();
        });
    })

    // select all checkbox
    .on('click', '.js-select-all:checkbox', function() {
        $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
        updateOnSelect();
    })

    // submit filters
    .on('keyup', 'input[name^="filter"]', uikit.Utils.debounce(function() {
        updateTable();
    }, 200))
    .on('change', 'select[name^="filter"]', function() {
        updateTable();
    })
    .on('submit', function(e) {
        e.preventDefault();
    });

    // pagination
    pagination.on('uk-select-page', function(e, index) {
        $('[name="page"]', form).val(index);
        updateTable();
    });

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

    function updateTable() {
        $.post(form.data('action'), form.serialize(), function(data) {
            table.html(data.table);
            pagination.toggleClass('uk-hidden', data.total == 0).data('pagination').render(data.total);
            $('.uk-alert', form).toggleClass('uk-hidden', data.total > 0);

            rows = table.find('tbody>tr');
        });

        lastselected = false;
    }

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