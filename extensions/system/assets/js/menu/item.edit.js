require(['jquery', 'system!linkpicker'], function($, system) {

    // source
    var link = $('.js-item-url'),
        form = link.closest('form'),
        nolink = $('[data-msg="no-link"]', form);

    // URL picker
    system.linkpicker(link, { context: 'system/menu' });

    form.on('submit', function() {
        if (!link.val()) {
            nolink.removeClass('uk-hidden');
            return false;
        }
    }).on('change', link, function() {
        nolink.addClass('uk-hidden');
        $('[name="item[url]"]:first').val(link.val());
    });
});