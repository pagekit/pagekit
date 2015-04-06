jQuery(function($) {

    // show system messages
    UIkit.notify.message.defaults.timeout = 2000;

    $('.pk-system-messages').children().each(function() {
        var message = $(this);
        UIkit.notify(message.html(), message.data());
        message.remove();
    });

    // save current menu order
    $('.js-menu').on('stop.uk.sortable', function() {

        var data = {};

        $(this).children().each(function(i) {
            data[$(this).data('id')] = i;
        });

        $.post($(this).data('url'), {'order': data}, function() {
            // message ?
        });
    });

});