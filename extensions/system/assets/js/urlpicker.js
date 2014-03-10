define(['jquery', 'require','tmpl!urlpicker.modal', 'uikit', 'link'], function($, req, tmpl, UI, Link) {

    var UrlPicker = function(element, options) {

        var $this = this;

        this.options = $.extend({}, UrlPicker.defaults, options);

        var modal  = $(tmpl.get('urlpicker.modal')).appendTo('body'),
            picker = new UI.modal.Modal(modal),
            link   = new Link({ typeFilter: ['/'] });

        this.url    = modal.find('.js-link-url');
        this.source = $(element);

        modal.on('click', '.js-update', function () {
            picker.hide();
            $this.source.val($this.url.val());
            $this.resolve();
        });

        $this.source.on('click', function() {
            picker.show();
            $this.url.val($this.source.val());
            setTimeout(function() { $this.url.focus(); }, 10);
        });

    };

    $.extend(UrlPicker.prototype, {

        resolve: function() {
            var $this = this, resolved = '';

            if (!this.source.val().match(/^@/)) {
                $this.source.trigger('resolved', '');
                return;
            }

            $.post(this.options.url, { url: this.source.val() }, function(data) {
                if (!data.error && data.url) {
                    resolved = data.url;
                }
            }, 'json')
            .always(function() {
                $this.source.trigger('resolved', resolved);
            });
        }

    });

    UrlPicker.defaults = {
        url: req.toUrl('admin/system/resolveurl')
    };

    return UrlPicker;
});