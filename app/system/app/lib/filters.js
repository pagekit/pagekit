var $ = require('jquery');
var _ = require('lodash');

module.exports = function (Vue) {

    Vue.filter('baseUrl', function(url) {
        return _.startsWith(url, Vue.url.options.root) ? url.substr(Vue.url.options.root.length) : url;
    });

    Vue.filter('trans', function(id, parameters, domain, locale) {
        return this.$trans(id, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
    });

    Vue.filter('transChoice', function(id, number, parameters, domain, locale) {
        return this.$transChoice(id, evalExp.call(this, number) || 0, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
    });

    Vue.filter('date', function(date, format) {
        return this.$date(date, evalExp.call(this, format));
    });

    Vue.filter('toOptions', function toOptions(collection) {
        return collection ? Object.keys(collection).map(function (key) {

            var op = collection[key];
            if (typeof op === 'string') {
                return { text: op, value: key };
            } else {
                return { label: key, options: toOptions(op) };
            }

        }) : [];
    });

    Vue.filter('trim', {

        write: function (val) {
            return val.trim();
        }

    });

    Vue.filter('relativeDate', function(value, reference) {

        var SECOND = 1000,
            MINUTE = 60 * SECOND,
            HOUR = 60 * MINUTE,
            DAY = 24 * HOUR,
            WEEK = 7 * DAY,
            YEAR = DAY * 365,
            MONTH = YEAR / 12;

            var formats = [

                [ 1.5 * MINUTE, 'minute',MINUTE ],
                [ 60 * MINUTE, 'minute', MINUTE ],
                [ DAY, 'hour', HOUR ],
                [ 7 * DAY, 'day', DAY ],
                [ MONTH, 'week', WEEK ]

            ],formatter;

        if (typeof(value)) value = new Date(value);
        if (!reference) reference = (new Date).getTime();
        if (reference instanceof Date) reference = reference.getTime();
        if (value instanceof Date) value = value.getTime();

        var delta = reference - value, format, i, len;

        for (i = -1, len=formats.length; ++i < len; ){

            format = formats[i];

            if (delta < format[0]){

                formatter = Globalize.relativeTimeFormatter( format[1] );

                return formatter(Math.round(delta/format[2])*-1);
            }
        }

        return this.$date((new Date(value)).toISOString(), 'medium');
    });

    var evalExp = function(expression) {

        try {

            return undefined === expression ? expression : Vue.parsers.expression.parse(expression).get.call(this, this);

        } catch (e) {

            if (Vue.util.warn && Vue.config.warnExpressionErrors) {
                Vue.util.warn('Error when evaluating expression "' + expression + '":\n   ' + e);
            }
        }

    };

};
