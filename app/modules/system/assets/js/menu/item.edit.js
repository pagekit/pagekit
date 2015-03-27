require(['jquery', 'system!linkpicker'], function($, system) {

    // source
    var form  = $('#js-item-edit'),
        link  = $('[name="item[link]"]', form),
        input = $('.js-url', form);

    // URL picker
    system.linkpicker(link, { context: 'system/menu' });

    link.on('change', function() {
        input.prop("checked", "checked");
    });
});