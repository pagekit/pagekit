require(['jquery', 'linkpicker'], function($, Picker) {

    // source
    var link = $('.js-item-url'),
        form = link.closest('form'),
        nolink = $('[data-msg="no-link"]', form);

    // URL picker
    new Picker(link, { context: 'system/menu' });

    form.on('submit', function() {
        if (!link.val()) {
            nolink.removeClass('uk-hidden');
            return false;
        }
    }).on('change', link, function() {
        nolink.addClass('uk-hidden');
        $('[name="item[url]"]:last').val(link.val());
    });
});