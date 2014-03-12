define('urlpicker', ['jquery', 'require','tmpl!urlpicker.modal,urlpicker.replace', 'uikit', 'link'], function($, req, tmpl, uikit, Link) {

    var UrlPicker = function(element, options) {

        var $this = this;

        this.options = $.extend({}, UrlPicker.defaults, options);

        var modal   = $(tmpl.get('urlpicker.modal')).appendTo('body'),
            picker  = new uikit.modal.Modal(modal),
            link    = new Link({ typeFilter: this.options.typeFilter }),
            url     = modal.find('.js-link-url'),
            source  = $(element),
            trigger = $(tmpl.get('urlpicker.replace')).insertBefore(source);

        modal.on('submit', 'form', function(e) {
            e.preventDefault();

            picker.hide();
            source.val(url.val()).trigger('change');
            resolve();
        });

        trigger.on('click', function(e) {
            e.preventDefault();

            url.val(source.val()).trigger('change');
            picker.show();
        });

        source
            .on('change', function() {
               resolve();
            })
            .on('resolved', function(e, resolved) {
                var $text = $('.js-picker-resolved', trigger);
                $text.text(resolved.length && source.val().length ? resolved : $text.data('text-empty'));
            }).trigger('change');

        function resolve() {
            var resolved = '';

            $.post($this.options.url, { url: source.val() }, function(data) {
                if (!data.error && data.url) {
                    resolved = data.url;
                }
            }, 'json').always(function() {
                source.trigger('resolved', resolved);
            });
        }

    };

    UrlPicker.defaults = {
        url: req.toUrl('admin/system/resolveurl'),
        typeFilter: ['/']
    };

    return UrlPicker;
});