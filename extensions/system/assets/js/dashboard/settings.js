require(['jquery', 'uikit!sortable', 'rowselect', 'domReady!'], function($, uikit, RowSelect) {

    var form         = $('#js-dashboard'),
        params       = form.data(),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = form.on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        rowselect    = new RowSelect(table, { 'rows': '.pk-table-fake' });

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();
        form.attr('action', $(this).data('action')).submit();
    })

        // select all checkbox
        .on('click', '.js-select-all',function() {
            form.find('.js-select').prop('checked', $(this).prop('checked'));
            rowselect.handleSelected();
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
});