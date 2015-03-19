(function () {

    var install = function (Vue) {

        var _ = Vue.util.extend({}, Vue.util);


        /**
         * The Http provides a service for sending XMLHttpRequests
         */

        var Http = function (config) {

            var request = new XMLHttpRequest(),
                headers = Http.defaults.headers,
                methods = {
                    success: function () {},
                    error: function () {}
                };

            config = _.extend({
                method: 'GET',
                url: '',
                data: '',
                params: {}
            }, config);

            headers = _.extend({},
                headers.common,
                headers[config.method.toLowerCase()],
                config.headers
            );

            if ((headers['Content-Type'] || '').indexOf('json') != -1 && _.isObject(config.data)) {
                config.data = JSON.stringify(config.data);
            }

            request.open(config.method, Url(config.url, config.params), true);

            _.each(headers, function (value, header) {
                request.setRequestHeader(header, value);
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

        _.extend(Http, {

            defaults: {

                headers: {
                    put: {'Content-Type': 'application/json'},
                    post: {'Content-Type': 'application/json'},
                    common: {}
                }

            },

            get: function (url, config) {
                return Http(_.extend({method: 'GET', url: url}, config));
            },

            post: function (url, data, config) {
                return Http(_.extend({method: 'POST', url: url, data: data}, config));
            },

            put: function (url, data, config) {
                return Http(_.extend({method: 'PUT', url: url, data: data}, config));
            },

            'delete': function (url, config) {
                return Http(_.extend({method: 'DELETE', url: url}, config));
            }

        });

        var parse = function (request) {

            var result;

            try {
                result = JSON.parse(request.responseText);
            } catch (e) {
                result = request.responseText;
            }

            return [result, request];
        };


        /**
         * The Url provides URL templating
         */

        var Url = function (url, params, base) {

            var urlParams = {}, query = [];

            if (!_.isPlainObject(params)) {
                params = {};
            }

            if (!base) {
                base = Url.defaults.base;
            }

            url = url.replace(/:([a-z]\w*)/gi, function (match, name) {

                if (params[name]) {
                    urlParams[name] = true;
                    return encodeUriSegment(params[name]);
                }

                return '';
            });

            if (!url.match(/^(https?:)?\//) && base) {
                url = base + '/' + url;
            }

            url = url.replace(/(^|[^:])[\/]{2,}/g, '$1/');
            url = url.replace(/(\w+)\/+$/, '$1');

            _.each(params, function (value, key) {
                if (!urlParams[key]) {
                    query.push(encodeUriSegment(key) + '=' + encodeUriSegment(value));
                }
            });

            if (query.length) {
                url += (url.indexOf('?') >= 0 ? '&' : '?') + query.join('&');
            }

            return url;
        };

        _.extend(Url, {

            defaults: {
                base: ''
            },

            params: function (params) {

                var query = [];

                _.each(params, function (value, key) {
                    query.push(encodeUriSegment(key) + '=' + encodeUriSegment(value));
                });

                return query.join('&');
            },

            parse: function (url) {

                var pattern = RegExp("^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\\?([^#]*))?(?:#(.*))?"),
                    matches = url.match(pattern);

                return {
                    url: url,
                    scheme: matches[1] || '',
                    host: matches[2] || '',
                    path: matches[3] || '',
                    query: matches[4] || '',
                    fragment: matches[5] || ''
                };
            }

        });

        var encodeUriQuery = function (value, spaces) {

            return encodeURIComponent(value).
                replace(/%40/gi, '@').
                replace(/%3A/gi, ':').
                replace(/%24/g, '$').
                replace(/%2C/gi, ',').
                replace(/%20/g, (spaces ? '%20' : '+'));
        };

        var encodeUriSegment = function (value) {

            return encodeUriQuery(value, true).
                replace(/%26/gi, '&').
                replace(/%3D/gi, '=').
                replace(/%2B/gi, '+');
        };


        /**
         * The Utility functions
         */

        _.each = function (obj, iterator) {

            var i, key;

            if (typeof obj.length == 'number') {
                for (i = 0; i < obj.length; i++) {
                    iterator.call(obj[i], obj[i], i);
                }
            } else if (_.isObject(obj)) {
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) {
                        iterator.call(obj[key], obj[key], key);
                    }
                }
            }

            return obj;
        };

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

        var extend = function(target, source, deep) {
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
        };


        /**
         * Add Vue functions
         */

        Vue.http = Http;
        Vue.prototype.$http = Http;
    };

    if (typeof exports == 'object') {
        module.exports = install;
    } else if (typeof define == 'function' && define.amd) {
        define([], function (){ return install; });
    } else if (window.Vue) {
        Vue.use(install);
    }

})();