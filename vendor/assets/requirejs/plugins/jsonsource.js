/**
 * RequireJS jsonsource plugin
 * @author Pagekit, http://pagekit.com
 * @license MIT license
 */
define(['jquery', 'module'], function ($, mod) {

    var sources = {},
        cfg     = mod.config();

    return {
        load: function(res, req, onload, config) {

            var load = [];

            res.split(',').forEach(function(name) {
                if (!sources[name]) load.push(name);
            });

            if (load.length) {

                $.getJSON(req.toUrl(cfg.url) + load.join(','), function(data) {

                    Object.keys(data).forEach(function(name) {
                        sources[name] = data[name];
                    });

                }).always(function() {

                    onload(sources);
                });

            } else {
                onload(sources);
            }
        }
    };

});