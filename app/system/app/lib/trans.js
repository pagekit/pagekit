module.exports = function (Vue) {

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

        if (delta <  MINUTE) {
            return this.$trans('Just now');
        }

        for (i = -1, len = formats.length; ++i < len;) {

            format = formats[i];

            if (delta < format[0]) {

                formatter = Globalize.relativeTimeFormatter(format[1]);

                return formatter(Math.round(delta / format[2]) * -1);
            }
        }

        return this.$date(value);
    };

    Vue.prototype.$trans = Globalize.trans;
    Vue.prototype.$transChoice = Globalize.transChoice;

};
