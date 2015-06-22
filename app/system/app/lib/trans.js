module.exports = function (Vue) {

    var formats = ['full', 'long', 'medium', 'short'];

    Vue.prototype.$date = function (date, format) {

        var options = format;

        if (typeof date == 'string') {
            date = new Date(date);
        }

        if (typeof options == 'string') {
            if (formats.indexOf(format) != -1) {
                options = {date: format};
            } else {
                options = {skeleton: format};
            }
        }

        return Globalize.formatDate(date, options);
    };

    Vue.prototype.$relativeDate = function (value, reference) {
        var SECOND = 1000,
            MINUTE = 60 * SECOND,
            HOUR = 60 * MINUTE,
            DAY = 24 * HOUR,
            WEEK = 7 * DAY,
            YEAR = DAY * 365,
            MONTH = YEAR / 12;

        var formats = [

            [1.5 * MINUTE, 'minute', MINUTE],
            [60 * MINUTE, 'minute', MINUTE],
            [DAY, 'hour', HOUR],
            [7 * DAY, 'day', DAY],
            [MONTH, 'week', WEEK]

        ], formatter;

        if (typeof(value)) value = new Date(value);
        if (!reference) reference = (new Date).getTime();
        if (reference instanceof Date) reference = reference.getTime();
        if (value instanceof Date) value = value.getTime();

        var delta = reference - value, format, i, len;

        for (i = -1, len = formats.length; ++i < len;) {

            format = formats[i];

            if (delta < format[0]) {

                formatter = Globalize.relativeTimeFormatter(format[1]);

                return formatter(Math.round(delta / format[2]) * -1);
            }
        }

        return this.$date((new Date(value)).toISOString(), 'medium');
    };

    Vue.prototype.$trans = Globalize.trans;
    Vue.prototype.$transChoice = Globalize.transChoice;

};
