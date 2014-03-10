require(['jquery', 'tmpl!package.updates', 'uikit', 'uikit!form-file', 'domReady!'], function($, tmpl, uikit) {

    var page = $('#js-extensions, #js-themes'), params = page.data(), packages = {};

    // upload frame
    var frame = $('#js-upload-frame').on('load', function() {
        uikit.modal.dialog(frame.contents().find('body').html()).show();
    });

    // install update
    $('.js-update-table').on('click', '[data-install]', function(e) {
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

    // query for updates
    $.post(params.api + '/package/update', {'api_key': params.key, 'packages': page.attr('data-installed')}, function(data) {

        if (data.packages.length) {

            $.each(data.packages, function(i, p) {
                packages[p.name] = p;
            });

            $('.js-update-table').append(tmpl.render('package.updates', data));

        } else {
            show('no-updates');
        }

    }, 'jsonp').fail(function() {

        show('no-connection');

    }).always(function() {

        badge(packages);

    });

    function show(message) {

        page.find('[data-msg]').each(function() {

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
            $('.js-updates').replaceWith('<span class="js-updates uk-badge">' + len + '</span>');
        } else {
            $('.js-updates').remove();
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