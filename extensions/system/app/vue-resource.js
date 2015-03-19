(function () {

    var install = function (Vue) {

        var _ = Vue.util.extend({}, Vue.util);

        var parse = function (request) {

            var result;

            try {
                result = JSON.parse(request.responseText);
            } catch (e) {
                result = request.responseText;
            }

            return [result, request];
        };

        var http = function (config) {

            var request = new XMLHttpRequest(),
                headers = http.defaults.headers,
                methods = {
                    success: function () {},
                    error: function () {}
                };

            config = _.extend({
                url: '',
                method: 'GET',
                data: ''
            }, config);

            headers = _.extend({},
                headers.common,
                headers[config.method.toLowerCase()],
                config.headers
            );

            if ((headers['Content-Type'] || '').indexOf('json') != -1 && _.isObject(config.data)) {
                config.data = JSON.stringify(config.data);
            }

            request.open(config.method, config.url, true);

            Object.keys(headers).forEach(function (header) {
                request.setRequestHeader(header, headers[header]);
            });

            request.onreadystatechange = function () {
                if (request.readyState === 4) {
                    if (request.status >= 200 && request.status < 300) {
                        methods.success.apply(methods, parse(request));
                    } else {
                        methods.error.apply(methods, parse(request));
                    }
                }
            };

            request.send(config.data);

            var callbacks = {
                success: function (callback) {
                    methods.success = callback;
                    return callbacks;
                },
                error: function (callback) {
                    methods.error = callback;
                    return callbacks;
                }
            };

            return callbacks;
        };

        _.extend(http, {

            defaults: {

                headers: {
                    put: {'Content-Type': 'application/json'},
                    post: {'Content-Type': 'application/json'},
                    common: {}
                }

            },

            get: function (url, config) {
                return http(_.extend({method: 'GET', url: url}, config));
            },

            post: function (url, data, config) {
                return http(_.extend({method: 'POST', url: url, data: data}, config));
            },

            put: function (url, data, config) {
                return http(_.extend({method: 'PUT', url: url, data: data}, config));
            },

            'delete': function (url, config) {
                return http(_.extend({method: 'DELETE', url: url}, config));
            }

        });

        Vue.prototype.$http = http;

        _.extend = function (target) {

            var deep, args = Array.slice(arguments, 1);

            if (typeof target == 'boolean') {
                deep = target;
                target = args.shift();
            }

            args.forEach(function (arg) {
                extend(target, arg, deep);
            });

            return target;
        };

        function extend(target, source, deep) {
            for (var key in source) {
                if (deep && (_.isPlainObject(source[key]) || _.isArray(source[key]))) {
                    if (_.isPlainObject(source[key]) && !_.isPlainObject(target[key])) {
                        target[key] = {};
                    }
                    if (_.isArray(source[key]) && !_.isArray(target[key])) {
                        target[key] = [];
                    }
                    extend(target[key], source[key], deep);
                } else if (source[key] !== undefined) {
                    target[key] = source[key];
                }
            }
        }

    };

    if (typeof exports == 'object') {
        module.exports = install;
    } else if (typeof define == 'function' && define.amd) {
        define([], function (){ return install; });
    } else if (window.Vue) {
        Vue.use(install);
    }

})();