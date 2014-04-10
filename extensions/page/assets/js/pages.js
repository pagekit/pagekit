require(['jquery', 'domReady!'], function($) {

    var form = $('#js-pages');

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();
        form.attr('action', $(this).data('action')).submit();
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