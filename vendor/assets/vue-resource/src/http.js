var _ = require('./util');
var jsonType = { 'Content-Type': 'application/json;charset=utf-8' };

/**
 * Http provides a service for sending XMLHttpRequests.
 */

function Http (url, options) {

    var headers, self = this, promise;

    options = options || {};

    if (_.isPlainObject(url)) {
        options = url;
        url = '';
    }

    headers = _.extend({},
        Http.headers.common,
        Http.headers[options.method ? options.method.toLowerCase() : 'post']
    );

    options = _.extend(true, {url: url, headers: headers, jsonp: false},
        Http.options, _.options('http', this, options)
    );

    if (_.isObject(options.data) && /FormData/i.test(options.data.toString())) {
        delete options.headers['Content-Type'];
    }

    if (options.emulateHTTP && options.method && /^(PUT|PATCH|DELETE)$/i.test(options.method)) {
        options.headers['X-HTTP-Method-Override'] = options.method;
        options.method = 'POST';
    }

    promise = new _.Promise((options.jsonp ? jsonp : xhr).bind(this, (this.$url || Vue.url), options));

    _.extend(promise, {

        success: function (onSuccess) {

            this.then(function (request) {
                onSuccess.apply(self, parseReq(request));
            }, function () {});

            return this;
        },

        error: function (onError) {

            this.catch(function (request) {
                onError.apply(self, parseReq(request));
            });

            return this;
        },

        always: function (onAlways) {

            var cb = function (request) {
                onAlways.apply(self, parseReq(request));
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

function xhr(url, options, resolve, reject) {

    var request = new XMLHttpRequest();

    if (_.isFunction(options.beforeSend)) {
        options.beforeSend(request, options);
    }

    if (options.emulateJSON && _.isPlainObject(options.data)) {
        options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        options.data = Vue.url.params(options.data);
    }

    if (_.isPlainObject(options.data)) {
        options.data = JSON.stringify(options.data);
    }

    request.open(options.method, url(options), true);

    _.each(options.headers, function (value, header) {
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
}

function jsonp(url, options, resolve, reject) {

    var head = document.getElementsByTagName('head')[0],
        script = document.createElement('script'),
        callback = '_jsonpcallback'+(new Date().getTime())+(Math.ceil(Math.random() * 100000));

    if (options.jsonp === true) {
        options.jsonp = 'callback';
    }

    options.params = options.params || {};

    options.params[options.jsonp] = callback;

    if (_.isFunction(options.beforeSend)) {
        options.beforeSend({}, options);
    }

    script.async = true;
    script.type  = 'text/javascript';
    script.src   = url(options.url, options.params);

    script.onerror = function() {
        cleanup(reject, '', 404);
    };

    window[callback] = function(data) {
        cleanup(resolve, data, 200);
    };

    // Appending the script to the head makes the request!
    head.appendChild(script);

    function cleanup(fn, data, status) {

        // API call clean-up
        delete window[callback];
        head.removeChild(script);

        // reject / resolve
        fn({ responseText: data, status: status });
    }
}

function parseReq(request) {

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
    beforeSend: null,
    emulateHTTP: false,
    emulateJSON: false
};

Http.headers = {
    put: jsonType,
    post: jsonType,
    patch: jsonType,
    delete: jsonType,
    common: { 'Accept': 'application/json, text/plain, */*' }
};

Http.get = function (url, success, options) {
    return this(url, _.extend({method: 'GET', success: success}, options));
};

Http.put = function (url, data, success, options) {
    return this(url, _.extend({method: 'PUT', data: data, success: success}, options));
};

Http.post = function (url, data, success, options) {
    return this(url, _.extend({method: 'POST', data: data, success: success}, options));
};

Http.patch = function (url, data, success, options) {
    return this(url, _.extend({method: 'PATCH', data: data, success: success}, options));
};

Http.delete = function (url, success, options) {
    return this(url, _.extend({method: 'DELETE', success: success}, options));
};

Http.jsonp = function (url, success, options) {
    return this(url, _.extend({method: 'GET', success: success, jsonp: true}, options));
};

module.exports = Http;
