(function (window) {

    var install = function (Vue) {

        var _ = Vue.util.extend({}, Vue.util);


        /**
         * The Url provides URL templating
         */

        var Url = function (url, params, root) {

            var urlParams = {}, queryParams = {}, query;

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
                    queryParams[key] = value;
                }
            });

            query = Url.params(queryParams);

            if (query) {
                url += (url.indexOf('?') == -1 ? '?' : '&') + query;
            }

            return url;
        };

        _.extend(Url, {

            root: '',

            params: function (obj) {

                var params = [];

                params.add = function (key, value) {

                    if (_.isFunction (value)) {
                        value = value();
                    }

                    if (value === null) {
                        value = '';
                    }

                    this.push(encodeUriSegment(key) + '=' + encodeUriSegment(value));
                };

                serialize(params, obj);

                return params.join('&');
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

        var serialize = function (params, obj, scope) {

            var array = _.isArray(obj), plain = _.isPlainObject(obj), hash;

            _.each(obj, function (value, key) {

                hash = _.isObject(value) || _.isArray(value);

                if (scope) {
                    key = scope + '[' + (plain || hash ? key : '') + ']';
                }

                if (!scope && array) {
                    params.add(value.name, value.value);
                } else if (hash) {
                    serialize(params, value, key);
                } else {
                    params.add(key, value);
                }
            });
        };

        var encodeUriSegment = function (value) {

            return encodeUriQuery(value, true).
                replace(/%26/gi, '&').
                replace(/%3D/gi, '=').
                replace(/%2B/gi, '+');
        };

        var encodeUriQuery = function (value, spaces) {

            return encodeURIComponent(value).
                replace(/%40/gi, '@').
                replace(/%3A/gi, ':').
                replace(/%24/g, '$').
                replace(/%2C/gi, ',').
                replace(/%20/g, (spaces ? '%20' : '+'));
        };


        /**
         * The Http provides a service for sending XMLHttpRequests
         */

        var Http = function (url, options) {

            var request = new XMLHttpRequest(),
                headers = Http.defaults.headers;

            if (_.isObject(url)) {
                options = url;
                url = '';
            }

            headers = _.extend({},
                headers.common,
                headers[options.method.toLowerCase()]
            );

            options = _.extend(true, {url: url, headers: headers},
                Http.defaults.options,
                options
            );

            if (options.emulateHTTP && /^(PUT|PATCH|DELETE)$/i.test(options.method)) {
                headers['X-HTTP-Method-Override'] = options.method;
                options.method = 'POST';
            }

            if (options.emulateJSON && _.isObject(options.data)) {
                headers['Content-Type'] = 'application/x-www-form-urlencoded';
                options.data = Url.params(options.data);
            }

            if (_.isObject(options.data)) {
                options.data = JSON.stringify(options.data);
            }

            var promise = new _.Promise(function (resolve, reject) {

                request.open(options.method, Url(options.url, options.params, options.urlRoot), true);

                _.each(headers, function (value, header) {
                    request.setRequestHeader(header, value);
                });

                request.onreadystatechange = function () {
                    if (this.readyState === 4) {
                        if (this.status >= 200 && this.status < 300) {
                            resolve(this);
                        } else {
                            reject(this);
                        }
                    }
                };

                request.send(options.data);
            });

            _.extend(promise, {
                success: function (onSuccess) {
                    this.then(function(request) {
                        onSuccess.apply(onSuccess, parseReq(request));
                    }, function() {});

                    return this;
                },
                error: function (onError) {
                    this.catch(function(request) {
                        onError.apply(onError, parseReq(request));
                    });

                    return this;
                },
                always: function (onAlways) {
                    this.then(onAlways, onAlways);

                    return this;
                }
            });

            if (options.success) {
                promise.success(options.success);
            }

            if (options.error) {
                promise.error(options.error);
            }

            return promise;
        };

        var parseReq = function (request) {

            var result;

            try {
                result = JSON.parse(request.responseText);
            } catch (e) {
                result = request.responseText;
            }

            return [result, request.status, request];
        };

        var jsonType = {'Content-Type': 'application/json;charset=utf-8'};

        _.extend(Http, {

            defaults: {

                config: {
                    method: 'GET',
                    params: {},
                    data: '',
                    urlRoot: '',
                    emulateHTTP: false,
                    emulateJSON: false
                },

                headers: {
                    get: { Accept: 'application/json, text/plain, * / *' },
                    put: jsonType,
                    post: jsonType,
                    patch: jsonType,
                    common: {}
                }

            },

            get: function (url, success, options) {
                return Http(url, _.extend({method: 'GET', success: success}, options));
            },

            put: function (url, data, success, options) {
                return Http(url, _.extend({method: 'PUT', data: data, success: success}, options));
            },

            post: function (url, data, success, options) {
                return Http(url, _.extend({method: 'POST', data: data, success: success}, options));
            },

            patch: function (url, data, success, options) {
                return Http(url, _.extend({method: 'PATCH', data: data, success: success}, options));
            },

            'delete': function (url, success, options) {
                return Http(url, _.extend({method: 'DELETE', success: success}, options));
            }

        });


        /**
         * The Resource provides interaction support with RESTful services
         */

        var Resource = function (url, params, actions) {

            var self = this;

            _.extend(true, this, Resource.defaults, {actions: actions});

            _.each(this.actions, function (action, name) {

                action = _.extend(true, {url: url, params: params || {}}, action);

                self[name] = function () {
                    return Http(getOptions(action, arguments));
                };
            });

            function getOptions(action, args) {

                var options = _.extend({}, action), params = {}, data, success, error;

                /* jshint -W086 */ /* (fall through case statements) */
                switch (args.length) {

                    case 4:

                        error = args[3];
                        success = args[2];

                    case 3:
                    case 2:

                        if (_.isFunction (args[1])) {

                            if (_.isFunction (args[0])) {

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

                        if (_.isFunction (args[0])) {
                            success = args[0];
                        } else if (/^(POST|PUT|PATCH)$/i.test(options.method)) {
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
                /* jshint +W086 */

                options.url = action.url;
                options.data = data;
                options.params = _.extend({}, action.params, params);

                if (success) {
                    options.success = success;
                }

                if (error) {
                    options.error = error;
                }

                return options;
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
        Vue.resource = function (url, params, actions) {
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

            var array = [], args = array.slice.call(arguments, 1), deep;

            if (typeof target == 'boolean') {
                deep = target;
                target = args.shift();
            }

            args.forEach(function (arg) {
                extend(target, arg, deep);
            });

            return target;
        };

        var extend = function (target, source, deep) {
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


        /**
         * The Promise polyfill (https://gist.github.com/briancavalier/814313)
         */

        _.Promise = window.Promise;

        if (!_.Promise) {

            _.Promise = function (executor) {
                executor(this.resolve.bind(this), this.reject.bind(this));
                this._thens = [];
            };

            _.Promise.prototype = {

                then: function (onResolve, onReject, onProgress) {
                    this._thens.push({resolve: onResolve, reject: onReject, progress: onProgress});
                },

                'catch': function (onReject) {
                    this._thens.push({reject: onReject});
                },

                resolve: function (value) {
                    this._complete('resolve', value);
                },

                reject: function (reason) {
                    this._complete('reject', reason);
                },

                progress: function (status) {

                    var i = 0, aThen;

                    while (aThen = this._thens[i++]) {
                        aThen.progress && aThen.progress(status);
                    }
                },

                _complete: function (which, arg) {

                    this.then = which === 'resolve' ?
                        function (resolve, reject) { resolve && resolve(arg); } :
                        function (resolve, reject) { reject && reject(arg); };

                    this.resolve = this.reject = this.progress =
                        function () { throw new Error('Promise already completed.'); };

                    var aThen, i = 0;

                    while (aThen = this._thens[i++]) {
                        aThen[which] && aThen[which](arg);
                    }

                    delete this._thens;
                }
            };
        }

    };

    if (typeof exports == 'object') {
        module.exports = install;
    } else if (typeof define == 'function' && define.amd) {
        define([], function (){ return install; });
    } else if (window.Vue) {
        Vue.use(install);
    }

})(this);
