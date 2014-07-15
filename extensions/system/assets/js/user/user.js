require(['jquery', 'uikit', 'rowselect', 'gravatar', 'domReady!'], function($, uikit, RowSelect, gravatar) {

    var form         = $('#js-user'),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = $('.js-table-users').on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        doaction     = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        };

    new RowSelect(table);

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

    // show avatar
    $('img[data-avatar]', form).each(function() {
        $(this).attr('src', gravatar.url($(this).data('avatar'), {s: 80, d: 'mm', r: 'g'}));
    });

});