require(['jquery', 'uikit!form-select', 'domReady!'], function($, uikit) {

    var form = $('.js-widget'), title = $('#form-title');

    // status handling
    var status   = $('input[name="widget[status]"]', form),
        statuses = $('.js-status', form).on('click', function(e){
            e.preventDefault();
            status.val(statuses.addClass('uk-hidden').not(this).removeClass('uk-hidden').data('status'));
        });

    if (status.val() === '') status.val(0);

    statuses.eq(status.val()).removeClass('uk-hidden');

    // show title checkbox
    var showtitleinput = $('input[name="widget[settings][show_title]"]', form),
        showtitle      = $('.js-title', form);

        showtitle.on("click", (function(e){
            var fn = function(){
                showtitleinput.val(showtitle.addClass('uk-hidden').not(this).removeClass('uk-hidden').data('value'));
            };

            showtitle.addClass('uk-hidden').filter('[data-value="'+showtitleinput.val()+'"]').removeClass('uk-hidden');

            return fn;
        })());

    // settings nav + remember active tab

    var sidenav = form.find('.uk-nav-side'),
        tabs    = sidenav.children();

    sidenav.on('uk.switcher.show', function(e, tab){
        sessionStorage['pk-widget-settings-'+form.data('id')] = tabs.index(tab);
    });

    if (form.data('id')!=='0') {
        tabs.eq(sessionStorage['pk-widget-settings-'+form.data('id')] || 0).find('a').trigger('click');
    }
});