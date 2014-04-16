require(['jquery', 'uikit', 'rowselect', 'domReady!'], function($, uikit, RowSelect) {

    var form         = $('#js-user'),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = $('.js-table-users').on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        rowselect    = new RowSelect(table),
        doaction     = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        };

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
});