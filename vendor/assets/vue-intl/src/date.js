/**
 * Date and time formatting.
 * Based on: https://docs.angularjs.org/api/ng/filter/date
 */

module.exports = function (_) {

    var NUMBER_STRING = /^\-?\d+$/;
    var R_ISO8601_STR = /^(\d{4})-?(\d\d)-?(\d\d)(?:T(\d\d)(?::?(\d\d)(?::?(\d\d)(?:\.(\d+))?)?)?(Z|([+-])(\d\d):?(\d\d))?)?$/;
    var DATE_FORMATS_SPLIT = /((?:[^yMdHhmsaZEwG']+)|(?:'(?:[^']|'')*')|(?:E+|y+|M+|d+|H+|h+|m+|s+|a|Z|G+|w+))(.*)/;
    var DATE_FORMATS = {
        yyyy: dateGetter('FullYear', 4),
        yy: dateGetter('FullYear', 2, 0, true),
        y: dateGetter('FullYear', 1),
        MMMM: dateStrGetter('Month'),
        MMM: dateStrGetter('Month', true),
        MM: dateGetter('Month', 2, 1),
        M: dateGetter('Month', 1, 1),
        dd: dateGetter('Date', 2),
        d: dateGetter('Date', 1),
        HH: dateGetter('Hours', 2),
        H: dateGetter('Hours', 1),
        hh: dateGetter('Hours', 2, -12),
        h: dateGetter('Hours', 1, -12),
        mm: dateGetter('Minutes', 2),
        m: dateGetter('Minutes', 1),
        ss: dateGetter('Seconds', 2),
        s: dateGetter('Seconds', 1),
        sss: dateGetter('Milliseconds', 3),
        EEEE: dateStrGetter('Day'),
        EEE: dateStrGetter('Day', true),
        a: ampmGetter,
        Z: timeZoneGetter,
        ww: weekGetter(2),
        w: weekGetter(1),
        G: eraGetter,
        GG: eraGetter,
        GGG: eraGetter,
        GGGG: longEraGetter
    };

    function padNumber(num, digits, trim) {
        var neg = '';
        if (num < 0) {
            neg = '-';
            num = -num;
        }
        num = '' + num;
        while (num.length < digits) num = '0' + num;
        if (trim) {
            num = num.substr(num.length - digits);
        }
        return neg + num;
    }

    function dateGetter(name, size, offset, trim) {
        offset = offset || 0;
        return function (date) {
            var value = date['get' + name]();
            if (offset > 0 || value > -offset) {
                value += offset;
            }
            if (value === 0 && offset == -12) value = 12;
            return padNumber(value, size, trim);
        };
    }

    function dateStrGetter(name, shortForm) {
        return function (date, formats) {
            var value = date['get' + name]();
            var get = _.uppercase(shortForm ? ('SHORT' + name) : name);

            return formats[get][value];
        };
    }

    function timeZoneGetter(date, formats, offset) {
        var zone = -1 * offset;
        var paddedZone = (zone >= 0) ? "+" : "";

        paddedZone += padNumber(Math[zone > 0 ? 'floor' : 'ceil'](zone / 60), 2) +
            padNumber(Math.abs(zone % 60), 2);

        return paddedZone;
    }

    function getFirstThursdayOfYear(year) {
        // 0 = index of January
        var dayOfWeekOnFirst = (new Date(year, 0, 1)).getDay();
        // 4 = index of Thursday (+1 to account for 1st = 5)
        // 11 = index of *next* Thursday (+1 account for 1st = 12)
        return new Date(year, 0, ((dayOfWeekOnFirst <= 4) ? 5 : 12) - dayOfWeekOnFirst);
    }

    function getThursdayThisWeek(datetime) {
        return new Date(datetime.getFullYear(), datetime.getMonth(),
            // 4 = index of Thursday
            datetime.getDate() + (4 - datetime.getDay()));
    }

    function weekGetter(size) {
        return function (date) {
            var firstThurs = getFirstThursdayOfYear(date.getFullYear()),
                thisThurs = getThursdayThisWeek(date);

            var diff = +thisThurs - +firstThurs,
                result = 1 + Math.round(diff / 6.048e8); // 6.048e8 ms per week

            return padNumber(result, size);
        };
    }

    function ampmGetter(date, formats) {
        return date.getHours() < 12 ? formats.AMPMS[0] : formats.AMPMS[1];
    }

    function eraGetter(date, formats) {
        return date.getFullYear() <= 0 ? formats.ERAS[0] : formats.ERAS[1];
    }

    function longEraGetter(date, formats) {
        return date.getFullYear() <= 0 ? formats.ERANAMES[0] : formats.ERANAMES[1];
    }

    function jsonStringToDate(string) {
        var match;
        if (match = string.match(R_ISO8601_STR)) {
            var date = new Date(0),
                tzHour = 0,
                tzMin = 0,
                dateSetter = match[8] ? date.setUTCFullYear : date.setFullYear,
                timeSetter = match[8] ? date.setUTCHours : date.setHours;

            if (match[9]) {
                tzHour = _.toInt(match[9] + match[10]);
                tzMin = _.toInt(match[9] + match[11]);
            }
            dateSetter.call(date, _.toInt(match[1]), _.toInt(match[2]) - 1, _.toInt(match[3]));
            var h = _.toInt(match[4] || 0) - tzHour;
            var m = _.toInt(match[5] || 0) - tzMin;
            var s = _.toInt(match[6] || 0);
            var ms = Math.round(parseFloat('0.' + (match[7] || 0)) * 1000);
            timeSetter.call(date, h, m, s, ms);
            return date;
        }
        return string;
    }

    function timezoneToOffset(timezone, fallback) {
        var requestedTimezoneOffset = Date.parse('Jan 01, 1970 00:00:00 ' + timezone) / 60000;
        return isNaN(requestedTimezoneOffset) ? fallback : requestedTimezoneOffset;
    }

    function addDateMinutes(date, minutes) {
        date = new Date(date.getTime());
        date.setMinutes(date.getMinutes() + minutes);
        return date;
    }

    function convertTimezoneToLocal(date, timezone, reverse) {
        reverse = reverse ? -1 : 1;
        var timezoneOffset = timezoneToOffset(timezone, date.getTimezoneOffset());
        return addDateMinutes(date, reverse * (timezoneOffset - date.getTimezoneOffset()));
    }

    return function (date, format, timezone) {

        var text = '',
            parts = [],
            formats = this.$locale.DATETIME_FORMATS,
            fn, match;

        format = format || 'mediumDate';
        format = formats[format] || format;

        if (_.isString(date)) {
            date = NUMBER_STRING.test(date) ? _.toInt(date) : jsonStringToDate(date);
        }

        if (_.isNumber(date)) {
            date = new Date(date);
        }

        if (!_.isDate(date) || !isFinite(date.getTime())) {
            return date;
        }

        while (format) {
            match = DATE_FORMATS_SPLIT.exec(format);
            if (match) {
                parts = _.concat(parts, match, 1);
                format = parts.pop();
            } else {
                parts.push(format);
                format = null;
            }
        }

        var dateTimezoneOffset = date.getTimezoneOffset();

        if (timezone) {
            dateTimezoneOffset = timezoneToOffset(timezone, date.getTimezoneOffset());
            date = convertTimezoneToLocal(date, timezone, true);
        }

        parts.forEach(function (value) {
            fn = DATE_FORMATS[value];
            text += fn ? fn(date, formats, dateTimezoneOffset) : value.replace(/(^'|'$)/g, '').replace(/''/g, "'");
        });

        return text;
    };

};
