jQuery(function($) {

    // switcher
    var tabs = $('[data-tabs]').on('show.uk.switcher', function(e, active) {
        $('input[name=tab]').val(active.prevAll().length);
    });

    UIkit.tab(tabs, { connect: '#tab-content', active: tabs.data('tabs') });

});
