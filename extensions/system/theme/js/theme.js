require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    $('.pk-system-messages').children().each(function() {
        var message = $(this);
        uikit.notify(message.html(), message.data());
        message.remove();
    });

});