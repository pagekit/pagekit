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
            params: {},
            useMethodOverride: false,
            stripTrailingSlashes: true
        };

        var resource = $.extend(true, {}, defaults, options, {url: url, actions: actions, params: params});

        $.each(resource.actions, function(name, action) {

            var hasBody = /^(POST|PUT|PATCH)$/i.test(action.type);

            resource[name] = function(a1, a2, a3, a4) {

                var options = $.extend({dataType: 'json', contentType: 'application/json;charset=utf-8'}, action), params = {}, data, success, error;

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

                if (data) {
                    options.data = JSON.stringify(data);
                }

                if (success) {
                    options.success = success;
                }

                if (error) {
                    options.error = error;
                }

                return $.ajax(getUrl(resource, params), options);
            };
        });

        return resource;
    };

    function getUrl(resource, params) {

        var url = resource.url, urlParams = {}, query = {}, val;

        $.each(url.split(/\W/), function(i, param) {
            if (!(new RegExp("^\\d+$").test(param)) && param && (new RegExp("(^|[^\\\\]):" + param + "(\\W|$)").test(url))) {
                urlParams[param] = true;
            }
        });

        url = url.replace(/\\:/g, ':');

        $.each(urlParams, function(urlParam) {

            val = params.hasOwnProperty(urlParam) ? params[urlParam] : resource.params[urlParam];

            if ($.type(val) !== 'undefined' && val !== null) {
                url = url.replace(new RegExp(":" + urlParam + "(\\W|$)", "g"), function(match, p1) {
                    return val + p1;
                });
            } else {
                url = url.replace(new RegExp("(\/?):" + urlParam + "(\\W|$)", "g"), function(match, leadingSlashes, tail) {
                    return tail.charAt(0) == '/' ? tail : leadingSlashes + tail;
                });
            }

        });

        if (resource.stripTrailingSlashes) {
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