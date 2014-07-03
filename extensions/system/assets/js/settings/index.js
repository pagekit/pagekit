require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    var modal;

    $('#clearCache').on('click', function(e) {
        e.preventDefault();

        if (!modal) {
            modal = uikit.modal('#modal-clearcache');

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
        }

        modal.show();
    });

});