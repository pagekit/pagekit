require(['jquery', 'uikit!form-select', 'domReady!'], function($, uikit) {

    var form = $('.js-widget'), title = $('#form-title');

    // status handling
    var status   = $('input[name="widget[status]"]', form),
        statuses = $('.js-status', form).on('click', function() {
            status.val(statuses.addClass('uk-hidden').not(this).removeClass('uk-hidden').data('value'));
        });

    // show title checkbox
    var showtitleinput = $('input[name="widget[settings][show_title]"]', form),
        showtitle      = $('.js-title', form).on('click', function() {
            showtitleinput.val(showtitle.addClass('uk-hidden').not(this).removeClass('uk-hidden').data('value'));
        });

    // settings nav + remember active tab
    var sidenav = form.find('.uk-nav-side'),
        tabs    = sidenav.children(), active;

    sidenav.on('uk.switcher.show', function(e, tab){
        active = tabs.index(tab);
    });

    form.on('submit', function() {
        sessionStorage['pk-widget-settings-tab'] = active;
    });

    tabs.eq(sessionStorage['pk-widget-settings-tab'] || 0).find('a').trigger('click');
    sessionStorage.removeItem('pk-widget-settings-tab');

    form.find(':input').bind('invalid', function(e) {

        var input = $(this);

        if (!input.is(':visible')) {
            sidenav.data('switcher').show(0);
        }
    });
});