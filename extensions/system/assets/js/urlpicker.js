define(['jquery', 'require','tmpl!urlpicker.modal', 'uikit', 'link'], function($, req, tmpl, UI, Link) {

    var UrlPicker = function(element, options) {

        var $this = this;

        this.options = $.extend({}, UrlPicker.defaults, options);

        var modal  = $(tmpl.get('urlpicker.modal')).appendTo('body'),
            picker = new UI.modal.Modal(modal),
            url    = modal.find('.js-link-url'),
            source = $(element),
            link   = new Link({ typeFilter: ['/'] });

        modal.on('click', '.js-update', function () {
            picker.hide();

            if (url.val().match(/^@/)) {

                $.getJSON($this.options.url, { url: url.val() }, function(data) {
                        if (data.error) {
                            UI.notify(data.message, 'danger');
                        }

                        if (data.url) {
                            source.val(data.url);
                        }
                    })
                    .fail(function(jqXHR) {
                        UI.notify(jqXHR.status == 500 ? 'Unknown error.' : jqXHR.responseText, 'danger');
                    });

            } else {
                source.val(url.val());
            }
        });

        source.on('click', function() {
            picker.show();
            url.val(source.val());
            setTimeout(function() { url.focus(); }, 10);
        });

    };

    UrlPicker.defaults = {
        url: req.toUrl('admin/system/resolveurl')
    };

    return UrlPicker;
});