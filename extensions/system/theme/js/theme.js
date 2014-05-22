jQuery(function($) {

    $.UIkit.notify.message.defaults.timeout = 2000;

    $('.pk-system-messages').children().each(function() {
        var message = $(this);
        $.UIkit.notify(message.html(), message.data());
        message.remove();
    });

    $('.pk-toolbar').each(function() {

        $('.tm-navbar').addClass('tm-navbar-margin');

        var toolbar = $(this).addClass('uk-container uk-container-center').wrap('<div class="tm-toolbar">').parent(),
            offset  = toolbar.offset();

        $(window).on('scroll', (function(){

            var fn = function(){
                toolbar.css(window.scrollY > offset.top ? {'position': 'fixed', 'top':0} : {'position': '', 'top':''});
            };

            fn();

            return fn;
        }));
    });

    var list = $('.js-admin-menu'),
        url  = list.data('url'),
        data, order, links;

    list.on('sortable-stop', function() {

        order = 0;
        links = list.children();
        data  = {};

        links.each(function(){
            var item = $(this);
            data[item.data('id')] = order;
        });

        $.post(url, {'order': data}, function(){
            // message?
        });
    });

});