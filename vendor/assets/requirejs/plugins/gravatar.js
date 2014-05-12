/**
 * Gravatar plugin
 * @author Pagekit, http://pagekit.com
 * @license MIT license
 */
define(['md5'], function (md5) {

    var base = '//www.gravatar.com/avatar/', module = {

        url: function(email, options) {
            return base + md5.hash(email) + module.params(options);
        },

        img: function(email, options) {
            return '<img src="' + module.url(email, options) + '">';
        },

        params: function(params) {

            var data = [];

            for (var name in params) {
                if (params.hasOwnProperty(name)) {
                    data.push(name + '=' + params[name]);
                }
            }

            return data.length ? '?' + data.join('&') : '';
        }
    };

    return module;
});