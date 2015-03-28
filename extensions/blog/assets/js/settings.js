require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    // switcher
    var tabs = $('[data-tabs]').on('show.uk.switcher', function(e, active) {
        $('input[name=tab]').val(active.prevAll().length);
    });

    new uikit.tab(tabs, { connect: '#tab-content', active: tabs.data('tabs') });
});
