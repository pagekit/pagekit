require(['jquery', 'rowselect', 'domReady!'], function($, RowSelect) {

    var form         = $('#js-aliases'),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = $('.js-table').on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        rowselect    = new RowSelect(table);


    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();
        form.attr('action', $(this).data('action')).submit();
    })

    // select all checkbox
    .on('click', '.js-select-all:checkbox', function() {
        $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
        rowselect.handleSelected();
    });

});