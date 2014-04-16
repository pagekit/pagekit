require(['jquery', 'marketplace', 'tmpl!package.updates,package.upload', 'uikit!upload', 'locale', 'domReady!'], function($, marketplace, tmpl, uikit, locale) {

    var page = $('#js-extensions, #js-themes'), update = $('.js-update', page), upload = $('.js-upload', page), market = $('.js-marketplace', page), params = page.data(), packages = {}, modal;

    // query for updates
    $.post(params.api + '/package/update', {'api_key': params.key, 'packages': page.attr('data-installed')}, function(data) {

        if (data.packages.length) {

            $.each(data.packages, function(i, p) {
                p.version.released = locale.date('medium', p.version.released);
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

    var progressbar = $(".js-upload-progressbar"),
        bar         = progressbar.find('.uk-progress-bar'),
        dialog      = $('.js-upload-modal', upload),
        settings    = {

        action: upload.data("action"), // upload url
        type  : 'json',
        params: {'_csrf': upload.find('input[name="_csrf"]').val() },
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

                var package = info.versions[data.package.version];

                if (package && package.dist.shasum != data.package.shasum) {
                    show('checksum-mismatch', upload);
                }

            }, 'jsonp');

            dialog.html(tmpl.render('package.upload', data));

            if (!modal) {
                modal = new uikit.modal.Modal(dialog);
            }

            modal.show();
        }
    },

    // upload objects
    uploadselect = new uikit.upload.select($(".js-upload-select"), settings),
    uploaddrop   = new uikit.upload.drop($(".js-upload-drop"), settings);


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
