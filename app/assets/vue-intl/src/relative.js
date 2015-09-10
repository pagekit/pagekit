/**
 * Relative Date and time formatting.
 * Based on: https://github.com/twitter/twitter-cldr-js
 */

module.exports = function (_) {

    var approximate_multiplier = 0.75,
        default_type = "default",
        time_in_seconds = {
            "second": 1,
            "minute": 60,
            "hour": 3600,
            "day": 86400,
            "week": 604800,
            "month": 2629743.83,
            "year": 31556926
        };

    function calculate_unit(seconds, unit_options) {
        var key, multiplier, obj, options;
        if (unit_options == null) {
            unit_options = {};
        }
        options = {};
        for (key in unit_options) {
            obj = unit_options[key];
            options[key] = obj;
        }
        if (options.approximate == null) {
            options["approximate"] = false;
        }
        multiplier = options.approximate ? approximate_multiplier : 1;
        if (seconds < (time_in_seconds.minute * multiplier)) {
            return "second";
        } else if (seconds < (time_in_seconds.hour * multiplier)) {
            return "minute";
        } else if (seconds < (time_in_seconds.day * multiplier)) {
            return "hour";
        } else if (seconds < (time_in_seconds.week * multiplier)) {
            return "day";
        } else if (seconds < (time_in_seconds.month * multiplier)) {
            return "week";
        } else if (seconds < (time_in_seconds.year * multiplier)) {
            return "month";
        } else {
            return "year";
        }
    }

    function calculate_time(seconds, unit) {
        return Math.round(seconds / time_in_seconds[unit]);
    }

    function format(seconds, fmt_options, patterns) {
        var key, number, obj, options;
        if (fmt_options == null) {
            fmt_options = {};
        }
        options = {};
        for (key in fmt_options) {
            obj = fmt_options[key];
            options[key] = obj;
        }
        options["direction"] || (options["direction"] = (seconds < 0 ? "ago" : "until"));
        if (options["unit"] === null || options["unit"] === void 0) {
            options["unit"] = calculate_unit(Math.abs(seconds), options);
        }
        options["type"] || (options["type"] = default_type);
        options["number"] = calculate_time(Math.abs(seconds), options["unit"]);
        number = calculate_time(Math.abs(seconds), options["unit"]);
        options["rule"] = _.pluralCat('de', number);
        return patterns[options["direction"]][options["unit"]][options["type"]][options["rule"]].replace(/\{[0-9]\}/, number.toString());
    }

    return function (date, options) {
        date = date instanceof Date ? date : new Date(date);
        return format((date - new Date()) / 1000, options, this.$locale.TIMESPAN_FORMATS);
    }

};
