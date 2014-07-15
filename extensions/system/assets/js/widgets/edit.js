require(['jquery', 'uikit!form-select', 'domReady!'], function($, uikit) {

    var form = $('.js-widget'), title = $('#form-title'), sidenav = $('.uk-nav-side', sidenav);

    // settings nav + remember active tab
    var tabs = sidenav.children(), active;

    sidenav.on('uk.switcher.show', function(e, tab){
        active = tabs.index(tab);
    });

    form.on('submit', function() {
        sessionStorage['pk-widget-settings-tab'] = active;
    });

    tabs.eq(sessionStorage['pk-widget-settings-tab'] || 0).find('a').trigger('click');
    sessionStorage.removeItem('pk-widget-settings-tab');

    form.find(':input').bind('invalid', function() {

        var input = $(this);

        if (!input.is(':visible')) {
            sidenav.data('switcher').show(0);
        }
    });

});