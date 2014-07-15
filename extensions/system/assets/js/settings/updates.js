require(['jquery', 'marketplace', 'system!locale', 'tmpl!package.updates,package.upload', 'uikit!upload', 'domReady!'], function($, marketplace, system, tmpl, uikit) {

    var page = $('#js-extensions, #js-themes'), update = $('.js-update', page), upload = $('.js-upload', page), market = $('.js-marketplace', page), params = page.data(), packages = {}, modal;

    // query for updates
    $.post(params.api + '/package/update', {'api_key': params.key, 'packages': page.attr('data-installed')}, function(data) {

        if (data.packages.length) {

            $.each(data.packages, function(i, p) {
                p.version.released = system.date('medium', p.version.released);
                packages[p.name] = p;
            });

            update.prepend(tmpl.render('package.updates', data));

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

        var $this = $(this), label = $this.text(), name = $this.data('install');

        $this.removeAttr('data-install').html('<i class="uk-icon-spinner uk-icon-spin"></i>');

        $.post(params.url, $.extend({'package': JSON.stringify(packages[name].version)}, system.csrf.params), function(data) {

            if (data.message) {

                delete packages[name];
                badge(packages);

                $this.addClass('uk-button-success').html('<i class="uk-icon-check"></i>');

            } else {
                uikit.notify(data.error, 'danger');
                $this.attr('data-install', name).html(label);
            }

        });

    });

    // upload package
    var progressbar = $(".js-upload-progressbar"),
        bar         = $('.uk-progress-bar', progressbar),
        dialog      = $('.js-upload-modal', upload),
        settings    = {

        action: upload.data("action"),
        type  : 'json',
        params: system.csrf.params,
        param : 'file',

        loadstart: function() {
            bar.css("width", "0%").text("0%");
            progressbar.removeClass("uk-hidden");
        },

        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css("width", percent+"%").text(percent+"%");
        },

        allcomplete: function(data) {

            bar.css("width", "100%").text("100%");

            setTimeout(function(){
                progressbar.addClass("uk-hidden");
            }, 250);

            if (data.error) {
                uikit.notify(data.error, 'danger');
                return;
            }

            $.post(params.api + '/package/' + data.package.name, function(info) {

                var version = info.versions[data.package.version];

                if (version && version.dist.shasum != data.package.shasum) {
                    show('checksum-mismatch', upload);
                }

            }, 'jsonp');

            dialog.html(tmpl.render('package.upload', data));

            if (!modal) {
                modal = uikit.modal(dialog);
            }

            modal.show();
        }
    };

    // upload objects
    uikit.uploadSelect($(".js-upload-select"), settings);
    uikit.uploadDrop($(".js-upload-drop"), settings);

    // install upload
    dialog.on('click', '[data-install]', function(e) {
        e.preventDefault();

        var $this = $(this), label = $this.text(), path = $this.data('install');

        $this.removeAttr('data-install').html('<i class="uk-icon-spinner uk-icon-spin"></i>');

        $.post(params.url, $.extend({'path': path}, system.csrf.params), function(data) {

            if (data.message) {
                modal.hide();
                uikit.notify(data.message, 'success');
            } else {
                $this.attr('data-install', path).html(label);
                uikit.notify(data.error, 'danger');
            }

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
