require(['jquery', 'marketplace', 'tmpl!package.updates,package.upload', 'uikit!form-file', 'domReady!'], function($, marketplace, tmpl, uikit) {

    var page = $('#js-extensions, #js-themes'), update = $('.js-update', page), upload = $('.js-upload', page), market = $('.js-marketplace', page), params = page.data(), packages = {}, modal;

    // query for updates
    $.post(params.api + '/package/update', {'api_key': params.key, 'packages': page.attr('data-installed')}, function(data) {

        if (data.packages.length) {

            $.each(data.packages, function(i, p) {
                packages[p.name] = p;
            });

            update.append(tmpl.render('package.updates', data));

        } else {
            show('no-updates', update);
        }

    }, 'jsonp').fail(function() {

        show('no-connection', update);

    }).always(function() {

        badge(packages);
        marketplace.init(market, $.extend({updates: packages}, params));

    });

    // install update
    update.on('click', '[data-install]', function(e) {
        e.preventDefault();

        var $this = $(this), name = $this.data('install');

        $this.removeAttr('data-install').html('<i class="uk-icon-spinner uk-icon-spin"></i>');

        $.post(params.url, {'package': JSON.stringify(packages[name].version)}, function(data) {

            $this.removeClass('uk-button-primary');

            if (data.message) {

                delete packages[name];
                badge(packages);

                $this.addClass('uk-button-success').html('<i class="uk-icon-check"></i>');

            } else {
                $this.html('Error!');
            }

        });

    });

    // upload package
    $('.js-upload-button', upload).on('click', function(e) {
        e.preventDefault();

        var form = $('form', upload), dialog = $('.js-upload-modal', upload);

        $.ajax({

            url: form.attr('action'),
            data: new FormData(form[0]),
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false

        }).done(function(data) {

            if (data.error) {
                uikit.notify(data.error, 'danger');
                return;
            }

            $.post(params.api + '/package/' + data.package.name, function(info) {

                var package = info.versions[data.package.version];

                if (package) {
                    if (package.dist.shasum != data.package.shasum) {
                        show('checksum-mismatch', upload);
                    }
                }

            }, 'jsonp');

            dialog.html(tmpl.render('package.upload', data));

            if (!modal) {
                modal = new uikit.modal.Modal(dialog);
            }

            modal.show();

        });

    });

    function show(message, context) {

        context.find('[data-msg]').each(function() {

            var $this = $(this);

            if ($this.data('msg') === message) {
                $this.removeClass('uk-hidden');
            } else {
                $this.addClass('uk-hidden');
            }

        });

    }

    function badge(packages) {

        var len = length(packages);

        if (len) {
            $('.js-updates', page).replaceWith('<span class="js-updates uk-badge">' + len + '</span>');
        } else {
            $('.js-updates', page).remove();
        }

    }

    function length(obj) {

        var len = 0, key;

        for (key in obj) {
            if (obj.hasOwnProperty(key)) len++;
        }

        return len;
    }

});
