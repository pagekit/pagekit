require(['jquery', 'tmpl!marketplace.table,marketplace.details', 'uikit', 'domReady!'], function($, tmpl, uikit) {

    var page = $('#js-extensions, #js-themes'), form = $('.js-marketplace-form', page), details = $('.js-marketplace-details', page), params = page.data(), updates = {}, packages = {}, modal;

    // details modal
    page.on('click', '[data-package]', function(e) {
        e.preventDefault();

        var name = $(this).data('package'), data = packages[name];

        data.iframe = params.api.replace(/\/api$/, '') + '/marketplace/frame/' + name;
        details.html(tmpl.render('marketplace.details', data));

        if (!modal) {
            modal = new uikit.modal.Modal(details);
        }

        modal.show();

    });

    // details install button
    details.on('click', '[data-install]', function(e) {
        e.preventDefault();

        var $this = $(this), name = $this.data('install');

        $this.removeAttr('data-install').html('<i class="uk-icon-spinner uk-icon-spin"></i>');

        $.post(params.url, {'package': JSON.stringify(packages[name].version)}, function(data) {

            $this.removeClass('uk-button-primary');

            if (data.message) {
                packages[name].installed = true;
                $this.addClass('uk-button-success').html('<i class="uk-icon-check"></i>');
            } else {
                $this.html('Error!');
            }

        });

    });

    // input search
    form.on('keyup', 'input', debounce(function() {
        form.submit();
    }, 150));

    // select type
    form.on('change', 'select', function() {
        form.submit();
    });

    // query the marketplace
    form.on('submit', function(e) {
        e.preventDefault();

        var content = '', message = '';

        $.post(params.api + '/package/search', $(this).serialize(), function(data) {

            if (data.packages.length) {

                packages = {};

                $.each(data.packages, function(i, p) {

                    packages[p.name] = p;

                    if (updates[p.name]) {
                        p.update = true;
                    } else if (params.installed[p.name]) {
                        p.installed = true;
                    } else {
                        p.install = true;
                    }

                });

                content = tmpl.render('marketplace.table', data);

            } else {
                message = 'no-packages';
            }

        }, 'jsonp').fail(function(e) {

            message = 'no-connection';

        }).always(function() {

            $('[data-msg]', page).each(function() {

                var $this = $(this);

                if ($this.data('msg') == message) {
                    $this.removeClass('uk-hidden');
                } else {
                    $this.addClass('uk-hidden');
                }

            });

            $('.js-marketplace-content').html(content);

        });

    });

    // query for updates
    $.post(params.api + '/package/update', {'api_key': params.key, 'packages': page.attr('data-installed')}, function(data) {

        if (data.packages) {
            $.each(data.packages, function(i, p) {
                updates[p.name] = p.version;
            });
        }

    }, 'jsonp').always(function() {

        form.submit();

    });

    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

});