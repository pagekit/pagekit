require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    $('#clearCache').on('click', function(e) {
        e.preventDefault();

        modal = new uikit.modal.Modal('#modal-clearcache');

        modal.element.find('form').on('submit', function(e) {
            e.preventDefault();

            $.post($(this).attr('action'), $(this).serialize(),function(data) {
                uikit.notify(data.message, 'success');
            }).fail(function() {
                uikit.notify('Clearing cache failed.', 'danger');
            }).always(function() {
                modal.hide();
            });
        });

        modal.show();
    });

});