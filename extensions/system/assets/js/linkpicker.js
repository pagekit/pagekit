define('linkpicker', ['jquery', 'require', 'tmpl!linkpicker.modal,linkpicker.replace', 'uikit', 'link'], function ($, req, tmpl, uikit, Link) {

    var LinkPicker = function (element, options) {

        options = $.extend({}, LinkPicker.defaults, options);

        var source  = $(element),
            modal   = $(tmpl.get('linkpicker.modal')).appendTo('body'),
            picker  = uikit.modal(modal),
            trigger = $(tmpl.get('linkpicker.replace')).insertAfter(source),
            link;

        modal.on('submit', 'form', function (e) {
            e.preventDefault();

            picker.hide();
            source.val(link.get()).trigger('change');
        });

        trigger.on('click', function (e) {
            e.preventDefault();

            link = Link.attach(modal.find('.js-linkpicker'), { filter: options.filter, context: options.context, value: source.val() });
            picker.show();
        });

        source
            .on('change', function () {
                var text = $('.js-picker-resolved', trigger);
                text.text(source.val().length ? source.val() : text.data('text-empty'));
            })
            .trigger('change');
    };

    LinkPicker.defaults = {
        filter   : [],
        context  : '',
        textEmpty: 'Choose Link'
    };

    return LinkPicker;
});