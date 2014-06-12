require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    // switcher
    var tabs = $('[data-tabs]').on('uk.switcher.show', function(e, active) {
        $('input[name=tab]').val(active.prevAll().length);
    });

    new uikit.tab(tabs, { connect: '#tab-content', active: tabs.data('tabs') });
});