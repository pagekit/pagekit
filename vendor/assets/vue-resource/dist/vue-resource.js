/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	var Url = __webpack_require__(1);
	var Http = __webpack_require__(2);
	var Resource = __webpack_require__(3);

	/**
	 * Install plugin.
	 */

	function install (Vue) {

	    Vue.url = Url;
	    Vue.http = Http;
	    Vue.resource = function (url, params, actions) {
	        return new Resource(url, params, actions);
	    };

	    Vue.prototype.$url = Vue.url;
	    Vue.prototype.$http = Vue.http;
	    Vue.prototype.$resource = Vue.resource;

	}

	if (window.Vue) {
	    Vue.use(install);
	}

	module.exports = install;


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(7);

	/**
	 * Url provides URL templating.
	 *
	 * @param {String} url
	 * @param {Object} params
	 * @param {String} root
	 */

	function Url (url, params, root) {

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
	}

	/**
	 * Url root path.
	 */

	Url.root = '';

	/**
	 * Encodes a Url parameter string.
	 *
	 * @param {Object} obj
	 */

	Url.params = function (obj) {

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
	};

	/**
	 * Parse a URL and return its components.
	 *
	 * @param {String} url
	 */

	Url.parse = function (url) {

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
	};

	function serialize (params, obj, scope) {

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
	}

	function encodeUriSegment (value) {

	    return encodeUriQuery(value, true).
	        replace(/%26/gi, '&').
	        replace(/%3D/gi, '=').
	        replace(/%2B/gi, '+');
	}

	function encodeUriQuery (value, spaces) {

	    return encodeURIComponent(value).
	        replace(/%40/gi, '@').
	        replace(/%3A/gi, ':').
	        replace(/%24/g, '$').
	        replace(/%2C/gi, ',').
	        replace(/%20/g, (spaces ? '%20' : '+'));
	}

	module.exports = Url;


/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(7);
	var Url = __webpack_require__(1);
	var jsonType = { 'Content-Type': 'application/json;charset=utf-8' };

	/**
	 * Http provides a service for sending XMLHttpRequests.
	 */

	function Http (url, options) {

	    var request = new XMLHttpRequest(),
	        headers = Http.headers;

	    if (_.isObject(url)) {
	        options = url;
	        url = '';
	    }

	    headers = _.extend({},
	        headers.common,
	        headers[options.method.toLowerCase()]
	    );

	    options = _.extend(true, {url: url, headers: headers},
	        Http.options,
	        options
	    );

	    if (_.isFunction(options.beforeSend)) {
	        options.beforeSend(request, options);
	    }

	    if (_.isObject(options.data) && /FormData/i.test(options.data.toString())) {
	        delete headers['Content-Type'];
	    }

	    if (options.emulateHTTP && /^(PUT|PATCH|DELETE)$/i.test(options.method)) {
	        headers['X-HTTP-Method-Override'] = options.method;
	        options.method = 'POST';
	    }

	    if (options.emulateJSON && _.isPlainObject(options.data)) {
	        headers['Content-Type'] = 'application/x-www-form-urlencoded';
	        options.data = Url.params(options.data);
	    }

	    if (_.isPlainObject(options.data)) {
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

	            this.then(function (request) {
	                onSuccess.apply(onSuccess, parseReq(request));
	            }, function () {});

	            return this;
	        },

	        error: function (onError) {

	            this.catch(function (request) {
	                onError.apply(onError, parseReq(request));
	            });

	            return this;
	        },

	        always: function (onAlways) {

	            var cb = function (request) {
	                onAlways.apply(onAlways, parseReq(request));
	            };

	            this.then(cb, cb);

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
	}

	function parseReq (request) {

	    var result;

	    try {
	        result = JSON.parse(request.responseText);
	    } catch (e) {
	        result = request.responseText;
	    }

	    return [result, request.status, request];
	}

	Http.options = {
	    method: 'GET',
	    params: {},
	    data: '',
	    urlRoot: '',
	    beforeSend: null,
	    emulateHTTP: false,
	    emulateJSON: false
	};

	Http.headers = {
	    put: jsonType,
	    post: jsonType,
	    patch: jsonType,
	    'delete': jsonType,
	    common: { 'Accept': 'application/json, text/plain, */*' }
	};

	Http.get = function (url, success, options) {
	    return Http(url, _.extend({method: 'GET', success: success}, options));
	};

	Http.put = function (url, data, success, options) {
	    return Http(url, _.extend({method: 'PUT', data: data, success: success}, options));
	};

	Http.post = function (url, data, success, options) {
	    return Http(url, _.extend({method: 'POST', data: data, success: success}, options));
	};

	Http.patch = function (url, data, success, options) {
	    return Http(url, _.extend({method: 'PATCH', data: data, success: success}, options));
	};

	Http.delete = function (url, success, options) {
	    return Http(url, _.extend({method: 'DELETE', success: success}, options));
	};

	module.exports = Http;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(7);
	var Http = __webpack_require__(2);

	/**
	 * Resource provides interaction support with RESTful services.
	 */

	function Resource (url, params, actions) {

	    var self = this, acts;

	    acts = _.extend({},
	        Resource.actions,
	        actions
	    );

	    _.each(acts, function (action, name) {

	        action = _.extend(true, {url: url, params: params || {}}, action);

	        self[name] = function () {
	            return Http(opts(action, arguments));
	        };
	    });
	}

	function opts (action, args) {

	    var options = _.extend({}, action), params = {}, data, success, error;

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

	Resource.actions = {

	    'get': {method: 'GET'},
	    'save': {method: 'POST'},
	    'query': {method: 'GET'},
	    'remove': {method: 'DELETE'},
	    'delete': {method: 'DELETE'}

	};

	module.exports = Resource;


/***/ },
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	var _ = Vue.util.extend({}, Vue.util);

	/**
	 * Utility functions.
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

	function extend (target, source, deep) {
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

	_.isFunction = function (obj) {
	    return obj && typeof obj === 'function';
	};

	/**
	 * Promise polyfill (https://gist.github.com/briancavalier/814313)
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

	module.exports = _;


/***/ }
/******/ ]);