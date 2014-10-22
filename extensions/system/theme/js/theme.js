jQuery(function($) {

    var $doc = $(document), toolbars = $('.tm-toolbar'), bars = [];

    // adjust toolbar
    toolbars.each(function() {

        var toolbar = $(this);

        // ignore initialized toolbars and toolbars in modals
        if (toolbar.data('init') || toolbar.parents('.uk-modal:first').length) return;

        var offset  = toolbar.offset();

        bars.push((function check() {
            toolbar.css(window.scrollY > offset.top ? { position: 'fixed', top: 0} : { position: '', top: ''});
            return check;
        })());

        toolbar.data('init', true);
    });

    $doc.on('uk-scroll', (function onscroll() {
        bars.forEach(function(check) { check(); });
        return onscroll;
    })());

    // fix toolbar jumping when a modal is shown
    $doc.on({
        'uk.modal.show': function() {
            $('.tm-toolbar').css('padding-right', Math.abs(parseInt($('.uk-modal-page').css('margin-left'), 10)) );
        },
        'uk.modal.hide': function() {
            $('.tm-toolbar').css('padding-right', '');
        }
    });

    // show system messages
    $.UIkit.notify.message.defaults.timeout = 2000;

    $('.pk-system-messages').children().each(function() {
        var message = $(this);
        $.UIkit.notify(message.html(), message.data());
        message.remove();
    });

    // save current menu order
    $('.js-admin-menu').on('uk.sortable.stop', function() {

        var data = {};

        $(this).children().each(function(i) {
            data[$(this).data('id')] = i;
        });

        $.post($(this).data('url'), {'order': data}, function() {
            // message ?
        });
    });

});