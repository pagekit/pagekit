(function($) {

    $.resource = function (url, params, actions, options) {

        var defaults = {
            actions: {
                'get': {type: 'GET'},
                'save': {type: 'POST'},
                'query': {type: 'GET'},
                'remove': {type: 'DELETE'},
                'delete': {type: 'DELETE'}
            },
            options: {
                useMethodOverride: false,
                stripTrailingSlashes: true
            }
        };

        var self = $.extend(true, {}, defaults, {actions: actions, options: options});

        $.each(self.actions, function(name, action) {

            action = $.extend(true, {url: url, params: params || {}}, action);

            var hasBody = /^(POST|PUT|PATCH)$/i.test(action.type);

            self[name] = function(a1, a2, a3, a4) {

                var options = {}, params = {}, data, success, error;

                switch (arguments.length) {

                  case 4:

                    error = a4;
                    success = a3;

                  case 3:
                  case 2:

                    if ($.isFunction(a2)) {

                      if ($.isFunction(a1)) {

                        success = a1;
                        error = a2;

                        break;
                      }

                      success = a2;
                      error = a3;

                    } else {

                        params = a1;
                        data = a2;
                        success = a3;

                        break;
                    }

                  case 1:

                    if ($.isFunction(a1)) success = a1;
                    else if (hasBody) data = a1;
                    else params = a1;

                    break;

                  case 0:

                    break;

                  default:

                    throw "Expected up to 4 arguments [params, data, success, error], got " + arguments.length + " arguments";
                }

                $.extend(options, {headers: {}, dataType: 'json', contentType: 'application/json;charset=utf-8'}, action);

                if (data) {
                    options.data = JSON.stringify(data);
                }

                if (success) {
                    options.success = success;
                }

                if (error) {
                    options.error = error;
                }

                if (self.options.useMethodOverride && /^(PUT|PATCH|DELETE)$/i.test(options.type)) {
                    options.headers['X-HTTP-Method-Override'] = options.type;
                    options.type = 'POST';
                }

                return $.ajax(getUrl(action, params, self.options), options);
            };
        });

        console.log(self);

        return self;
    };

    function getUrl(action, params, options) {

        var url = action.url, urlParams = {}, query = {}, val;

        $.each(url.split(/\W/), function(i, param) {
            if (!(new RegExp("^\\d+$").test(param)) && param && (new RegExp("(^|[^\\\\]):" + param + "(\\W|$)").test(url))) {
                urlParams[param] = true;
            }
        });

        url = url.replace(/\\:/g, ':');

        $.each(urlParams, function(urlParam) {

            val = params.hasOwnProperty(urlParam) ? params[urlParam] : action.params[urlParam];

            if (typeof val !== 'undefined' && val !== null) {
                url = url.replace(new RegExp(":" + urlParam + "(\\W|$)", "g"), function(match, p1) {
                    return val + p1;
                });
            } else {
                url = url.replace(new RegExp("(\/?):" + urlParam + "(\\W|$)", "g"), function(match, leadingSlashes, tail) {
                    return tail.charAt(0) == '/' ? tail : leadingSlashes + tail;
                });
            }

        });

        if (options.stripTrailingSlashes) {
            url = url.replace(/\/+$/, '') || '/';
        }

        $.each(params, function(key, value) {
            if (!urlParams[key]) {
                query[key] = value;
            }
        });

        if (Object.keys(query).length) {
            url += (url.indexOf('?') >= 0 ? '&' : '?') + $.param(query);
        }

        return url;
    }

})(jQuery);