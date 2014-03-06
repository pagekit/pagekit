require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    var form = $('#js-user'),
        doaction = function(element) {
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

    // select all checkbox
    form.on('click', '.js-select-all:checkbox', function() {
        $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
    });

});