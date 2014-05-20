jQuery(function($) {

    $.UIkit.notify.message.defaults.timeout = 2000;

    $('.pk-system-messages').children().each(function() {
        var message = $(this);
        $.UIkit.notify(message.html(), message.data());
        message.remove();
    });

    $('.pk-options').each(function() {

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

    // always fit navbar
    $(window).on('resize load', (function(){

        var navbar = $('.tm-navbar .uk-navbar:first'),
            menu   = navbar.find('.uk-navbar-nav:first'),
            links  = menu.children(),
            more   = $([
                        '<li class="uk-hidden" data-uk-dropdown>',
                            '<a><i class="uk-icon-plus"></i></a>',
                            '<div class="uk-dropdown uk-dropdown-flip uk-dropdown-navbar"><ul class="uk-nav uk-nav-navbar"></ul></div>',
                        '</li>'
                       ].join('')).appendTo(menu),
            list   = more.find('ul'),

            check  = function(children) {

                for (var i=0;i<children.length;i++) {

                    if (children.eq(i).position().top > 0) {
                        return false;
                    }
                }

                return true;
            },

            respfn = function(){

                if(!navbar.is(':visible')) return;

                more.addClass('uk-hidden');
                links.removeClass("uk-hidden");

                var children = navbar.children(':visible');

                if (!check(children)) {

                    list.empty();
                    more.removeClass('uk-hidden');
                    links.removeClass('uk-hidden');

                    for (var i = links.length -1 ; i > -1; i--) {
                        list.prepend(links[i].outerHTML);
                        links.eq(i).addClass('uk-hidden');

                        if (check(children)) {
                            break;
                        }
                    }
                }
            };

        respfn();

        return $.UIkit.Utils.debounce(respfn, 10);

    })());

});