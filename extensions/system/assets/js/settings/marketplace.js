define('marketplace', ['jquery', 'system', 'tmpl!marketplace.table,marketplace.details', 'uikit!pagination'], function($, system, tmpl, uikit) {

    var updates = {}, packages = {}, element, form, details, modal, params;

    // details modal
    function detailsModal(e) {
        e.preventDefault();

        var name = $(this).data('package'), data = packages[name];

        data.iframe = params.api.replace(/\/api$/, '') + '/marketplace/frame/' + name;
        details.html(tmpl.render('marketplace.details', data));

        if (!modal) {
            modal = uikit.modal(details);
        }

        modal.show();
    }

    // details install button
    function installButton(e) {
        e.preventDefault();

        var $this = $(this), label = $this.text(), name = $this.data('install');

        $this.removeAttr('data-install').html('<i class="uk-icon-spinner uk-icon-spin"></i>');

        $.post(params.url, $.extend({'package': JSON.stringify(packages[name].version)}, system.csrf.params), function(data) {

            if (data.message) {
                packages[name].installed = true;
                $this.addClass('uk-button-success').html('<i class="uk-icon-check"></i>');
            } else {
                uikit.notify(data.error, 'danger');
                $this.attr('data-install', name).html(label);
            }

        });

    }

    // query marketplace
    function queryMarketplace(e, data) {

        var content = '', message = '', container, pagination;

        if (e) {
            e.preventDefault();
        }

        if (!data) {
            data = {};
        }

        $('input', form).each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        $.post(params.api + '/package/search', data, function(data) {

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

        }, 'jsonp').fail(function() {

            message = 'no-connection';

        }).always(function(data) {

            $('[data-msg]', element).each(function() {

                var $this = $(this);

                if ($this.data('msg') == message) {
                    $this.removeClass('uk-hidden');
                } else {
                    $this.addClass('uk-hidden');
                }

            });

            container  = $('.js-marketplace-content', element).html(content);
            pagination = $('.uk-pagination', container);

            if (pagination.length && data.pages > 1) {
                uikit.pagination(pagination.on('uk.pagination.select', function(e, page){
                    queryMarketplace(null, {'page': page});
                }), {'pages': data.pages, 'currentPage': data.page + 1});
            }

        });

    }

    return {

        init: function(el, p) {

            element = $(el); form = $('form', el); details = $('.js-marketplace-details', el); params = p; updates = p.updates;

            element.on('click', '[data-package]', detailsModal);
            details.on('click', '[data-install]', installButton);

            form
                .on('submit', queryMarketplace)
                .on('keyup', 'input', uikit.Utils.debounce(function() {
                    form.submit();
                }, 150))
                .on('change', 'select', function() {
                    form.submit();
                })
                .submit();
        }

    };

});