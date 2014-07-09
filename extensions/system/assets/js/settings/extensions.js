require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    var page = $('#js-extensions'), view = $('.js-installed', page);

    // install update
    view.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $(this).html('<i class="uk-icon-spinner uk-icon-spin"></i>');
        $.getJSON($(this).data('action'), function(data) {
            uikit.notify(data.message, data.error ? 'danger' : 'success');
        }).always(function() {
            refreshTable();
        });

    });

    function refreshTable() {
        $.getJSON(view.data('url'), function(data) {
            view.html(data.table);
        });
    }
});
