require(['jquery', 'uikit!sortable', 'domReady!'], function($, uikit) {

    var form = $('#js-dashboard'), params = form.data(), rows = form.find('.pk-table-fake'), showOnSelect, lastselected;

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();
        form.attr('action', $(this).data('action')).submit();
    })

        // select all checkbox
        .on('click', '.js-select-all',function() {
            form.find('.js-select').prop('checked', $(this).prop('checked'));
            updateOnSelect();
        })
        .on('click', '.js-select', function() {
            form.find('.js-select-all').prop('checked', false);
        })

        // save widgets order on sortable change
        .on('sortable-change', 'ul.uk-sortable', function(e) {
            $.post(params.reorder, { order: $(this).data('uksortable').serialize(), _csrf: $('[name="_csrf"]').val() }, function(response) {
                uikit.notify(response.message || 'Widgets order updated', 'success');
            }).fail(function() {
                uikit.notify('Unable to reorder widgets.', 'danger');
            });
        });

    // selections
    showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden');

    form.on('click', '.js-select', function() {
        updateOnSelect();
    })
    // select via row clicking
    .on('click', '.pk-table-fake', function(e){

        var target = $(e.target), row = $(this), select;

        if(!target.is('a, input, [data-action]') && !target.closest('[data-action]').length) {

            if (e.shiftKey && window.getSelection) {
                window.getSelection()[window.getSelection().empty ? 'empty':'removeAllRanges']();
            }

            select = row.find('.js-select:first');

            if (select.length) {

                select.prop('checked', !select.prop('checked'));

                // shift select
                if (e.shiftKey && lastselected) {

                    var start = Math.min(row.index(), lastselected.index()), end = Math.max(rows.index(row), rows.index(lastselected));

                    for(i = start; i <= end; i++) {
                        rows.eq(i).find('.js-select:first').prop('checked', true);
                    }
                }

                if (!e.shiftKey && select.prop('checked')) {
                    lastselected = row;
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
        selected.closest('.pk-table-fake').addClass('pk-table-selected');

        if (!selected.length) {
            lastselected = false;
        }
    }

});