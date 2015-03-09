(function($) {

    var config = $.extend({}, pagekit), templates = {};

    window.System = {

        version: config.version,

        url: function(url, params, isStatic) {

            var baseUrl = config.url;

            if (params === true) {
                params = {}; isStatic = true;
            }

            if (isStatic === true) {
                baseUrl = baseUrl.replace(/\/index.php$/i, '');
            }

            return Url.get(url, params, { baseUrl: baseUrl });
        },

        resource: function(url, params, actions, options) {
            return new Resource(url, params, actions, options);
        },

        template: function(name) {

            if (!templates[name]) {
                templates[name] = $.get(this.url('system/tmpl/:template', { template: name } ));
            }

            return templates[name];
        },

        loadLanguage: function(locale) {
            return $.getJSON(this.url('admin/system/locale', { locale: locale }, function(data) {
                data.locale = locale;
                Locale.Translator.fromJSON(data);
            }));
        }

    };

    $(function(){
        Resource.defaults.options.baseUrl = config.url;
    });

    $(document).on('ajaxSend', function(e, xhr){
        xhr.setRequestHeader('X-XSRF-TOKEN', config.csrf);
    });


    /**
     * The Resource provides interaction support with RESTful services
     */

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

            options.url = Url.get(action.url, $.extend({}, action.params, params), {baseUrl: self.options.baseUrl});

            if (data) {
                options.data = (typeof data === 'object') ? JSON.stringify(data) : data;
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
            baseUrl: '',
            useMethodOverride: false
        }

    };


    /**
     * The Url provides URL templating
     */

    var Url = {

        get: function(url, params, options) {

            var self = this, urlParams = {}, query = {}, val;

            params  = params || {};
            options = $.extend({baseUrl: ''}, options);

            $.each(url.split(/\W/), function(i, param) {
                if (!(new RegExp("^\\d+$").test(param)) && param && (new RegExp("(^|[^\\\\]):" + param + "(\\W|$)").test(url))) {
                    urlParams[param] = true;
                }
            });

            url = url.replace(/\\:/g, ':');

            $.each(urlParams, function(urlParam) {

                val = params[urlParam];

                if (typeof val !== 'undefined' && val !== null) {
                    url = url.replace(new RegExp(':' + urlParam + '(\\W|$)', 'g'), function(match, part) {
                        return self.encodeUriSegment(val) + part;
                    });
                } else {
                    url = url.replace(new RegExp('(\/?):' + urlParam + '(\\W|$)', 'g'), function(match, slashes, tail) {
                        return (tail.charAt(0) == '/') ? tail : slashes + tail;
                    });
                }

            });

            url = url.replace(/\/+$/, '') || '/';

            if (!url.match(/^(https?:)?\//)) {
                url = options.baseUrl + '/' + url;
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
        },

        parse: function(url) {

            var pattern = RegExp("^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\\?([^#]*))?(?:#(.*))?"), matches = url.match(pattern);

            return {
                url: url,
                scheme: matches[1] || '',
                host: matches[2] || '',
                path: matches[3] || '',
                query: matches[4] || '',
                fragment: matches[5] || ''
            };
        },

        encodeUriSegment: function(val) {

            return this.encodeUriQuery(val, true).
                replace(/%26/gi, '&').
                replace(/%3D/gi, '=').
                replace(/%2B/gi, '+');
        },

        encodeUriQuery: function(val, spaces) {

            return encodeURIComponent(val).
                replace(/%40/gi, '@').
                replace(/%3A/gi, ':').
                replace(/%24/g, '$').
                replace(/%2C/gi, ',').
                replace(/%20/g, (spaces ? '%20' : '+'));
        }

    };

})(jQuery);
