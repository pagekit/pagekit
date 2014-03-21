define('linkpicker', ['jquery', 'require', 'tmpl!linkpicker.modal,linkpicker.replace', 'uikit', 'link'], function ($, req, tmpl, uikit, Link) {

    var LinkPicker = function (element, options) {

        options = $.extend({}, LinkPicker.defaults, options);

        var modal   = $(tmpl.get('linkpicker.modal')).appendTo('body'),
            picker  = new uikit.modal.Modal(modal),
            link    = new Link(modal.find('.js-linkpicker'), { typeFilter: options.typeFilter }),
            source  = $(element),
            trigger = $(tmpl.get('linkpicker.replace')).insertBefore(source);

        modal.on('submit', 'form', function (e) {
            e.preventDefault();

            picker.hide();
            source.val(link.getValue()).trigger('change');
        });

        trigger.on('click', function (e) {
            e.preventDefault();

            picker.show();
            link.init(source.val());
        });

        source
            .on('change', function () {
                if (!source.val()) {
                    source.trigger('resolved', '');
                    return;
                }

                var resolved = '';
                $.post(options.url, { url: source.val() },function (data) {

                    if (!data.url) {
                        source.val('');
                    }

                    if (!data.error && data.url) {
                        resolved = data.url;
                    }
                }, 'json').always(function () {
                    source.trigger('resolved', resolved);
                });
            })
            .on('resolved',function (e, resolved) {
                var $text = $('.js-picker-resolved', trigger);
                $text.text(resolved.length && source.val().length ? resolved : $text.data('text-empty'));
            }).trigger('change');
    };

    LinkPicker.defaults = {
        url       : req.toUrl('admin/system/resolveurl'),
        typeFilter: []
    };

    return LinkPicker;
});