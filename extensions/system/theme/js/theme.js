jQuery(function($) {

    $('.pk-system-messages').hide().children().each(function() {
        var message = $(this);
        $.UIkit.notify(message.html(), message.data());
        message.remove();
    });

});