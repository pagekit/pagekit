(function(window) {

    var config = window.$locale || { translations: {}, formats: {}};

    /**
     * Copyright (c) William DURAND <william.durand1@gmail.com> (https://github.com/willdurand/BazingaJsTranslationBundle)
     */

    var Translator = (function(document, undefined) {

        "use strict";

        var _messages     = {},
            _domains      = [],
            _sPluralRegex = new RegExp(/^\w+\: +(.+)$/),
            _cPluralRegex = new RegExp(/^\s*((\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]]))\s?(.+?)$/),
            _iPluralRegex = new RegExp(/^\s*(\{\s*(\-?\d+[\s*,\s*\-?\d+]*)\s*\})|([\[\]])\s*(-Inf|\-?\d+)\s*,\s*(\+?Inf|\-?\d+)\s*([\[\]])/);

        /**
         * Replace placeholders in given message.
         *
         * **WARNING:** used placeholders are removed.
         *
         * @param {String} message      The translated message
         * @param {Object} placeholders The placeholders to replace
         * @return {String}             A human readable message
         * @api private
         */
        function replace_placeholders(message, placeholders) {
            var _i,
                _prefix = Translator.placeHolderPrefix,
                _suffix = Translator.placeHolderSuffix;

            for (_i in placeholders) {
                var _r = new RegExp(_prefix + _i + _suffix, 'g');

                if (_r.test(message)) {
                    message = message.replace(_r, placeholders[_i]);
                }
            }

            return message;
        }

        /**
         * Get the message based on its id, its domain, and its locale. If domain or
         * locale are not specified, it will try to find the message using fallbacks.
         *
         * @param {String} id               The message id
         * @param {String} domain           The domain for the message or null to guess it
         * @param {String} locale           The locale or null to use the default
         * @param {String} currentLocale    The current locale or null to use the default
         * @param {String} localeFallback   The fallback (default) locale
         * @return {String}                 The right message if found, `undefined` otherwise
         * @api private
         */
        function get_message(id, domain, locale, currentLocale, localeFallback) {
            var _locale = locale || currentLocale || localeFallback,
                _domain = domain;

            if (undefined == _messages[_locale]) {
                if (undefined == _messages[localeFallback]) {
                    return id;
                }

                _locale = localeFallback;
            }

            if (undefined === _domain || null === _domain) {
                for (var i = 0; i < _domains.length; i++) {
                    if (has_message(_locale, _domains[i], id) ||
                        has_message(localeFallback, _domains[i], id)) {
                        _domain = _domains[i];

                        break;
                    }
                }
            }

            if (has_message(_locale, _domain, id)) {
                return _messages[_locale][_domain][id];
            }

            var _length, _parts, _last, _lastLength;

            while (_locale.length > 2) {
                _length     = _locale.length;
                _parts      = _locale.split(/[\s_]+/);
                _last       = _parts[_parts.length - 1];
                _lastLength = _last.length;

                if (1 === _parts.length) {
                    break;
                }

                _locale = _locale.substring(0, _length - (_lastLength + 1));

                if (has_message(_locale, _domain, id)) {
                    return _messages[_locale][_domain][id];
                }
            }

            if (has_message(localeFallback, _domain, id)) {
                return _messages[localeFallback][_domain][id];
            }

            return id;
        }

        /**
         * Just look for a specific locale / domain / id if the message is available,
         * helpfull for message availability validation
         *
         * @param {String} locale           The locale
         * @param {String} domain           The domain for the message
         * @param {String} id               The message id
         * @return {Boolean}                Return `true` if message is available,
         *                      `               false` otherwise
         * @api private
         */
        function has_message(locale, domain, id) {
            if (undefined == _messages[locale]) {
                return false;
            }

            if (undefined == _messages[locale][domain]) {
                return false;
            }

            if (undefined == _messages[locale][domain][id]) {
                return false;
            }

            return true;
        }

        /**
         * The logic comes from the Symfony2 PHP Framework.
         *
         * Given a message with different plural translations separated by a
         * pipe (|), this method returns the correct portion of the message based
         * on the given number, the current locale and the pluralization rules
         * in the message itself.
         *
         * The message supports two different types of pluralization rules:
         *
         * interval: {0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples
         * indexed:  There is one apple|There is %count% apples
         *
         * The indexed solution can also contain labels (e.g. one: There is one apple).
         * This is purely for making the translations more clear - it does not
         * affect the functionality.
         *
         * The two methods can also be mixed:
         *     {0} There is no apples|one: There is one apple|more: There is %count% apples
         *
         * @param {String} message  The message id
         * @param {Number} number   The number to use to find the indice of the message
         * @param {String} locale   The locale
         * @return {String}         The message part to use for translation
         * @api private
         */
        function pluralize(message, number, locale) {
            var _p,
                _e,
                _explicitRules = [],
                _standardRules = [],
                _parts         = message.split(Translator.pluralSeparator),
                _matches       = [];

            for (_p = 0; _p < _parts.length; _p++) {
                var _part = _parts[_p];

                if (_cPluralRegex.test(_part)) {
                    _matches = _part.match(_cPluralRegex);
                    _explicitRules[_matches[0]] = _matches[_matches.length - 1];
                } else if (_sPluralRegex.test(_part)) {
                    _matches = _part.match(_sPluralRegex);
                    _standardRules.push(_matches[1]);
                } else {
                    _standardRules.push(_part);
                }
            }

            for (_e in _explicitRules) {
                if (_iPluralRegex.test(_e)) {
                    _matches = _e.match(_iPluralRegex);

                    if (_matches[1]) {
                        var _ns = _matches[2].split(','),
                            _n;

                        for (_n in _ns) {
                            if (number == _ns[_n]) {
                                return _explicitRules[_e];
                            }
                        }
                    } else {
                        var _leftNumber  = convert_number(_matches[4]);
                        var _rightNumber = convert_number(_matches[5]);

                        if (('[' === _matches[3] ? number >= _leftNumber : number > _leftNumber) &&
                            (']' === _matches[6] ? number <= _rightNumber : number < _rightNumber)) {
                            return _explicitRules[_e];
                        }
                    }
                }
            }

            return _standardRules[plural_position(number, locale)] || _standardRules[0] || undefined;
        }

        /**
         * The logic comes from the Symfony2 PHP Framework.
         *
         * Convert number as String, "Inf" and "-Inf"
         * values to number values.
         *
         * @param {String} number   A litteral number
         * @return {Number}         The int value of the number
         * @api private
         */
        function convert_number(number) {
            if ('-Inf' === number) {
                return Number.NEGATIVE_INFINITY;
            } else if ('+Inf' === number || 'Inf' === number) {
                return Number.POSITIVE_INFINITY;
            }

            return parseInt(number, 10);
        }

        /**
         * The logic comes from the Symfony2 PHP Framework.
         *
         * Returns the plural position to use for the given locale and number.
         *
         * @param {Number} number  The number to use to find the indice of the message
         * @param {String} locale  The locale
         * @return {Number}        The plural position
         * @api private
         */
        function plural_position(number, locale) {
            var _locale = locale;

            if ('pt_BR' === _locale) {
                _locale = 'xbr';
            }

            if (_locale.length > 3) {
                _locale = _locale.split('_')[0];
            }

            switch (_locale) {
                case 'bo':
                case 'dz':
                case 'id':
                case 'ja':
                case 'jv':
                case 'ka':
                case 'km':
                case 'kn':
                case 'ko':
                case 'ms':
                case 'th':
                case 'tr':
                case 'vi':
                case 'zh':
                    return 0;
                case 'af':
                case 'az':
                case 'bn':
                case 'bg':
                case 'ca':
                case 'da':
                case 'de':
                case 'el':
                case 'en':
                case 'eo':
                case 'es':
                case 'et':
                case 'eu':
                case 'fa':
                case 'fi':
                case 'fo':
                case 'fur':
                case 'fy':
                case 'gl':
                case 'gu':
                case 'ha':
                case 'he':
                case 'hu':
                case 'is':
                case 'it':
                case 'ku':
                case 'lb':
                case 'ml':
                case 'mn':
                case 'mr':
                case 'nah':
                case 'nb':
                case 'ne':
                case 'nl':
                case 'nn':
                case 'no':
                case 'om':
                case 'or':
                case 'pa':
                case 'pap':
                case 'ps':
                case 'pt':
                case 'so':
                case 'sq':
                case 'sv':
                case 'sw':
                case 'ta':
                case 'te':
                case 'tk':
                case 'ur':
                case 'zu':
                    return (number == 1) ? 0 : 1;

                case 'am':
                case 'bh':
                case 'fil':
                case 'fr':
                case 'gun':
                case 'hi':
                case 'ln':
                case 'mg':
                case 'nso':
                case 'xbr':
                case 'ti':
                case 'wa':
                    return ((number === 0) || (number == 1)) ? 0 : 1;

                case 'be':
                case 'bs':
                case 'hr':
                case 'ru':
                case 'sr':
                case 'uk':
                    return ((number % 10 == 1) && (number % 100 != 11)) ? 0 : (((number % 10 >= 2) && (number % 10 <= 4) && ((number % 100 < 10) || (number % 100 >= 20))) ? 1 : 2);

                case 'cs':
                case 'sk':
                    return (number == 1) ? 0 : (((number >= 2) && (number <= 4)) ? 1 : 2);

                case 'ga':
                    return (number == 1) ? 0 : ((number == 2) ? 1 : 2);

                case 'lt':
                    return ((number % 10 == 1) && (number % 100 != 11)) ? 0 : (((number % 10 >= 2) && ((number % 100 < 10) || (number % 100 >= 20))) ? 1 : 2);

                case 'sl':
                    return (number % 100 == 1) ? 0 : ((number % 100 == 2) ? 1 : (((number % 100 == 3) || (number % 100 == 4)) ? 2 : 3));

                case 'mk':
                    return (number % 10 == 1) ? 0 : 1;

                case 'mt':
                    return (number == 1) ? 0 : (((number === 0) || ((number % 100 > 1) && (number % 100 < 11))) ? 1 : (((number % 100 > 10) && (number % 100 < 20)) ? 2 : 3));

                case 'lv':
                    return (number === 0) ? 0 : (((number % 10 == 1) && (number % 100 != 11)) ? 1 : 2);

                case 'pl':
                    return (number == 1) ? 0 : (((number % 10 >= 2) && (number % 10 <= 4) && ((number % 100 < 12) || (number % 100 > 14))) ? 1 : 2);

                case 'cy':
                    return (number == 1) ? 0 : ((number == 2) ? 1 : (((number == 8) || (number == 11)) ? 2 : 3));

                case 'ro':
                    return (number == 1) ? 0 : (((number === 0) || ((number % 100 > 0) && (number % 100 < 20))) ? 1 : 2);

                case 'ar':
                    return (number === 0) ? 0 : ((number == 1) ? 1 : ((number == 2) ? 2 : (((number >= 3) && (number <= 10)) ? 3 : (((number >= 11) && (number <= 99)) ? 4 : 5))));

                default:
                    return 0;
            }
        }

        /**
         * @type {Array}        An array
         * @type {String}       An element to compare
         * @return {Boolean}    Return `true` if `array` contains `element`,
         *                      `false` otherwise
         * @api private
         */
        function exists(array, element) {
            for (var i = 0; i < array.length; i++) {
                if (element === array[i]) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Get the current application's locale based on the `lang` attribute
         * on the `html` tag.
         *
         * @return {String}     The current application's locale
         * @api private
         */
        function get_current_locale() {
            return document.documentElement.lang.replace('-', '_');
        }

        return {
            /**
             * The current locale.
             *
             * @type {String}
             * @api public
             */
            locale: get_current_locale(),

            /**
             * Fallback locale.
             *
             * @type {String}
             * @api public
             */
            fallback: 'en',

            /**
             * Placeholder prefix.
             *
             * @type {String}
             * @api public
             */
            placeHolderPrefix: '%',

            /**
             * Placeholder suffix.
             *
             * @type {String}
             * @api public
             */
            placeHolderSuffix: '%',

            /**
             * Default domain.
             *
             * @type {String}
             * @api public
             */
            defaultDomain: 'messages',

            /**
             * Plurar separator.
             *
             * @type {String}
             * @api public
             */
            pluralSeparator: '|',

            /**
             * Adds a translation entry.
             *
             * @param {String} id       The message id
             * @param {String} message  The message to register for the given id
             * @param {String} domain   The domain for the message or null to use the default
             * @param {String} locale   The locale or null to use the default
             * @return {Object}         Translator
             * @api public
             */
            add: function(id, message, domain, locale) {
                var _locale = locale || this.locale || this.fallback,
                    _domain = domain || this.defaultDomain;

                if (!_messages[_locale]) {
                    _messages[_locale] = {};
                }

                if (!_messages[_locale][_domain]) {
                    _messages[_locale][_domain] = {};
                }

                _messages[_locale][_domain][id] = message;

                if (false === exists(_domains, _domain)) {
                    _domains.push(_domain);
                }

                return this;
            },


            /**
             * Translates the given message.
             *
             * @param {String} id             The message id
             * @param {Object} parameters     An array of parameters for the message
             * @param {String} domain         The domain for the message or null to guess it
             * @param {String} locale         The locale or null to use the default
             * @return {String}               The translated string
             * @api public
             */
            trans: function(id, parameters, domain, locale) {
                var _message = get_message(
                    id,
                    domain,
                    locale,
                    this.locale,
                    this.fallback
                );

                return replace_placeholders(_message, parameters || {});
            },

            /**
             * Translates the given choice message by choosing a translation according to a number.
             *
             * @param {String} id             The message id
             * @param {Number} number         The number to use to find the indice of the message
             * @param {Object} parameters     An array of parameters for the message
             * @param {String} domain         The domain for the message or null to guess it
             * @param {String} locale         The locale or null to use the default
             * @return {String}               The translated string
             * @api public
             */
            transChoice: function(id, number, parameters, domain, locale) {
                var _message = get_message(
                    id,
                    domain,
                    locale,
                    this.locale,
                    this.fallback
                );

                var _number  = parseInt(number, 10);

                if (undefined != _message && !isNaN(_number)) {
                    _message = pluralize(
                        _message,
                        _number,
                        locale || this.locale || this.fallback
                    );
                }

                return replace_placeholders(_message, parameters || {});
            },

            /**
             * Loads translations from JSON.
             *
             * @param {String} data     A JSON string or object literal
             * @return {Object}         Translator
             * @api public
             */
            fromJSON: function(data) {
                if(typeof data === 'string') {
                    data = JSON.parse(data);
                }

                if (data.locale) {
                    this.locale = data.locale;
                }

                if (data.fallback) {
                    this.fallback = data.fallback;
                }

                if (data.defaultDomain) {
                    this.defaultDomain = data.defaultDomain;
                }

                if (data.translations) {
                    for (var locale in data.translations) {
                        for (var domain in data.translations[locale]) {
                            for (var id in data.translations[locale][domain]) {
                                this.add(id, data.translations[locale][domain][id], domain, locale);
                            }
                        }
                    }
                }

                return this;
            },

            /**
             * @api public
             */
            reset: function() {
                _messages   = {};
                _domains    = [];
                this.locale = get_current_locale();
            }
        };

    })(document, undefined).fromJSON({ translations: config.translations });

    /**
     * Copyright (c) 2013 Kevin van Zonneveld (http://kvz.io) and Contributors (http://phpjs.org/authors)
     */

    var date = (function (trans, formats) {

        var jsdate, f,
            days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            months  = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

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
                return trans(days[f.w()-1].slice(0, 3));
            },
            j: function () {
                // Day of month; 1..31
                return jsdate.getDate();
            },
            l: function () {
                // Full day name; Monday...Sunday
                return trans(days[f.w()-1]);
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
                return trans(months[f.n() - 1]);
            },
            m: function () {
                // Month w/leading 0; 01...12
                return _pad(f.n(), 2);
            },
            M: function () {
                // Shorthand month name; Jan...Dec
                return trans(months[f.n() - 1].slice(0, 3));
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

        return function (format, timestamp) {

            jsdate = (timestamp === undefined ? new Date() : // Not provided
                (typeof timestamp === 'string' || timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
                    new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
            );

            if (formats.hasOwnProperty(format)) {
                format = trans(formats[format]);
            }

            return format.replace(formatChr, formatChrCb);
        };

    })(Translator.trans.bind(Translator), config.formats);

    window.Locale = {

        Translator: Translator,

        trans: Translator.trans.bind(Translator),

        transChoice: Translator.transChoice.bind(Translator),

        date: date

    };

})(this);
