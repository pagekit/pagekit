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
                if (!source.val()) {
                    source.trigger('resolved', '');
                    return;
                }

                var resolved = '';
                $.post(options.url, { link: source.val() },function (data) {

                    resolved = data.url ? decodeURIComponent(data.url) : source.val();

                }, 'json').always(function () {
                    source.trigger('resolved', resolved);
                });
            })
            .on('resolved',function (e, resolved) {
                var text = $('.js-picker-resolved', trigger);
                text.text(resolved.length && source.val().length ? resolved : text.data('text-empty'));
            }).trigger('change');
    };

    LinkPicker.defaults = {
        url    : req.toUrl('index.php/admin/system/link/resolve'),
        filter : [],
        context: '',
        textEmpty: 'Choose Link'
    };

    return LinkPicker;
});