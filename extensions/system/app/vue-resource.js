(function (window) {

    var install = function (Vue) {

        var _ = Vue.util.extend({}, Vue.util);


        /**
         * The Url provides URL templating
         */

        var Url = function (url, params, root) {

            var urlParams = {}, query = [];

            if (!_.isPlainObject(params)) {
                params = {};
            }

            if (!root) {
                root = Url.root;
            }

            url = url.replace(/:([a-z]\w*)/gi, function (match, name) {

                if (params[name]) {
                    urlParams[name] = true;
                    return encodeUriSegment(params[name]);
                }

                return '';
            });

            if (!url.match(/^(https?:)?\//) && root) {
                url = root + '/' + url;
            }

            url = url.replace(/(^|[^:])[\/]{2,}/g, '$1/');
            url = url.replace(/(\w+)\/+$/, '$1');

            _.each(params, function (value, key) {
                if (!urlParams[key]) {
                    query.push(encodeUriSegment(key) + '=' + encodeUriSegment(value));
                }
            });

            if (query.length) {
                url += (url.indexOf('?') == -1 ? '?' : '&') + query.join('&');
            }

            return url;
        };

        _.extend(Url, {

            root: '',

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
         * The Http provides a service for sending XMLHttpRequests
         */

        var Http = function (config) {

            var request = new window.XMLHttpRequest(),
                headers = Http.defaults.headers,
                methods = {success: [], error: []},
                status  = null, result, method;

            config = _.extend({},
                Http.defaults.config,
                config
            );

            headers = _.extend({},
                headers.common,
                headers[config.method.toLowerCase()],
                config.headers
            );

            if ((headers['Content-Type'] || '').indexOf('json') != -1 && _.isObject(config.data)) {
                config.data = JSON.stringify(config.data);
            }

            if (config.methodOverride && /^(PUT|PATCH|DELETE)$/i.test(config.method)) {
                headers['X-HTTP-Method-Override'] = config.method;
                config.method = 'POST';
            }

            request.open(config.method, Url(config.url, config.params, config.urlRoot), true);

            _.each(headers, function (value, header) {
                request.setRequestHeader(header, value);
            });

            request.onreadystatechange = function () {
                if (request.readyState === 4) {

                    status = request.status >= 200 && request.status < 300;
                    method = methods[status ? 'success' : 'error'];
                    result = parse(request);

                    for (i = 0; i < method.length; i++) {
                        method[i].apply(method[i], result);
                    }
                }
            };

            request.send(config.data);

            var parse = function (request) {

                var result;

                try {
                    result = JSON.parse(request.responseText);
                } catch (e) {
                    result = request.responseText;
                }

                return [result, request.status, request];
            };

            var callbacks = {
                success: function (callback) {

                    if (status === null) {
                        methods.success.push(callback);
                    } else {
                        callback.apply(callback, result);
                    }

                    return callbacks;
                },
                error: function (callback) {

                    if (status === null) {
                        methods.error.push(callback);
                    } else {
                        callback.apply(callback, result);
                    }

                    return callbacks;
                }
            };

            return callbacks;
        };

        var contentType = {'Content-Type': 'application/json;charset=utf-8'};

        _.extend(Http, {

            defaults: {

                config: {
                    method: 'GET',
                    url: '',
                    urlRoot: '',
                    data: '',
                    params: {},
                    methodOverride: false
                },

                headers: {
                    put: contentType,
                    post: contentType,
                    patch: contentType,
                    common: {}
                }

            },

            get: function (url, config) {
                return Http(_.extend({method: 'GET', url: url}, config));
            },

            put: function (url, data, config) {
                return Http(_.extend({method: 'PUT', url: url, data: data}, config));
            },

            post: function (url, data, config) {
                return Http(_.extend({method: 'POST', url: url, data: data}, config));
            },

            patch: function (url, data, config) {
                return Http(_.extend({method: 'PATCH', url: url, data: data}, config));
            },

            'delete': function (url, config) {
                return Http(_.extend({method: 'DELETE', url: url}, config));
            }

        });


        /**
         * The Resource provides interaction support with RESTful services
         */

        var Resource = function(url, params, actions) {

            var self = this;

            _.extend(true, this, Resource.defaults, {actions: actions});

            _.each(this.actions, function(action, name) {

                action = _.extend(true, {url: url, params: params || {}}, action);

                self[name] = function() {
                    return Http(getConfig(action, arguments));
                };
            });

            function getConfig(action, args) {

                var config = _.extend({}, action), params = {}, data, success, error;

                switch (args.length) {

                    case 4:

                        error = args[3];
                        success = args[2];

                    case 3:
                    case 2:

                        if (_.isFunction(args[1])) {

                            if (_.isFunction(args[0])) {

                                success = args[0];
                                error = args[1];

                                break;
                            }

                            success = args[1];
                            error = args[2];

                        } else {

                            params = args[0];
                            data = args[1];
                            success = args[2];

                            break;
                        }

                    case 1:

                        if (_.isFunction(args[0])) {
                            success = args[0];
                        } else if (/^(POST|PUT|PATCH)$/i.test(config.method)) {
                            data = args[0];
                        } else {
                            params = args[0];
                        }

                        break;

                    case 0:

                        break;

                    default:

                        throw 'Expected up to 4 arguments [params, data, success, error], got ' + args.length + ' arguments';
                }

                config.url = action.url;
                config.params = _.extend({}, action.params, params);

                if (success) {
                    config.success = success;
                }

                if (error) {
                    config.error = error;
                }

                return config;
            }

        };

        Resource.defaults = {

            actions: {
                'get': {method: 'GET'},
                'save': {method: 'POST'},
                'query': {method: 'GET'},
                'remove': {method: 'DELETE'},
                'delete': {method: 'DELETE'}
            }

        };


        /**
         * The Vue functions
         */

        Vue.url = Url;
        Vue.http = Http;
        Vue.resource = function(url, params, actions) {
            return new Resource(url, params, actions);
        };

        Vue.prototype.$url = Vue.url;
        Vue.prototype.$http = Vue.http;
        Vue.prototype.$resource = Vue.resource;


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

        _.isFunction = function (obj) {
            return obj && typeof obj === 'function';
        };
    };

    if (typeof exports == 'object') {
        module.exports = install;
    } else if (typeof define == 'function' && define.amd) {
        define([], function (){ return install; });
    } else if (window.Vue) {
        Vue.use(install);
    }

})(this);
