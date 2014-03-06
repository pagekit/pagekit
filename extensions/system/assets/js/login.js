require(['jquery', 'domReady!'], function($) {

    var login = $('.js-login'), messages = $('.pk-system-messages').children();

    if (messages.length) {
        login.addClass('uk-animation-shake').find('input:password').focus();
    }

});