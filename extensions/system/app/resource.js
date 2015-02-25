(function($) {

    var Resource = function(url, params, actions, options) {

        var self = this;

        $.extend(true, this, Resource.defaults, {actions: actions, options: options});

        $.each(this.actions, function(name, action) {

            action = $.extend(true, {url: url, params: params || {}}, action);

            self[name] = function() {
                return $.ajax(getOptions(action, arguments));
            };

        });


        function getOptions(action, args) {

            var options = $.extend({headers: {}, dataType: 'json', contentType: 'application/json;charset=utf-8'}, action), params = {}, data, success, error;

            switch (args.length) {

              case 4:

                error = args[3];
                success = args[2];

              case 3:
              case 2:

                if ($.isFunction(args[1])) {

                  if ($.isFunction(args[0])) {

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

                if ($.isFunction(args[0])) {
                    success = args[0];
                } else if (/^(POST|PUT|PATCH)$/i.test(options.type)) {
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

            options.url = getUrl(action, params);

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

            return options;
        }

        function getUrl(action, params) {

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
                    url = url.replace(new RegExp(':' + urlParam + '(\\W|$)', 'g'), function(match, p1) {
                        return encodeUriSegment(val) + p1;
                    });
                } else {
                    url = url.replace(new RegExp('(\/?):' + urlParam + '(\\W|$)', 'g'), function(match, leadingSlashes, tail) {
                        return tail.charAt(0) == '/' ? tail : leadingSlashes + tail;
                    });
                }

            });

            if (self.options.stripTrailingSlashes) {
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

        function encodeUriSegment(val) {
            return encodeUriQuery(val, true).
            replace(/%26/gi, '&').
            replace(/%3D/gi, '=').
            replace(/%2B/gi, '+');
        }

        function encodeUriQuery(val, spaces) {
            return encodeURIComponent(val).
            replace(/%40/gi, '@').
            replace(/%3A/gi, ':').
            replace(/%24/g, '$').
            replace(/%2C/gi, ',').
            replace(/%20/g, (spaces ? '%20' : '+'));
        }

    };

    Resource.defaults = {

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

    $.resource = function(url, params, actions, options) {
        return new Resource(url, params, actions, options);
    };

    $.resource.defaults = Resource.defaults;

})(jQuery);