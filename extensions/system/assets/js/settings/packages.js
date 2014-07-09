require(['jquery', 'uikit', 'system', 'domReady!'], function($, uikit, system) {

    var page = $('#js-extensions, #js-themes'), view = $('.js-installed', page);

    // install update
    view.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $(this).html('<i class="uk-icon-spinner uk-icon-spin"></i>');
        $.post($(this).data('action'), system.csrf.params, function(data) {
            uikit.notify(data.message, data.error ? 'danger' : 'success');
        }, 'json').always(function() {
            refreshTable();
        });

    });

    function refreshTable() {
        $.getJSON(view.data('url'), function(data) {
            view.html(data.table);
        });
    }
});
