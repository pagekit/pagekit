define(['jquery', 'module'], function ($, mod) {

    var system = $('meta[name="generator"]').data(), locale = {};

    system.config = mod.config();

    system.path = system.url.replace(/\/index.php$/i, '');

    system.csrf = {token: system.csrf, param: '_csrf', params: {'_csrf': system.csrf}};

    system.trans = function(id, parameters, domain){

        if (parameters === undefined) {
            parameters = {};
        }

        if (domain === undefined) {
            domain = 'messages';
        }

        if (locale[domain] && locale[domain][id]) {
            id = locale[domain][id];
        }

        return strtr(id, parameters);
    };

    system.date = function(format, timestamp){

        if (locale.date[format]) {
            format = locale.date[format];
        }

        if (typeof timestamp === 'string') {
            timestamp = new Date(timestamp);
        }

        return date(format, timestamp);
    };

    system.load = function(res, req, onload) {

        var load = [], index;

        $.each(res.split(','), function(i, name) {
            if (name == 'locale') {
                index = i;
                load.push(system.config.locale);
            } else {
                load.push(system.config.base + name);
            }
        });

        req(load, function() {

            if (index !== undefined) {
                locale = arguments[index];
            }

            onload(system);
        });
    };

    return system;

    /**
     * Copyright (c) 2013 Kevin van Zonneveld (http://kvz.io) and Contributors (http://phpjs.org/authors)
     */

    function strtr(str, from, to) {

        var fr = '',
            i = 0,
            j = 0,
            lenStr = 0,
            lenFrom = 0,
            tmpStrictForIn = false,
            fromTypeStr = '',
            toTypeStr = '',
            istr = '';
        var tmpFrom = [];
        var tmpTo = [];
        var ret = '';
        var match = false;

        // Received replace_pairs?
        // Convert to normal from->to chars
        if (typeof from === 'object') {
            for (fr in from) {
                if (from.hasOwnProperty(fr)) {
                    tmpFrom.push(fr);
                    tmpTo.push(from[fr]);
                }
            }
            from = tmpFrom;
            to = tmpTo;
        }

        // Walk through subject and replace chars when needed
        lenStr = str.length;
        lenFrom = from.length;
        fromTypeStr = typeof from === 'string';
        toTypeStr = typeof to === 'string';

        for (i = 0; i < lenStr; i++) {
            match = false;
            if (fromTypeStr) {
                istr = str.charAt(i);
                for (j = 0; j < lenFrom; j++) {
                    if (istr == from.charAt(j)) {
                        match = true;
                        break;
                    }
                }
            } else {
                for (j = 0; j < lenFrom; j++) {
                    if (str.substr(i, from[j].length) == from[j]) {
                        match = true;
                        // Fast forward
                        i = (i + from[j].length) - 1;
                        break;
                    }
                }
            }
            if (match) {
                ret += toTypeStr ? to.charAt(j) : to[j];
            } else {
                ret += str.charAt(i);
            }
        }

        return ret;
    }

    function date(format, timestamp) {

        var that = this;
        var jsdate, f;

        // trailing backslash -> (dropped)
        // a backslash followed by any character (including backslash) -> the character
        // empty string -> empty string
        var formatChr = /\\?(.?)/gi;
        var formatChrCb = function (t, s) {
            return f[t] ? f[t]() : s;
        };
        var _pad = function (n, c) {
            n = String(n);
            while (n.length < c) {
                n = '0' + n;
            }
            return n;
        };
        f = {
            // Day
            d: function () {
                // Day of month w/leading 0; 01..31
                return _pad(f.j(), 2);
            },
            D: function () {
                // Shorthand day name; Mon...Sun
                return locale.date.shortdays[f.w()-1];
            },
            j: function () {
                // Day of month; 1..31
                return jsdate.getDate();
            },
            l: function () {
                // Full day name; Monday...Sunday
                return locale.date.longdays[f.w()-1];
            },
            N: function () {
                // ISO-8601 day of week; 1[Mon]..7[Sun]
                return f.w() || 7;
            },
            S: function () {
                // Ordinal suffix for day of month; st, nd, rd, th
                var j = f.j();
                var i = j % 10;
                if (i <= 3 && parseInt((j % 100) / 10, 10) == 1) {
                    i = 0;
                }
                return ['st', 'nd', 'rd'][i - 1] || 'th';
            },
            w: function () {
                // Day of week; 0[Sun]..6[Sat]
                return jsdate.getDay();
            },
            z: function () {
                // Day of year; 0..365
                var a = new Date(f.Y(), f.n() - 1, f.j());
                var b = new Date(f.Y(), 0, 1);
                return Math.round((a - b) / 864e5);
            },

            // Week
            W: function () {
                // ISO-8601 week number
                var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3);
                var b = new Date(a.getFullYear(), 0, 4);
                return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
            },

            // Month
            F: function () {
                // Full month name; January...December
                return locale.date.longmonths[f.n() - 1];
            },
            m: function () {
                // Month w/leading 0; 01...12
                return _pad(f.n(), 2);
            },
            M: function () {
                // Shorthand month name; Jan...Dec
                return locale.date.shortmonths[f.n() - 1];
            },
            n: function () {
                // Month; 1...12
                return jsdate.getMonth() + 1;
            },
            t: function () {
                // Days in month; 28...31
                return (new Date(f.Y(), f.n(), 0)).getDate();
            },

            // Year
            L: function () {
                // Is leap year?; 0 or 1
                var j = f.Y();
                return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0;
            },
            o: function () {
                // ISO-8601 year
                var n = f.n();
                var W = f.W();
                var Y = f.Y();
                return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
            },
            Y: function () {
                // Full year; e.g. 1980...2010
                return jsdate.getFullYear();
            },
            y: function () {
                // Last two digits of year; 00...99
                return f.Y().toString().slice(-2);
            },

            // Time
            a: function () {
                // am or pm
                return jsdate.getHours() > 11 ? 'pm' : 'am';
            },
            A: function () {
                // AM or PM
                return f.a().toUpperCase();
            },
            B: function () {
                // Swatch Internet time; 000..999
                var H = jsdate.getUTCHours() * 36e2;
                // Hours
                var i = jsdate.getUTCMinutes() * 60;
                // Minutes
                // Seconds
                var s = jsdate.getUTCSeconds();
                return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
            },
            g: function () {
                // 12-Hours; 1..12
                return f.G() % 12 || 12;
            },
            G: function () {
                // 24-Hours; 0..23
                return jsdate.getHours();
            },
            h: function () {
                // 12-Hours w/leading 0; 01..12
                return _pad(f.g(), 2);
            },
            H: function () {
                // 24-Hours w/leading 0; 00..23
                return _pad(f.G(), 2);
            },
            i: function () {
                // Minutes w/leading 0; 00..59
                return _pad(jsdate.getMinutes(), 2);
            },
            s: function () {
                // Seconds w/leading 0; 00..59
                return _pad(jsdate.getSeconds(), 2);
            },
            u: function () {
                // Microseconds; 000000-999000
                return _pad(jsdate.getMilliseconds() * 1000, 6);
            },

            // Timezone
            e: function () {
                // Timezone identifier; e.g. Atlantic/Azores, ...
                // The following works, but requires inclusion of the very large
                // timezone_abbreviations_list() function.
                /*              return that.date_default_timezone_get();
                */
                throw 'Not supported (see source code of date() for timezone on how to add support)';
            },
            I: function () {
                // DST observed?; 0 or 1
                // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
                // If they are not equal, then DST is observed.
                var a = new Date(f.Y(), 0);
                // Jan 1
                var c = Date.UTC(f.Y(), 0);
                // Jan 1 UTC
                var b = new Date(f.Y(), 6);
                // Jul 1
                // Jul 1 UTC
                var d = Date.UTC(f.Y(), 6);
                return ((a - c) !== (b - d)) ? 1 : 0;
            },
            O: function () {
                // Difference to GMT in hour format; e.g. +0200
                var tzo = jsdate.getTimezoneOffset();
                var a = Math.abs(tzo);
                return (tzo > 0 ? '-' : '+') + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
            },
            P: function () {
                // Difference to GMT w/colon; e.g. +02:00
                var O = f.O();
                return (O.substr(0, 3) + ':' + O.substr(3, 2));
            },
            T: function () {
                return 'UTC';
            },
            Z: function () {
                // Timezone offset in seconds (-43200...50400)
                return -jsdate.getTimezoneOffset() * 60;
            },

            // Full Date/Time
            c: function () {
                // ISO-8601 date.
                return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb);
            },
            r: function () {
                // RFC 2822
                return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
            },
            U: function () {
                // Seconds since UNIX epoch
                return jsdate / 1000 | 0;
            }
        };

        this.date = function (format, timestamp) {
            that = this;
            jsdate = (timestamp === undefined ? new Date() : // Not provided
            (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
            new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
            );
            return format.replace(formatChr, formatChrCb);
        };

        return this.date(format, timestamp);
    }

});