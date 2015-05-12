var _ = require('./util');
var jsonType = { 'Content-Type': 'application/json;charset=utf-8' };

/**
 * Http provides a service for sending XMLHttpRequests.
 */

function Http (url, options) {

    var headers = Http.headers, request = new XMLHttpRequest();

    if (_.isPlainObject(url)) {
        options = url;
        url = '';
    }

    headers = _.extend({},
        headers.common,
        headers[options.method.toLowerCase()]
    );

    options = _.extend(true, {url: url, headers: headers},
        Http.options, _.options('http', this, options)
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
        options.data = Vue.url.params(options.data);
    }

    if (_.isPlainObject(options.data)) {
        options.data = JSON.stringify(options.data);
    }

    var self = this, promise = new _.Promise(function (resolve, reject) {

        request.open(options.method, (self.$url || Vue.url)(options), true);

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

module.exports = Http;
