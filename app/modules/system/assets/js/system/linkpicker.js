define(['jquery', 'tmpl!linkpicker.modal,linkpicker.replace', 'uikit', 'system!link'], function ($, tmpl, uikit, system) {

    var LinkPicker = function (element, options) {

        options = $.extend({}, LinkPicker.defaults, options);

        var source  = $(element),
            modal   = $(tmpl.get('linkpicker.modal')).appendTo('body'),
            picker  = uikit.modal(modal),
            replace = $('<span>').insertAfter(source), link;

        modal.on('submit', 'form', function (e) {
            e.preventDefault();

            picker.hide();
            source.val(link.get()).trigger('change');
        });

        source.parent().on('click', '.js-linkpicker-trigger', function (e) {
            e.preventDefault();

            link = system.link(modal.find('.js-linkpicker'), { context: options.context, value: source.val() });
            picker.show();
        });

        source
            .on('change', function () {

                if (!source.val()) {
                    preview(null);
                    return;
                }

                $.post(options.url + '/resolve', { link: source.val(), context: options.context },function (data) {

                    preview(data);

                }, 'json').fail(function() {
                    preview(null);
                });

            }).trigger('change');

        function preview(data) {
            replace = $(tmpl.render('linkpicker.replace', data)).replaceAll(replace);
        }
    };

    LinkPicker.defaults = {
        url    : system.config.link,
        context: ''
    };

    system.linkpicker = function(element, options) {
        return new LinkPicker(element, options);
    };

    return system;
});