/**
 * RequireJS tmpl plugin for mustache templating
 * @author Pagekit, http://pagekit.com
 * @license MIT license
 */
define(['jquery', 'module', 'mustache'], function ($, mod, mustache) {

    var templates = {}, module = {

        get: function(name) {
            return templates[name] || '';
        },

        set: function(name, template) {
            templates[name] = template.replace(/^\s+|\s+$/g, '');
        },

        render: function(name, data) {
            return mustache.render(templates[name] || '', data);
        },

        config: mod.config()

    };

    return {
        load: function(res, req, onload, config) {

            var load = [];

            $.each(res.split(','), function(i, name) {
                if (!module.get(name)) {

                    var tmpl = $('[data-tmpl="' + name + '"]');

                    if (tmpl.length) {
                        module.set(name, tmpl.first().text());
                    } else {
                        load.push(name);
                    }
                }
            });

            if (load.length) {

                $.getJSON(req.toUrl(module.config.url) + load.join(','), function(data) {

                    $.each(data, function(name, template) {
                        module.set(name, template);
                    });

                }).always(function() {

                    onload(module);

                });

            } else {
                onload(module);
            }
        }
    };

});