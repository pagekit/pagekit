document.addEventListener("DOMContentLoaded", function(event) {

    (function($, $doc){

        // adjust toolbar

        $doc.on('uk-domready', (function() {

            var navbar = $('.tm-navbar'), bars = [], fn;

            document.body.style.visibility = 'hidden';

            fn = function() {

                var toolbars = $('.pk-toolbar:not([toolbar-init])');

                if (toolbars.length && !navbar.hasClass('tm-navbar-margin')) {
                    navbar.addClass('tm-navbar-margin');
                }

                toolbars.each(function() {

                    var ele = $(this);

                    // ignore toolbars in modals
                    if (ele.parents('.uk-modal:first').length) return;

                    var toolbar = ele.addClass('uk-container uk-container-center').wrap('<div class="tm-toolbar">').parent(), offset  = toolbar.offset();

                    bars.push((function(){

                        var check = function(){
                            return toolbar.css(window.scrollY > offset.top ? {'position': 'fixed', 'top':0} : {'position': '', 'top':''}) ? check : check;
                        };
                        return check();
                    })());

                    ele.attr('toolbar-init', 'true');
                });

                return fn;
            };

            document.body.style.visibility = '';

            $doc.on('uk-scroll', (function(){

                var onscroll = function(){
                    return bars.forEach(function(check){ check(); }) ? onscroll : onscroll;
                };

                return onscroll();
            }));

            // fix toolbar jumping when a modal is shown
            $doc.on({
                'uk.modal.show': function() {
                    $('.tm-toolbar').css('padding-right', $('.uk-modal-page').css('padding-right'));
                },
                'uk.modal.hide': function() {
                    $('.tm-toolbar').css('padding-right', '');
                }
            });

            return fn();

        })());

        // show system messages

        $.UIkit.notify.message.defaults.timeout = 2000;

        $('.pk-system-messages').children().each(function() {
            var message = $(this);
            $.UIkit.notify(message.html(), message.data());
            message.remove();
        });


        // save current menu order

        $('.js-admin-menu').on('sortable-stop', function() {

            var data = {};

            $(this).children().each(function(i) {
                data[$(this).data('id')] = i;
            });

            $.post($(this).data('url'), {'order': data}, function() {
                // message ?
            });
        });

    })(jQuery, jQuery(document));
});