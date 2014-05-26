jQuery(function($) {

    $.UIkit.notify.message.defaults.timeout = 2000;

    $('.pk-system-messages').children().each(function() {
        var message = $(this);
        $.UIkit.notify(message.html(), message.data());
        message.remove();
    });

    $(document).on('uk-domready', (function() {

        var i = 0;

        var fn = function() {

            $('.pk-toolbar:not([toolbar-init])').each(function() {

                var ele = $(this);

                // ignore toolbars in modals
                if (ele.parents('.uk-modal:first').length) {
                    return;
                }

                $('.tm-navbar').addClass('tm-navbar-margin');

                var toolbar = ele.attr('toolbar-init', 'true').addClass('uk-container uk-container-center').wrap('<div class="tm-toolbar">').parent(),
                    offset  = toolbar.offset();

                $(document).on('uk-scroll', (function(){

                    var fn = function(){
                        toolbar.css(window.scrollY > offset.top ? {'position': 'fixed', 'top':0} : {'position': '', 'top':''});
                    };

                    fn();

                    return fn;
                }));
            });

        };

        fn();

        return fn;

    })());



    $('.js-admin-menu').on('sortable-stop', function() {

        var data = {};

        $(this).children().each(function(i) {
            data[$(this).data('id')] = i;
        });

        $.post($(this).data('url'), {'order': data}, function() {
            // message ?
        });
    });

});
