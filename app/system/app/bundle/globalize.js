var Globalize =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	var config = window.$globalize || { translations: {} };

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

	__webpack_require__(2);
	__webpack_require__(3);

	var Globalize = __webpack_require__(1);

	// load cldr
	if (config.locale) {
	    Globalize.load(config);
	    Globalize.locale(config.locale);
	}

	// add translator
	Globalize.trans = Translator.trans.bind(Translator);
	Globalize.transChoice = Translator.transChoice.bind(Translator);

	// export
	module.exports = Globalize;

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
	 * Globalize v1.0.0
	 *
	 * http://github.com/jquery/globalize
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-04-23T12:02Z
	 */
	/*!
	 * Globalize v1.0.0 2015-04-23T12:02Z Released under the MIT license
	 * http://git.io/TrdQbw
	 */
	(function( root, factory ) {

		// UMD returnExports
		if ( true ) {

			// AMD
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [
				__webpack_require__(4),
				__webpack_require__(5)
			], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if ( typeof exports === "object" ) {

			// Node, CommonJS
			module.exports = factory( require( "cldrjs" ) );
		} else {

			// Global
			root.Globalize = factory( root.Cldr );
		}
	}( this, function( Cldr ) {


	/**
	 * A toString method that outputs meaningful values for objects or arrays and
	 * still performs as fast as a plain string in case variable is string, or as
	 * fast as `"" + number` in case variable is a number.
	 * Ref: http://jsperf.com/my-stringify
	 */
	var toString = function( variable ) {
		return typeof variable === "string" ? variable : ( typeof variable === "number" ? "" +
			variable : JSON.stringify( variable ) );
	};




	/**
	 * formatMessage( message, data )
	 *
	 * @message [String] A message with optional {vars} to be replaced.
	 *
	 * @data [Array or JSON] Object with replacing-variables content.
	 *
	 * Return the formatted message. For example:
	 *
	 * - formatMessage( "{0} second", [ 1 ] ); // 1 second
	 *
	 * - formatMessage( "{0}/{1}", ["m", "s"] ); // m/s
	 *
	 * - formatMessage( "{name} <{email}>", {
	 *     name: "Foo",
	 *     email: "bar@baz.qux"
	 *   }); // Foo <bar@baz.qux>
	 */
	var formatMessage = function( message, data ) {

		// Replace {attribute}'s
		message = message.replace( /{[0-9a-zA-Z-_. ]+}/g, function( name ) {
			name = name.replace( /^{([^}]*)}$/, "$1" );
			return toString( data[ name ] );
		});

		return message;
	};




	var objectExtend = function() {
		var destination = arguments[ 0 ],
			sources = [].slice.call( arguments, 1 );

		sources.forEach(function( source ) {
			var prop;
			for ( prop in source ) {
				destination[ prop ] = source[ prop ];
			}
		});

		return destination;
	};




	var createError = function( code, message, attributes ) {
		var error;

		message = code + ( message ? ": " + formatMessage( message, attributes ) : "" );
		error = new Error( message );
		error.code = code;

		objectExtend( error, attributes );

		return error;
	};




	var validate = function( code, message, check, attributes ) {
		if ( !check ) {
			throw createError( code, message, attributes );
		}
	};




	var alwaysArray = function( stringOrArray ) {
		return Array.isArray( stringOrArray ) ? stringOrArray : stringOrArray ? [ stringOrArray ] : [];
	};




	var validateCldr = function( path, value, options ) {
		var skipBoolean;
		options = options || {};

		skipBoolean = alwaysArray( options.skip ).some(function( pathRe ) {
			return pathRe.test( path );
		});

		validate( "E_MISSING_CLDR", "Missing required CLDR content `{path}`.", value || skipBoolean, {
			path: path
		});
	};




	var validateDefaultLocale = function( value ) {
		validate( "E_DEFAULT_LOCALE_NOT_DEFINED", "Default locale has not been defined.",
			value !== undefined, {} );
	};




	var validateParameterPresence = function( value, name ) {
		validate( "E_MISSING_PARAMETER", "Missing required parameter `{name}`.",
			value !== undefined, { name: name });
	};




	/**
	 * range( value, name, minimum, maximum )
	 *
	 * @value [Number].
	 *
	 * @name [String] name of variable.
	 *
	 * @minimum [Number]. The lowest valid value, inclusive.
	 *
	 * @maximum [Number]. The greatest valid value, inclusive.
	 */
	var validateParameterRange = function( value, name, minimum, maximum ) {
		validate(
			"E_PAR_OUT_OF_RANGE",
			"Parameter `{name}` has value `{value}` out of range [{minimum}, {maximum}].",
			value === undefined || value >= minimum && value <= maximum,
			{
				maximum: maximum,
				minimum: minimum,
				name: name,
				value: value
			}
		);
	};




	var validateParameterType = function( value, name, check, expected ) {
		validate(
			"E_INVALID_PAR_TYPE",
			"Invalid `{name}` parameter ({value}). {expected} expected.",
			check,
			{
				expected: expected,
				name: name,
				value: value
			}
		);
	};




	var validateParameterTypeLocale = function( value, name ) {
		validateParameterType(
			value,
			name,
			value === undefined || typeof value === "string" || value instanceof Cldr,
			"String or Cldr instance"
		);
	};




	/**
	 * Function inspired by jQuery Core, but reduced to our use case.
	 */
	var isPlainObject = function( obj ) {
		return obj !== null && "" + obj === "[object Object]";
	};




	var validateParameterTypePlainObject = function( value, name ) {
		validateParameterType(
			value,
			name,
			value === undefined || isPlainObject( value ),
			"Plain Object"
		);
	};




	var alwaysCldr = function( localeOrCldr ) {
		return localeOrCldr instanceof Cldr ? localeOrCldr : new Cldr( localeOrCldr );
	};




	// ref: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_Expressions?redirectlocale=en-US&redirectslug=JavaScript%2FGuide%2FRegular_Expressions
	var regexpEscape = function( string ) {
		return string.replace( /([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1" );
	};




	var stringPad = function( str, count, right ) {
		var length;
		if ( typeof str !== "string" ) {
			str = String( str );
		}
		for ( length = str.length; length < count; length += 1 ) {
			str = ( right ? ( str + "0" ) : ( "0" + str ) );
		}
		return str;
	};




	function validateLikelySubtags( cldr ) {
		cldr.once( "get", validateCldr );
		cldr.get( "supplemental/likelySubtags" );
	}

	/**
	 * [new] Globalize( locale|cldr )
	 *
	 * @locale [String]
	 *
	 * @cldr [Cldr instance]
	 *
	 * Create a Globalize instance.
	 */
	function Globalize( locale ) {
		if ( !( this instanceof Globalize ) ) {
			return new Globalize( locale );
		}

		validateParameterPresence( locale, "locale" );
		validateParameterTypeLocale( locale, "locale" );

		this.cldr = alwaysCldr( locale );

		validateLikelySubtags( this.cldr );
	}

	/**
	 * Globalize.load( json, ... )
	 *
	 * @json [JSON]
	 *
	 * Load resolved or unresolved cldr data.
	 * Somewhat equivalent to previous Globalize.addCultureInfo(...).
	 */
	Globalize.load = function() {
		// validations are delegated to Cldr.load().
		Cldr.load.apply( Cldr, arguments );
	};

	/**
	 * Globalize.locale( [locale|cldr] )
	 *
	 * @locale [String]
	 *
	 * @cldr [Cldr instance]
	 *
	 * Set default Cldr instance if locale or cldr argument is passed.
	 *
	 * Return the default Cldr instance.
	 */
	Globalize.locale = function( locale ) {
		validateParameterTypeLocale( locale, "locale" );

		if ( arguments.length ) {
			this.cldr = alwaysCldr( locale );
			validateLikelySubtags( this.cldr );
		}
		return this.cldr;
	};

	/**
	 * Optimization to avoid duplicating some internal functions across modules.
	 */
	Globalize._alwaysArray = alwaysArray;
	Globalize._createError = createError;
	Globalize._formatMessage = formatMessage;
	Globalize._isPlainObject = isPlainObject;
	Globalize._objectExtend = objectExtend;
	Globalize._regexpEscape = regexpEscape;
	Globalize._stringPad = stringPad;
	Globalize._validate = validate;
	Globalize._validateCldr = validateCldr;
	Globalize._validateDefaultLocale = validateDefaultLocale;
	Globalize._validateParameterPresence = validateParameterPresence;
	Globalize._validateParameterRange = validateParameterRange;
	Globalize._validateParameterTypePlainObject = validateParameterTypePlainObject;
	Globalize._validateParameterType = validateParameterType;

	return Globalize;




	}));


/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
	 * Globalize v1.0.0
	 *
	 * http://github.com/jquery/globalize
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-04-23T12:02Z
	 */
	/*!
	 * Globalize v1.0.0 2015-04-23T12:02Z Released under the MIT license
	 * http://git.io/TrdQbw
	 */
	(function( root, factory ) {

		// UMD returnExports
		if ( true ) {

			// AMD
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [
				__webpack_require__(4),
				__webpack_require__(1),
				__webpack_require__(5),
				__webpack_require__(6)
			], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if ( typeof exports === "object" ) {

			// Node, CommonJS
			module.exports = factory( require( "cldrjs" ), require( "globalize" ) );
		} else {

			// Global
			factory( root.Cldr, root.Globalize );
		}
	}(this, function( Cldr, Globalize ) {

	var createError = Globalize._createError,
		objectExtend = Globalize._objectExtend,
		regexpEscape = Globalize._regexpEscape,
		stringPad = Globalize._stringPad,
		validateCldr = Globalize._validateCldr,
		validateDefaultLocale = Globalize._validateDefaultLocale,
		validateParameterPresence = Globalize._validateParameterPresence,
		validateParameterRange = Globalize._validateParameterRange,
		validateParameterType = Globalize._validateParameterType,
		validateParameterTypePlainObject = Globalize._validateParameterTypePlainObject;


	var createErrorUnsupportedFeature = function( feature ) {
		return createError( "E_UNSUPPORTED", "Unsupported {feature}.", {
			feature: feature
		});
	};




	var validateParameterTypeNumber = function( value, name ) {
		validateParameterType(
			value,
			name,
			value === undefined || typeof value === "number",
			"Number"
		);
	};




	var validateParameterTypeString = function( value, name ) {
		validateParameterType(
			value,
			name,
			value === undefined || typeof value === "string",
			"a string"
		);
	};




	/**
	 * goupingSeparator( number, primaryGroupingSize, secondaryGroupingSize )
	 *
	 * @number [Number].
	 *
	 * @primaryGroupingSize [Number]
	 *
	 * @secondaryGroupingSize [Number]
	 *
	 * Return the formatted number with group separator.
	 */
	var numberFormatGroupingSeparator = function( number, primaryGroupingSize, secondaryGroupingSize ) {
		var index,
			currentGroupingSize = primaryGroupingSize,
			ret = "",
			sep = ",",
			switchToSecondary = secondaryGroupingSize ? true : false;

		number = String( number ).split( "." );
		index = number[ 0 ].length;

		while ( index > currentGroupingSize ) {
			ret = number[ 0 ].slice( index - currentGroupingSize, index ) +
				( ret.length ? sep : "" ) + ret;
			index -= currentGroupingSize;
			if ( switchToSecondary ) {
				currentGroupingSize = secondaryGroupingSize;
				switchToSecondary = false;
			}
		}

		number[ 0 ] = number[ 0 ].slice( 0, index ) + ( ret.length ? sep : "" ) + ret;
		return number.join( "." );
	};




	/**
	 * integerFractionDigits( number, minimumIntegerDigits, minimumFractionDigits,
	 * maximumFractionDigits, round, roundIncrement )
	 *
	 * @number [Number]
	 *
	 * @minimumIntegerDigits [Number]
	 *
	 * @minimumFractionDigits [Number]
	 *
	 * @maximumFractionDigits [Number]
	 *
	 * @round [Function]
	 *
	 * @roundIncrement [Function]
	 *
	 * Return the formatted integer and fraction digits.
	 */
	var numberFormatIntegerFractionDigits = function( number, minimumIntegerDigits, minimumFractionDigits, maximumFractionDigits, round,
		roundIncrement ) {

		// Fraction
		if ( maximumFractionDigits ) {

			// Rounding
			if ( roundIncrement ) {
				number = round( number, roundIncrement );

			// Maximum fraction digits
			} else {
				number = round( number, { exponent: -maximumFractionDigits } );
			}

			// Minimum fraction digits
			if ( minimumFractionDigits ) {
				number = String( number ).split( "." );
				number[ 1 ] = stringPad( number[ 1 ] || "", minimumFractionDigits, true );
				number = number.join( "." );
			}
		} else {
			number = round( number );
		}

		number = String( number );

		// Minimum integer digits
		if ( minimumIntegerDigits ) {
			number = number.split( "." );
			number[ 0 ] = stringPad( number[ 0 ], minimumIntegerDigits );
			number = number.join( "." );
		}

		return number;
	};




	/**
	 * toPrecision( number, precision, round )
	 *
	 * @number (Number)
	 *
	 * @precision (Number) significant figures precision (not decimal precision).
	 *
	 * @round (Function)
	 *
	 * Return number.toPrecision( precision ) using the given round function.
	 */
	var numberToPrecision = function( number, precision, round ) {
		var roundOrder;

		// Get number at two extra significant figure precision.
		number = number.toPrecision( precision + 2 );

		// Then, round it to the required significant figure precision.
		roundOrder = Math.ceil( Math.log( Math.abs( number ) ) / Math.log( 10 ) );
		roundOrder -= precision;

		return round( number, { exponent: roundOrder } );
	};




	/**
	 * toPrecision( number, minimumSignificantDigits, maximumSignificantDigits, round )
	 *
	 * @number [Number]
	 *
	 * @minimumSignificantDigits [Number]
	 *
	 * @maximumSignificantDigits [Number]
	 *
	 * @round [Function]
	 *
	 * Return the formatted significant digits number.
	 */
	var numberFormatSignificantDigits = function( number, minimumSignificantDigits, maximumSignificantDigits, round ) {
		var atMinimum, atMaximum;

		// Sanity check.
		if ( minimumSignificantDigits > maximumSignificantDigits ) {
			maximumSignificantDigits = minimumSignificantDigits;
		}

		atMinimum = numberToPrecision( number, minimumSignificantDigits, round );
		atMaximum = numberToPrecision( number, maximumSignificantDigits, round );

		// Use atMaximum only if it has more significant digits than atMinimum.
		number = +atMinimum === +atMaximum ? atMinimum : atMaximum;

		// Expand integer numbers, eg. 123e5 to 12300.
		number = ( +number ).toString( 10 );

		if ( (/e/).test( number ) ) {
			throw createErrorUnsupportedFeature({
				feature: "integers out of (1e21, 1e-7)"
			});
		}

		// Add trailing zeros if necessary.
		if ( minimumSignificantDigits - number.replace( /^0+|\./g, "" ).length > 0 ) {
			number = number.split( "." );
			number[ 1 ] = stringPad( number[ 1 ] || "", minimumSignificantDigits - number[ 0 ].replace( /^0+/, "" ).length, true );
			number = number.join( "." );
		}

		return number;
	};




	/**
	 * format( number, properties )
	 *
	 * @number [Number].
	 *
	 * @properties [Object] Output of number/format-properties.
	 *
	 * Return the formatted number.
	 * ref: http://www.unicode.org/reports/tr35/tr35-numbers.html
	 */
	var numberFormat = function( number, properties ) {
		var infinitySymbol, maximumFractionDigits, maximumSignificantDigits, minimumFractionDigits,
		minimumIntegerDigits, minimumSignificantDigits, nanSymbol, nuDigitsMap, padding, prefix,
		primaryGroupingSize, pattern, ret, round, roundIncrement, secondaryGroupingSize, suffix,
		symbolMap;

		padding = properties[ 1 ];
		minimumIntegerDigits = properties[ 2 ];
		minimumFractionDigits = properties[ 3 ];
		maximumFractionDigits = properties[ 4 ];
		minimumSignificantDigits = properties[ 5 ];
		maximumSignificantDigits = properties[ 6 ];
		roundIncrement = properties[ 7 ];
		primaryGroupingSize = properties[ 8 ];
		secondaryGroupingSize = properties[ 9 ];
		round = properties[ 15 ];
		infinitySymbol = properties[ 16 ];
		nanSymbol = properties[ 17 ];
		symbolMap = properties[ 18 ];
		nuDigitsMap = properties[ 19 ];

		// NaN
		if ( isNaN( number ) ) {
			return nanSymbol;
		}

		if ( number < 0 ) {
			pattern = properties[ 12 ];
			prefix = properties[ 13 ];
			suffix = properties[ 14 ];
		} else {
			pattern = properties[ 11 ];
			prefix = properties[ 0 ];
			suffix = properties[ 10 ];
		}

		// Infinity
		if ( !isFinite( number ) ) {
			return prefix + infinitySymbol + suffix;
		}

		ret = prefix;

		// Percent
		if ( pattern.indexOf( "%" ) !== -1 ) {
			number *= 100;

		// Per mille
		} else if ( pattern.indexOf( "\u2030" ) !== -1 ) {
			number *= 1000;
		}

		// Significant digit format
		if ( !isNaN( minimumSignificantDigits * maximumSignificantDigits ) ) {
			number = numberFormatSignificantDigits( number, minimumSignificantDigits,
				maximumSignificantDigits, round );

		// Integer and fractional format
		} else {
			number = numberFormatIntegerFractionDigits( number, minimumIntegerDigits,
				minimumFractionDigits, maximumFractionDigits, round, roundIncrement );
		}

		// Remove the possible number minus sign
		number = number.replace( /^-/, "" );

		// Grouping separators
		if ( primaryGroupingSize ) {
			number = numberFormatGroupingSeparator( number, primaryGroupingSize,
				secondaryGroupingSize );
		}

		ret += number;

		// Scientific notation
		// TODO implement here

		// Padding/'([^']|'')+'|''|[.,\-+E%\u2030]/g
		// TODO implement here

		ret += suffix;

		return ret.replace( /('([^']|'')+'|'')|./g, function( character, literal ) {

			// Literals
			if ( literal ) {
				literal = literal.replace( /''/, "'" );
				if ( literal.length > 2 ) {
					literal = literal.slice( 1, -1 );
				}
				return literal;
			}

			// Symbols
			character = character.replace( /[.,\-+E%\u2030]/, function( symbol ) {
				return symbolMap[ symbol ];
			});

			// Numbering system
			if ( nuDigitsMap ) {
				character = character.replace( /[0-9]/, function( digit ) {
					return nuDigitsMap[ +digit ];
				});
			}

			return character;
		});
	};




	/**
	 * NumberingSystem( cldr )
	 *
	 * - http://www.unicode.org/reports/tr35/tr35-numbers.html#otherNumberingSystems
	 * - http://cldr.unicode.org/index/bcp47-extension
	 * - http://www.unicode.org/reports/tr35/#u_Extension
	 */
	var numberNumberingSystem = function( cldr ) {
		var nu = cldr.attributes[ "u-nu" ];

		if ( nu ) {
			if ( nu === "traditio" ) {
				nu = "traditional";
			}
			if ( [ "native", "traditional", "finance" ].indexOf( nu ) !== -1 ) {

				// Unicode locale extension `u-nu` is set using either (native, traditional or
				// finance). So, lookup the respective locale's numberingSystem and return it.
				return cldr.main([ "numbers/otherNumberingSystems", nu ]);
			}

			// Unicode locale extension `u-nu` is set with an explicit numberingSystem. Return it.
			return nu;
		}

		// Return the default numberingSystem.
		return cldr.main( "numbers/defaultNumberingSystem" );
	};




	/**
	 * nuMap( cldr )
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return digits map if numbering system is different than `latn`.
	 */
	var numberNumberingSystemDigitsMap = function( cldr ) {
		var aux,
			nu = numberNumberingSystem( cldr );

		if ( nu === "latn" ) {
			return;
		}

		aux = cldr.supplemental([ "numberingSystems", nu ]);

		if ( aux._type !== "numeric" ) {
			throw createErrorUnsupportedFeature( "`" + aux._type + "` numbering system" );
		}

		return aux._digits;
	};




	/**
	 * EBNF representation:
	 *
	 * number_pattern_re =        prefix?
	 *                            padding?
	 *                            (integer_fraction_pattern | significant_pattern)
	 *                            scientific_notation?
	 *                            suffix?
	 *
	 * prefix =                   non_number_stuff
	 *
	 * padding =                  "*" regexp(.)
	 *
	 * integer_fraction_pattern = integer_pattern
	 *                            fraction_pattern?
	 *
	 * integer_pattern =          regexp([#,]*[0,]*0+)
	 *
	 * fraction_pattern =         "." regexp(0*[0-9]*#*)
	 *
	 * significant_pattern =      regexp([#,]*@+#*)
	 *
	 * scientific_notation =      regexp(E\+?0+)
	 *
	 * suffix =                   non_number_stuff
	 *
	 * non_number_stuff =         regexp(('[^']+'|''|[^*#@0,.E])*)
	 *
	 *
	 * Regexp groups:
	 *
	 *  0: number_pattern_re
	 *  1: prefix
	 *  2: -
	 *  3: padding
	 *  4: (integer_fraction_pattern | significant_pattern)
	 *  5: integer_fraction_pattern
	 *  6: integer_pattern
	 *  7: fraction_pattern
	 *  8: significant_pattern
	 *  9: scientific_notation
	 * 10: suffix
	 * 11: -
	 */
	var numberPatternRe = (/^(('[^']+'|''|[^*#@0,.E])*)(\*.)?((([#,]*[0,]*0+)(\.0*[0-9]*#*)?)|([#,]*@+#*))(E\+?0+)?(('[^']+'|''|[^*#@0,.E])*)$/);




	/**
	 * format( number, pattern )
	 *
	 * @number [Number].
	 *
	 * @pattern [String] raw pattern for numbers.
	 *
	 * Return the formatted number.
	 * ref: http://www.unicode.org/reports/tr35/tr35-numbers.html
	 */
	var numberPatternProperties = function( pattern ) {
		var aux1, aux2, fractionPattern, integerFractionOrSignificantPattern, integerPattern,
			maximumFractionDigits, maximumSignificantDigits, minimumFractionDigits,
			minimumIntegerDigits, minimumSignificantDigits, padding, prefix, primaryGroupingSize,
			roundIncrement, scientificNotation, secondaryGroupingSize, significantPattern, suffix;

		pattern = pattern.match( numberPatternRe );
		if ( !pattern ) {
			throw new Error( "Invalid pattern: " + pattern );
		}

		prefix = pattern[ 1 ];
		padding = pattern[ 3 ];
		integerFractionOrSignificantPattern = pattern[ 4 ];
		significantPattern = pattern[ 8 ];
		scientificNotation = pattern[ 9 ];
		suffix = pattern[ 10 ];

		// Significant digit format
		if ( significantPattern ) {
			significantPattern.replace( /(@+)(#*)/, function( match, minimumSignificantDigitsMatch, maximumSignificantDigitsMatch ) {
				minimumSignificantDigits = minimumSignificantDigitsMatch.length;
				maximumSignificantDigits = minimumSignificantDigits +
					maximumSignificantDigitsMatch.length;
			});

		// Integer and fractional format
		} else {
			fractionPattern = pattern[ 7 ];
			integerPattern = pattern[ 6 ];

			if ( fractionPattern ) {

				// Minimum fraction digits, and rounding.
				fractionPattern.replace( /[0-9]+/, function( match ) {
					minimumFractionDigits = match;
				});
				if ( minimumFractionDigits ) {
					roundIncrement = +( "0." + minimumFractionDigits );
					minimumFractionDigits = minimumFractionDigits.length;
				} else {
					minimumFractionDigits = 0;
				}

				// Maximum fraction digits
				// 1: ignore decimal character
				maximumFractionDigits = fractionPattern.length - 1 /* 1 */;
			}

			// Minimum integer digits
			integerPattern.replace( /0+$/, function( match ) {
				minimumIntegerDigits = match.length;
			});
		}

		// Scientific notation
		if ( scientificNotation ) {
			throw createErrorUnsupportedFeature({
				feature: "scientific notation (not implemented)"
			});
		}

		// Padding
		if ( padding ) {
			throw createErrorUnsupportedFeature({
				feature: "padding (not implemented)"
			});
		}

		// Grouping
		if ( ( aux1 = integerFractionOrSignificantPattern.lastIndexOf( "," ) ) !== -1 ) {

			// Primary grouping size is the interval between the last group separator and the end of
			// the integer (or the end of the significant pattern).
			aux2 = integerFractionOrSignificantPattern.split( "." )[ 0 ];
			primaryGroupingSize = aux2.length - aux1 - 1;

			// Secondary grouping size is the interval between the last two group separators.
			if ( ( aux2 = integerFractionOrSignificantPattern.lastIndexOf( ",", aux1 - 1 ) ) !== -1 ) {
				secondaryGroupingSize = aux1 - 1 - aux2;
			}
		}

		// Return:
		//  0: @prefix String
		//  1: @padding Array [ <character>, <count> ] TODO
		//  2: @minimumIntegerDigits non-negative integer Number value indicating the minimum integer
		//        digits to be used. Numbers will be padded with leading zeroes if necessary.
		//  3: @minimumFractionDigits and
		//  4: @maximumFractionDigits are non-negative integer Number values indicating the minimum and
		//        maximum fraction digits to be used. Numbers will be rounded or padded with trailing
		//        zeroes if necessary.
		//  5: @minimumSignificantDigits and
		//  6: @maximumSignificantDigits are positive integer Number values indicating the minimum and
		//        maximum fraction digits to be shown. Either none or both of these properties are
		//        present; if they are, they override minimum and maximum integer and fraction digits
		//        – the formatter uses however many integer and fraction digits are required to display
		//        the specified number of significant digits.
		//  7: @roundIncrement Decimal round increment or null
		//  8: @primaryGroupingSize
		//  9: @secondaryGroupingSize
		// 10: @suffix String
		return [
			prefix,
			padding,
			minimumIntegerDigits,
			minimumFractionDigits,
			maximumFractionDigits,
			minimumSignificantDigits,
			maximumSignificantDigits,
			roundIncrement,
			primaryGroupingSize,
			secondaryGroupingSize,
			suffix
		];
	};




	/**
	 * Symbol( name, cldr )
	 *
	 * @name [String] Symbol name.
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return the localized symbol given its name.
	 */
	var numberSymbol = function( name, cldr ) {
		return cldr.main([
			"numbers/symbols-numberSystem-" + numberNumberingSystem( cldr ),
			name
		]);
	};




	var numberSymbolName = {
		".": "decimal",
		",": "group",
		"%": "percentSign",
		"+": "plusSign",
		"-": "minusSign",
		"E": "exponential",
		"\u2030": "perMille"
	};




	/**
	 * symbolMap( cldr )
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return the (localized symbol, pattern symbol) key value pair, eg. {
	 *   ".": "٫",
	 *   ",": "٬",
	 *   "%": "٪",
	 *   ...
	 * };
	 */
	var numberSymbolMap = function( cldr ) {
		var symbol,
			symbolMap = {};

		for ( symbol in numberSymbolName ) {
			symbolMap[ symbol ] = numberSymbol( numberSymbolName[ symbol ], cldr );
		}

		return symbolMap;
	};




	var numberTruncate = function( value ) {
		if ( isNaN( value ) ) {
			return NaN;
		}
		return Math[ value < 0 ? "ceil" : "floor" ]( value );
	};




	/**
	 * round( method )
	 *
	 * @method [String] with either "round", "ceil", "floor", or "truncate".
	 *
	 * Return function( value, incrementOrExp ):
	 *
	 *   @value [Number] eg. 123.45.
	 *
	 *   @incrementOrExp [Number] optional, eg. 0.1; or
	 *     [Object] Either { increment: <value> } or { exponent: <value> }
	 *
	 *   Return the rounded number, eg:
	 *   - round( "round" )( 123.45 ): 123;
	 *   - round( "ceil" )( 123.45 ): 124;
	 *   - round( "floor" )( 123.45 ): 123;
	 *   - round( "truncate" )( 123.45 ): 123;
	 *   - round( "round" )( 123.45, 0.1 ): 123.5;
	 *   - round( "round" )( 123.45, 10 ): 120;
	 *
	 *   Based on https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/round
	 *   Ref: #376
	 */
	var numberRound = function( method ) {
		method = method || "round";
		method = method === "truncate" ? numberTruncate : Math[ method ];

		return function( value, incrementOrExp ) {
			var exp, increment;

			value = +value;

			// If the value is not a number, return NaN.
			if ( isNaN( value ) ) {
				return NaN;
			}

			// Exponent given.
			if ( typeof incrementOrExp === "object" && incrementOrExp.exponent ) {
				exp = +incrementOrExp.exponent;
				increment = 1;

				if ( exp === 0 ) {
					return method( value );
				}

				// If the exp is not an integer, return NaN.
				if ( !( typeof exp === "number" && exp % 1 === 0 ) ) {
					return NaN;
				}

			// Increment given.
			} else {
				increment = +incrementOrExp || 1;

				if ( increment === 1 ) {
					return method( value );
				}

				// If the increment is not a number, return NaN.
				if ( isNaN( increment ) ) {
					return NaN;
				}

				increment = increment.toExponential().split( "e" );
				exp = +increment[ 1 ];
				increment = +increment[ 0 ];
			}

			// Shift & Round
			value = value.toString().split( "e" );
			value[ 0 ] = +value[ 0 ] / increment;
			value[ 1 ] = value[ 1 ] ? ( +value[ 1 ] - exp ) : -exp;
			value = method( +(value[ 0 ] + "e" + value[ 1 ] ) );

			// Shift back
			value = value.toString().split( "e" );
			value[ 0 ] = +value[ 0 ] * increment;
			value[ 1 ] = value[ 1 ] ? ( +value[ 1 ] + exp ) : exp;
			return +( value[ 0 ] + "e" + value[ 1 ] );
		};
	};




	/**
	 * formatProperties( pattern, cldr [, options] )
	 *
	 * @pattern [String] raw pattern for numbers.
	 *
	 * @cldr [Cldr instance].
	 *
	 * @options [Object]:
	 * - minimumIntegerDigits [Number]
	 * - minimumFractionDigits, maximumFractionDigits [Number]
	 * - minimumSignificantDigits, maximumSignificantDigits [Number]
	 * - round [String] "ceil", "floor", "round" (default), or "truncate".
	 * - useGrouping [Boolean] default true.
	 *
	 * Return the processed properties that will be used in number/format.
	 * ref: http://www.unicode.org/reports/tr35/tr35-numbers.html
	 */
	var numberFormatProperties = function( pattern, cldr, options ) {
		var negativePattern, negativePrefix, negativeProperties, negativeSuffix, positivePattern,
			properties;

		function getOptions( attribute, propertyIndex ) {
			if ( attribute in options ) {
				properties[ propertyIndex ] = options[ attribute ];
			}
		}

		options = options || {};
		pattern = pattern.split( ";" );

		positivePattern = pattern[ 0 ];

		negativePattern = pattern[ 1 ] || "-" + positivePattern;
		negativeProperties = numberPatternProperties( negativePattern );
		negativePrefix = negativeProperties[ 0 ];
		negativeSuffix = negativeProperties[ 10 ];

		properties = numberPatternProperties( positivePattern ).concat([
			positivePattern,
			negativePrefix + positivePattern + negativeSuffix,
			negativePrefix,
			negativeSuffix,
			numberRound( options.round ),
			numberSymbol( "infinity", cldr ),
			numberSymbol( "nan", cldr ),
			numberSymbolMap( cldr ),
			numberNumberingSystemDigitsMap( cldr )
		]);

		getOptions( "minimumIntegerDigits", 2 );
		getOptions( "minimumFractionDigits", 3 );
		getOptions( "maximumFractionDigits", 4 );
		getOptions( "minimumSignificantDigits", 5 );
		getOptions( "maximumSignificantDigits", 6 );

		// Grouping separators
		if ( options.useGrouping === false ) {
			properties[ 8 ] = null;
		}

		// Normalize number of digits if only one of either minimumFractionDigits or
		// maximumFractionDigits is passed in as an option
		if ( "minimumFractionDigits" in options && !( "maximumFractionDigits" in options ) ) {
			// maximumFractionDigits = Math.max( minimumFractionDigits, maximumFractionDigits );
			properties[ 4 ] = Math.max( properties[ 3 ], properties[ 4 ] );
		} else if ( !( "minimumFractionDigits" in options ) &&
				"maximumFractionDigits" in options ) {
			// minimumFractionDigits = Math.min( minimumFractionDigits, maximumFractionDigits );
			properties[ 3 ] = Math.min( properties[ 3 ], properties[ 4 ] );
		}

		// Return:
		// 0-10: see number/pattern-properties.
		// 11: @positivePattern [String] Positive pattern.
		// 12: @negativePattern [String] Negative pattern.
		// 13: @negativePrefix [String] Negative prefix.
		// 14: @negativeSuffix [String] Negative suffix.
		// 15: @round [Function] Round function.
		// 16: @infinitySymbol [String] Infinity symbol.
		// 17: @nanSymbol [String] NaN symbol.
		// 18: @symbolMap [Object] A bunch of other symbols.
		// 19: @nuDigitsMap [Array] Digits map if numbering system is different than `latn`.
		return properties;
	};




	/**
	 * EBNF representation:
	 *
	 * number_pattern_re =        prefix_including_padding?
	 *                            number
	 *                            scientific_notation?
	 *                            suffix?
	 *
	 * number =                   integer_including_group_separator fraction_including_decimal_separator
	 *
	 * integer_including_group_separator =
	 *                            regexp([0-9,]*[0-9]+)
	 *
	 * fraction_including_decimal_separator =
	 *                            regexp((\.[0-9]+)?)

	 * prefix_including_padding = non_number_stuff
	 *
	 * scientific_notation =      regexp(E[+-]?[0-9]+)
	 *
	 * suffix =                   non_number_stuff
	 *
	 * non_number_stuff =         regexp([^0-9]*)
	 *
	 *
	 * Regexp groups:
	 *
	 * 0: number_pattern_re
	 * 1: prefix
	 * 2: integer_including_group_separator fraction_including_decimal_separator
	 * 3: integer_including_group_separator
	 * 4: fraction_including_decimal_separator
	 * 5: scientific_notation
	 * 6: suffix
	 */
	var numberNumberRe = (/^([^0-9]*)(([0-9,]*[0-9]+)(\.[0-9]+)?)(E[+-]?[0-9]+)?([^0-9]*)$/);




	/**
	 * parse( value, properties )
	 *
	 * @value [String].
	 *
	 * @properties [Object] Parser properties is a reduced pre-processed cldr
	 * data set returned by numberParserProperties().
	 *
	 * Return the parsed Number (including Infinity) or NaN when value is invalid.
	 * ref: http://www.unicode.org/reports/tr35/tr35-numbers.html
	 */
	var numberParse = function( value, properties ) {
		var aux, infinitySymbol, invertedNuDigitsMap, invertedSymbolMap, localizedDigitRe,
			localizedSymbolsRe, negativePrefix, negativeSuffix, number, prefix, suffix;

		infinitySymbol = properties[ 0 ];
		invertedSymbolMap = properties[ 1 ];
		negativePrefix = properties[ 2 ];
		negativeSuffix = properties[ 3 ];
		invertedNuDigitsMap = properties[ 4 ];

		// Infinite number.
		if ( aux = value.match( infinitySymbol ) ) {

			number = Infinity;
			prefix = value.slice( 0, aux.length );
			suffix = value.slice( aux.length + 1 );

		// Finite number.
		} else {

			// TODO: Create it during setup, i.e., make it a property.
			localizedSymbolsRe = new RegExp(
				Object.keys( invertedSymbolMap ).map(function( localizedSymbol ) {
					return regexpEscape( localizedSymbol );
				}).join( "|" ),
				"g"
			);

			// Reverse localized symbols.
			value = value.replace( localizedSymbolsRe, function( localizedSymbol ) {
				return invertedSymbolMap[ localizedSymbol ];
			});

			// Reverse localized numbering system.
			if ( invertedNuDigitsMap ) {

				// TODO: Create it during setup, i.e., make it a property.
				localizedDigitRe = new RegExp(
					Object.keys( invertedNuDigitsMap ).map(function( localizedDigit ) {
						return regexpEscape( localizedDigit );
					}).join( "|" ),
					"g"
				);
				value = value.replace( localizedDigitRe, function( localizedDigit ) {
					return invertedNuDigitsMap[ localizedDigit ];
				});
			}

			// Is it a valid number?
			value = value.match( numberNumberRe );
			if ( !value ) {

				// Invalid number.
				return NaN;
			}

			prefix = value[ 1 ];
			suffix = value[ 6 ];

			// Remove grouping separators.
			number = value[ 2 ].replace( /,/g, "" );

			// Scientific notation
			if ( value[ 5 ] ) {
				number += value[ 5 ];
			}

			number = +number;

			// Is it a valid number?
			if ( isNaN( number ) ) {

				// Invalid number.
				return NaN;
			}

			// Percent
			if ( value[ 0 ].indexOf( "%" ) !== -1 ) {
				number /= 100;
				suffix = suffix.replace( "%", "" );

			// Per mille
			} else if ( value[ 0 ].indexOf( "\u2030" ) !== -1 ) {
				number /= 1000;
				suffix = suffix.replace( "\u2030", "" );
			}
		}

		// Negative number
		// "If there is an explicit negative subpattern, it serves only to specify the negative prefix
		// and suffix. If there is no explicit negative subpattern, the negative subpattern is the
		// localized minus sign prefixed to the positive subpattern" UTS#35
		if ( prefix === negativePrefix && suffix === negativeSuffix ) {
			number *= -1;
		}

		return number;
	};




	/**
	 * symbolMap( cldr )
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return the (localized symbol, pattern symbol) key value pair, eg. {
	 *   "٫": ".",
	 *   "٬": ",",
	 *   "٪": "%",
	 *   ...
	 * };
	 */
	var numberSymbolInvertedMap = function( cldr ) {
		var symbol,
			symbolMap = {};

		for ( symbol in numberSymbolName ) {
			symbolMap[ numberSymbol( numberSymbolName[ symbol ], cldr ) ] = symbol;
		}

		return symbolMap;
	};




	/**
	 * parseProperties( pattern, cldr )
	 *
	 * @pattern [String] raw pattern for numbers.
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return parser properties, used to feed parser function.
	 */
	var numberParseProperties = function( pattern, cldr ) {
		var invertedNuDigitsMap, invertedNuDigitsMapSanityCheck, negativePattern, negativeProperties,
			nuDigitsMap = numberNumberingSystemDigitsMap( cldr );

		pattern = pattern.split( ";" );
		negativePattern = pattern[ 1 ] || "-" + pattern[ 0 ];
		negativeProperties = numberPatternProperties( negativePattern );
		if ( nuDigitsMap ) {
			invertedNuDigitsMap = nuDigitsMap.split( "" ).reduce(function( object, localizedDigit, i ) {
				object[ localizedDigit ] = String( i );
				return object;
			}, {} );
			invertedNuDigitsMapSanityCheck = "0123456789".split( "" ).reduce(function( object, digit ) {
				object[ digit ] = "invalid";
				return object;
			}, {} );
			invertedNuDigitsMap = objectExtend(
				invertedNuDigitsMapSanityCheck,
				invertedNuDigitsMap
			);
		}

		// 0: @infinitySymbol [String] Infinity symbol.
		// 1: @invertedSymbolMap [Object] Inverted symbol map augmented with sanity check.
		//    The sanity check prevents permissive parsing, i.e., it prevents symbols that doesn't
		//    belong to the localized set to pass through. This is obtained with the result of the
		//    inverted map object overloading symbol name map object (the remaining symbol name
		//    mappings will invalidate parsing, working as the sanity check).
		// 2: @negativePrefix [String] Negative prefix.
		// 3: @negativeSuffix [String] Negative suffix with percent or per mille stripped out.
		// 4: @invertedNuDigitsMap [Object] Inverted digits map if numbering system is different than
		//    `latn` augmented with sanity check (similar to invertedSymbolMap).
		return [
			numberSymbol( "infinity", cldr ),
			objectExtend( {}, numberSymbolName, numberSymbolInvertedMap( cldr ) ),
			negativeProperties[ 0 ],
			negativeProperties[ 10 ].replace( "%", "" ).replace( "\u2030", "" ),
			invertedNuDigitsMap
		];
	};




	/**
	 * Pattern( style )
	 *
	 * @style [String] "decimal" (default) or "percent".
	 *
	 * @cldr [Cldr instance].
	 */
	var numberPattern = function( style, cldr ) {
		if ( style !== "decimal" && style !== "percent" ) {
			throw new Error( "Invalid style" );
		}

		return cldr.main([
			"numbers",
			style + "Formats-numberSystem-" + numberNumberingSystem( cldr ),
			"standard"
		]);
	};




	/**
	 * .numberFormatter( [options] )
	 *
	 * @options [Object]:
	 * - style: [String] "decimal" (default) or "percent".
	 * - see also number/format options.
	 *
	 * Return a function that formats a number according to the given options and default/instance
	 * locale.
	 */
	Globalize.numberFormatter =
	Globalize.prototype.numberFormatter = function( options ) {
		var cldr, maximumFractionDigits, maximumSignificantDigits, minimumFractionDigits,
			minimumIntegerDigits, minimumSignificantDigits, pattern, properties;

		validateParameterTypePlainObject( options, "options" );

		options = options || {};
		cldr = this.cldr;

		validateDefaultLocale( cldr );

		cldr.on( "get", validateCldr );

		if ( options.raw ) {
			pattern = options.raw;
		} else {
			pattern = numberPattern( options.style || "decimal", cldr );
		}

		properties = numberFormatProperties( pattern, cldr, options );

		cldr.off( "get", validateCldr );

		minimumIntegerDigits = properties[ 2 ];
		minimumFractionDigits = properties[ 3 ];
		maximumFractionDigits = properties[ 4 ];

		minimumSignificantDigits = properties[ 5 ];
		maximumSignificantDigits = properties[ 6 ];

		// Validate significant digit format properties
		if ( !isNaN( minimumSignificantDigits * maximumSignificantDigits ) ) {
			validateParameterRange( minimumSignificantDigits, "minimumSignificantDigits", 1, 21 );
			validateParameterRange( maximumSignificantDigits, "maximumSignificantDigits",
				minimumSignificantDigits, 21 );

		} else if ( !isNaN( minimumSignificantDigits ) || !isNaN( maximumSignificantDigits ) ) {
			throw new Error( "Neither or both the minimum and maximum significant digits must be " +
				"present" );

		// Validate integer and fractional format
		} else {
			validateParameterRange( minimumIntegerDigits, "minimumIntegerDigits", 1, 21 );
			validateParameterRange( minimumFractionDigits, "minimumFractionDigits", 0, 20 );
			validateParameterRange( maximumFractionDigits, "maximumFractionDigits",
				minimumFractionDigits, 20 );
		}

		return function( value ) {
			validateParameterPresence( value, "value" );
			validateParameterTypeNumber( value, "value" );
			return numberFormat( value, properties );
		};
	};

	/**
	 * .numberParser( [options] )
	 *
	 * @options [Object]:
	 * - style: [String] "decimal" (default) or "percent".
	 *
	 * Return the number parser according to the default/instance locale.
	 */
	Globalize.numberParser =
	Globalize.prototype.numberParser = function( options ) {
		var cldr, pattern, properties;

		validateParameterTypePlainObject( options, "options" );

		options = options || {};
		cldr = this.cldr;

		validateDefaultLocale( cldr );

		cldr.on( "get", validateCldr );

		if ( options.raw ) {
			pattern = options.raw;
		} else {
			pattern = numberPattern( options.style || "decimal", cldr );
		}

		properties = numberParseProperties( pattern, cldr );

		cldr.off( "get", validateCldr );

		return function( value ) {
			validateParameterPresence( value, "value" );
			validateParameterTypeString( value, "value" );
			return numberParse( value, properties );
		};
	};

	/**
	 * .formatNumber( value [, options] )
	 *
	 * @value [Number] number to be formatted.
	 *
	 * @options [Object]: see number/format-properties.
	 *
	 * Format a number according to the given options and default/instance locale.
	 */
	Globalize.formatNumber =
	Globalize.prototype.formatNumber = function( value, options ) {
		validateParameterPresence( value, "value" );
		validateParameterTypeNumber( value, "value" );

		return this.numberFormatter( options )( value );
	};

	/**
	 * .parseNumber( value [, options] )
	 *
	 * @value [String]
	 *
	 * @options [Object]: See numberParser().
	 *
	 * Return the parsed Number (including Infinity) or NaN when value is invalid.
	 */
	Globalize.parseNumber =
	Globalize.prototype.parseNumber = function( value, options ) {
		validateParameterPresence( value, "value" );
		validateParameterTypeString( value, "value" );

		return this.numberParser( options )( value );
	};

	/**
	 * Optimization to avoid duplicating some internal functions across modules.
	 */
	Globalize._createErrorUnsupportedFeature = createErrorUnsupportedFeature;
	Globalize._numberNumberingSystem = numberNumberingSystem;
	Globalize._numberPattern = numberPattern;
	Globalize._numberSymbol = numberSymbol;
	Globalize._stringPad = stringPad;
	Globalize._validateParameterTypeNumber = validateParameterTypeNumber;
	Globalize._validateParameterTypeString = validateParameterTypeString;

	return Globalize;




	}));


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
	 * Globalize v1.0.0
	 *
	 * http://github.com/jquery/globalize
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-04-23T12:02Z
	 */
	/*!
	 * Globalize v1.0.0 2015-04-23T12:02Z Released under the MIT license
	 * http://git.io/TrdQbw
	 */
	(function( root, factory ) {

		// UMD returnExports
		if ( true ) {

			// AMD
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [
				__webpack_require__(4),
				__webpack_require__(1),
				__webpack_require__(2),
				__webpack_require__(5),
				__webpack_require__(6)
			], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if ( typeof exports === "object" ) {

			// Node, CommonJS
			module.exports = factory( require( "cldrjs" ), require( "globalize" ) );
		} else {

			// Extend global
			factory( root.Cldr, root.Globalize );
		}
	}(this, function( Cldr, Globalize ) {

	var createError = Globalize._createError,
		createErrorUnsupportedFeature = Globalize._createErrorUnsupportedFeature,
		formatMessage = Globalize._formatMessage,
		numberSymbol = Globalize._numberSymbol,
		regexpEscape = Globalize._regexpEscape,
		stringPad = Globalize._stringPad,
		validateCldr = Globalize._validateCldr,
		validateDefaultLocale = Globalize._validateDefaultLocale,
		validateParameterPresence = Globalize._validateParameterPresence,
		validateParameterType = Globalize._validateParameterType,
		validateParameterTypePlainObject = Globalize._validateParameterTypePlainObject,
		validateParameterTypeString = Globalize._validateParameterTypeString;


	var validateParameterTypeDate = function( value, name ) {
		validateParameterType( value, name, value === undefined || value instanceof Date, "Date" );
	};




	var createErrorInvalidParameterValue = function( name, value ) {
		return createError( "E_INVALID_PAR_VALUE", "Invalid `{name}` value ({value}).", {
			name: name,
			value: value
		});
	};




	/**
	 * expandPattern( options, cldr )
	 *
	 * @options [Object] if String, it's considered a skeleton. Object accepts:
	 * - skeleton: [String] lookup availableFormat;
	 * - date: [String] ( "full" | "long" | "medium" | "short" );
	 * - time: [String] ( "full" | "long" | "medium" | "short" );
	 * - datetime: [String] ( "full" | "long" | "medium" | "short" );
	 * - raw: [String] For more info see datetime/format.js.
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return the corresponding pattern.
	 * Eg for "en":
	 * - "GyMMMd" returns "MMM d, y G";
	 * - { skeleton: "GyMMMd" } returns "MMM d, y G";
	 * - { date: "full" } returns "EEEE, MMMM d, y";
	 * - { time: "full" } returns "h:mm:ss a zzzz";
	 * - { datetime: "full" } returns "EEEE, MMMM d, y 'at' h:mm:ss a zzzz";
	 * - { raw: "dd/mm" } returns "dd/mm";
	 */

	var dateExpandPattern = function( options, cldr ) {
		var dateSkeleton, result, skeleton, timeSkeleton, type;

		function combineDateTime( type, datePattern, timePattern ) {
			return formatMessage(
				cldr.main([
					"dates/calendars/gregorian/dateTimeFormats",
					type
				]),
				[ timePattern, datePattern ]
			);
		}

		switch ( true ) {
			case "skeleton" in options:
				skeleton = options.skeleton;
				result = cldr.main([
					"dates/calendars/gregorian/dateTimeFormats/availableFormats",
					skeleton
				]);
				if ( !result ) {
					timeSkeleton = skeleton.split( /[^hHKkmsSAzZOvVXx]/ ).slice( -1 )[ 0 ];
					dateSkeleton = skeleton.split( /[^GyYuUrQqMLlwWdDFgEec]/ )[ 0 ];
					if ( /(MMMM|LLLL).*[Ec]/.test( dateSkeleton ) ) {
						type = "full";
					} else if ( /MMMM/g.test( dateSkeleton ) ) {
						type = "long";
					} else if ( /MMM/g.test( dateSkeleton ) || /LLL/g.test( dateSkeleton ) ) {
						type = "medium";
					} else {
						type = "short";
					}
					result = combineDateTime( type,
						cldr.main([
							"dates/calendars/gregorian/dateTimeFormats/availableFormats",
							dateSkeleton
						]),
						cldr.main([
							"dates/calendars/gregorian/dateTimeFormats/availableFormats",
							timeSkeleton
						])
					);
				}
				break;

			case "date" in options:
			case "time" in options:
				result = cldr.main([
					"dates/calendars/gregorian",
					"date" in options ? "dateFormats" : "timeFormats",
					( options.date || options.time )
				]);
				break;

			case "datetime" in options:
				result = combineDateTime( options.datetime,
					cldr.main([ "dates/calendars/gregorian/dateFormats", options.datetime ]),
					cldr.main([ "dates/calendars/gregorian/timeFormats", options.datetime ])
				);
				break;

			case "raw" in options:
				result = options.raw;
				break;

			default:
				throw createErrorInvalidParameterValue({
					name: "options",
					value: options
				});
		}

		return result;
	};




	/**
	 * dayOfWeek( date, firstDay )
	 *
	 * @date
	 *
	 * @firstDay the result of `dateFirstDayOfWeek( cldr )`
	 *
	 * Return the day of the week normalized by the territory's firstDay [0-6].
	 * Eg for "mon":
	 * - return 0 if territory is GB, or BR, or DE, or FR (week starts on "mon");
	 * - return 1 if territory is US (week starts on "sun");
	 * - return 2 if territory is EG (week starts on "sat");
	 */
	var dateDayOfWeek = function( date, firstDay ) {
		return ( date.getDay() - firstDay + 7 ) % 7;
	};




	/**
	 * distanceInDays( from, to )
	 *
	 * Return the distance in days between from and to Dates.
	 */
	var dateDistanceInDays = function( from, to ) {
		var inDays = 864e5;
		return ( to.getTime() - from.getTime() ) / inDays;
	};




	/**
	 * startOf changes the input to the beginning of the given unit.
	 *
	 * For example, starting at the start of a day, resets hours, minutes
	 * seconds and milliseconds to 0. Starting at the month does the same, but
	 * also sets the date to 1.
	 *
	 * Returns the modified date
	 */
	var dateStartOf = function( date, unit ) {
		date = new Date( date.getTime() );
		switch ( unit ) {
			case "year":
				date.setMonth( 0 );
			/* falls through */
			case "month":
				date.setDate( 1 );
			/* falls through */
			case "day":
				date.setHours( 0 );
			/* falls through */
			case "hour":
				date.setMinutes( 0 );
			/* falls through */
			case "minute":
				date.setSeconds( 0 );
			/* falls through */
			case "second":
				date.setMilliseconds( 0 );
		}
		return date;
	};




	/**
	 * dayOfYear
	 *
	 * Return the distance in days of the date to the begin of the year [0-d].
	 */
	var dateDayOfYear = function( date ) {
		return Math.floor( dateDistanceInDays( dateStartOf( date, "year" ), date ) );
	};




	var dateWeekDays = [ "sun", "mon", "tue", "wed", "thu", "fri", "sat" ];




	/**
	 * firstDayOfWeek
	 */
	var dateFirstDayOfWeek = function( cldr ) {
		return dateWeekDays.indexOf( cldr.supplemental.weekData.firstDay() );
	};




	/**
	 * millisecondsInDay
	 */
	var dateMillisecondsInDay = function( date ) {
		// TODO Handle daylight savings discontinuities
		return date - dateStartOf( date, "day" );
	};




	var datePatternRe = (/([a-z])\1*|'([^']|'')+'|''|./ig);




	/**
	 * hourFormat( date, format, timeSeparator, formatNumber )
	 *
	 * Return date's timezone offset according to the format passed.
	 * Eg for format when timezone offset is 180:
	 * - "+H;-H": -3
	 * - "+HHmm;-HHmm": -0300
	 * - "+HH:mm;-HH:mm": -03:00
	 */
	var dateTimezoneHourFormat = function( date, format, timeSeparator, formatNumber ) {
		var absOffset,
			offset = date.getTimezoneOffset();

		absOffset = Math.abs( offset );
		formatNumber = formatNumber || {
			1: function( value ) {
				return stringPad( value, 1 );
			},
			2: function( value ) {
				return stringPad( value, 2 );
			}
		};

		return format

			// Pick the correct sign side (+ or -).
			.split( ";" )[ offset > 0 ? 1 : 0 ]

			// Localize time separator
			.replace( ":", timeSeparator )

			// Update hours offset.
			.replace( /HH?/, function( match ) {
				return formatNumber[ match.length ]( Math.floor( absOffset / 60 ) );
			})

			// Update minutes offset and return.
			.replace( /mm/, function() {
				return formatNumber[ 2 ]( absOffset % 60 );
			});
	};




	/**
	 * format( date, properties )
	 *
	 * @date [Date instance].
	 *
	 * @properties
	 *
	 * TODO Support other calendar types.
	 *
	 * Disclosure: this function borrows excerpts of dojo/date/locale.
	 */
	var dateFormat = function( date, numberFormatters, properties ) {
		var timeSeparator = properties.timeSeparator;

		return properties.pattern.replace( datePatternRe, function( current ) {
			var ret,
				chr = current.charAt( 0 ),
				length = current.length;

			if ( chr === "j" ) {
				// Locale preferred hHKk.
				// http://www.unicode.org/reports/tr35/tr35-dates.html#Time_Data
				chr = properties.preferredTime;
			}

			if ( chr === "Z" ) {
				// Z..ZZZ: same as "xxxx".
				if ( length < 4 ) {
					chr = "x";
					length = 4;

				// ZZZZ: same as "OOOO".
				} else if ( length < 5 ) {
					chr = "O";
					length = 4;

				// ZZZZZ: same as "XXXXX"
				} else {
					chr = "X";
					length = 5;
				}
			}

			switch ( chr ) {

				// Era
				case "G":
					ret = properties.eras[ date.getFullYear() < 0 ? 0 : 1 ];
					break;

				// Year
				case "y":
					// Plain year.
					// The length specifies the padding, but for two letters it also specifies the
					// maximum length.
					ret = date.getFullYear();
					if ( length === 2 ) {
						ret = String( ret );
						ret = +ret.substr( ret.length - 2 );
					}
					break;

				case "Y":
					// Year in "Week of Year"
					// The length specifies the padding, but for two letters it also specifies the
					// maximum length.
					// yearInWeekofYear = date + DaysInAWeek - (dayOfWeek - firstDay) - minDays
					ret = new Date( date.getTime() );
					ret.setDate(
						ret.getDate() + 7 -
						dateDayOfWeek( date, properties.firstDay ) -
						properties.firstDay -
						properties.minDays
					);
					ret = ret.getFullYear();
					if ( length === 2 ) {
						ret = String( ret );
						ret = +ret.substr( ret.length - 2 );
					}
					break;

				// Quarter
				case "Q":
				case "q":
					ret = Math.ceil( ( date.getMonth() + 1 ) / 3 );
					if ( length > 2 ) {
						ret = properties.quarters[ chr ][ length ][ ret ];
					}
					break;

				// Month
				case "M":
				case "L":
					ret = date.getMonth() + 1;
					if ( length > 2 ) {
						ret = properties.months[ chr ][ length ][ ret ];
					}
					break;

				// Week
				case "w":
					// Week of Year.
					// woy = ceil( ( doy + dow of 1/1 ) / 7 ) - minDaysStuff ? 1 : 0.
					// TODO should pad on ww? Not documented, but I guess so.
					ret = dateDayOfWeek( dateStartOf( date, "year" ), properties.firstDay );
					ret = Math.ceil( ( dateDayOfYear( date ) + ret ) / 7 ) -
						( 7 - ret >= properties.minDays ? 0 : 1 );
					break;

				case "W":
					// Week of Month.
					// wom = ceil( ( dom + dow of `1/month` ) / 7 ) - minDaysStuff ? 1 : 0.
					ret = dateDayOfWeek( dateStartOf( date, "month" ), properties.firstDay );
					ret = Math.ceil( ( date.getDate() + ret ) / 7 ) -
						( 7 - ret >= properties.minDays ? 0 : 1 );
					break;

				// Day
				case "d":
					ret = date.getDate();
					break;

				case "D":
					ret = dateDayOfYear( date ) + 1;
					break;

				case "F":
					// Day of Week in month. eg. 2nd Wed in July.
					ret = Math.floor( date.getDate() / 7 ) + 1;
					break;

				// Week day
				case "e":
				case "c":
					if ( length <= 2 ) {
						// Range is [1-7] (deduced by example provided on documentation)
						// TODO Should pad with zeros (not specified in the docs)?
						ret = dateDayOfWeek( date, properties.firstDay ) + 1;
						break;
					}

				/* falls through */
				case "E":
					ret = dateWeekDays[ date.getDay() ];
					ret = properties.days[ chr ][ length ][ ret ];
					break;

				// Period (AM or PM)
				case "a":
					ret = properties.dayPeriods[ date.getHours() < 12 ? "am" : "pm" ];
					break;

				// Hour
				case "h": // 1-12
					ret = ( date.getHours() % 12 ) || 12;
					break;

				case "H": // 0-23
					ret = date.getHours();
					break;

				case "K": // 0-11
					ret = date.getHours() % 12;
					break;

				case "k": // 1-24
					ret = date.getHours() || 24;
					break;

				// Minute
				case "m":
					ret = date.getMinutes();
					break;

				// Second
				case "s":
					ret = date.getSeconds();
					break;

				case "S":
					ret = Math.round( date.getMilliseconds() * Math.pow( 10, length - 3 ) );
					break;

				case "A":
					ret = Math.round( dateMillisecondsInDay( date ) * Math.pow( 10, length - 3 ) );
					break;

				// Zone
				case "z":
				case "O":
					// O: "{gmtFormat}+H;{gmtFormat}-H" or "{gmtZeroFormat}", eg. "GMT-8" or "GMT".
					// OOOO: "{gmtFormat}{hourFormat}" or "{gmtZeroFormat}", eg. "GMT-08:00" or "GMT".
					if ( date.getTimezoneOffset() === 0 ) {
						ret = properties.gmtZeroFormat;
					} else {
						ret = dateTimezoneHourFormat(
							date,
							length < 4 ? "+H;-H" : properties.tzLongHourFormat,
							timeSeparator,
							numberFormatters
						);
						ret = properties.gmtFormat.replace( /\{0\}/, ret );
					}
					break;

				case "X":
					// Same as x*, except it uses "Z" for zero offset.
					if ( date.getTimezoneOffset() === 0 ) {
						ret = "Z";
						break;
					}

				/* falls through */
				case "x":
					// x: hourFormat("+HH;-HH")
					// xx or xxxx: hourFormat("+HHmm;-HHmm")
					// xxx or xxxxx: hourFormat("+HH:mm;-HH:mm")
					ret = length === 1 ? "+HH;-HH" : ( length % 2 ? "+HH:mm;-HH:mm" : "+HHmm;-HHmm" );
					ret = dateTimezoneHourFormat( date, ret, ":" );
					break;

				// timeSeparator
				case ":":
					ret = timeSeparator;
					break;

				// ' literals.
				case "'":
					current = current.replace( /''/, "'" );
					if ( length > 2 ) {
						current = current.slice( 1, -1 );
					}
					ret = current;
					break;

				// Anything else is considered a literal, including [ ,:/.@#], chinese, japonese, and
				// arabic characters.
				default:
					ret = current;
			}
			if ( typeof ret === "number" ) {
				ret = numberFormatters[ length ]( ret );
			}
			return ret;
		});
	};




	/**
	 * properties( pattern, cldr )
	 *
	 * @pattern [String] raw pattern.
	 * ref: http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return the properties given the pattern and cldr.
	 *
	 * TODO Support other calendar types.
	 */
	var dateFormatProperties = function( pattern, cldr ) {
		var properties = {
				pattern: pattern,
				timeSeparator: numberSymbol( "timeSeparator", cldr )
			},
			widths = [ "abbreviated", "wide", "narrow" ];

		function setNumberFormatterPattern( pad ) {
			if ( !properties.numberFormatters ) {
				properties.numberFormatters = {};
			}
			properties.numberFormatters[ pad ] = stringPad( "", pad );
		}

		pattern.replace( datePatternRe, function( current ) {
			var formatNumber,
				chr = current.charAt( 0 ),
				length = current.length;

			if ( chr === "j" ) {
				// Locale preferred hHKk.
				// http://www.unicode.org/reports/tr35/tr35-dates.html#Time_Data
				properties.preferredTime = chr = cldr.supplemental.timeData.preferred();
			}

			// ZZZZ: same as "OOOO".
			if ( chr === "Z" && length === 4 ) {
				chr = "O";
				length = 4;
			}

			switch ( chr ) {

				// Era
				case "G":
					properties.eras = cldr.main([
						"dates/calendars/gregorian/eras",
						length <= 3 ? "eraAbbr" : ( length === 4 ? "eraNames" : "eraNarrow" )
					]);
					break;

				// Year
				case "y":
					// Plain year.
					formatNumber = true;
					break;

				case "Y":
					// Year in "Week of Year"
					properties.firstDay = dateFirstDayOfWeek( cldr );
					properties.minDays = cldr.supplemental.weekData.minDays();
					formatNumber = true;
					break;

				case "u": // Extended year. Need to be implemented.
				case "U": // Cyclic year name. Need to be implemented.
					throw createErrorUnsupportedFeature({
						feature: "year pattern `" + chr + "`"
					});

				// Quarter
				case "Q":
				case "q":
					if ( length > 2 ) {
						if ( !properties.quarters ) {
							properties.quarters = {};
						}
						if ( !properties.quarters[ chr ] ) {
							properties.quarters[ chr ] = {};
						}
						properties.quarters[ chr ][ length ] = cldr.main([
							"dates/calendars/gregorian/quarters",
							chr === "Q" ? "format" : "stand-alone",
							widths[ length - 3 ]
						]);
					} else {
						formatNumber = true;
					}
					break;

				// Month
				case "M":
				case "L":
					if ( length > 2 ) {
						if ( !properties.months ) {
							properties.months = {};
						}
						if ( !properties.months[ chr ] ) {
							properties.months[ chr ] = {};
						}
						properties.months[ chr ][ length ] = cldr.main([
							"dates/calendars/gregorian/months",
							chr === "M" ? "format" : "stand-alone",
							widths[ length - 3 ]
						]);
					} else {
						formatNumber = true;
					}
					break;

				// Week - Week of Year (w) or Week of Month (W).
				case "w":
				case "W":
					properties.firstDay = dateFirstDayOfWeek( cldr );
					properties.minDays = cldr.supplemental.weekData.minDays();
					formatNumber = true;
					break;

				// Day
				case "d":
				case "D":
				case "F":
					formatNumber = true;
					break;

				case "g":
					// Modified Julian day. Need to be implemented.
					throw createErrorUnsupportedFeature({
						feature: "Julian day pattern `g`"
					});

				// Week day
				case "e":
				case "c":
					if ( length <= 2 ) {
						properties.firstDay = dateFirstDayOfWeek( cldr );
						formatNumber = true;
						break;
					}

				/* falls through */
				case "E":
					if ( !properties.days ) {
						properties.days = {};
					}
					if ( !properties.days[ chr ] ) {
						properties.days[ chr ] = {};
					}
					if ( length === 6 ) {

						// If short day names are not explicitly specified, abbreviated day names are
						// used instead.
						// http://www.unicode.org/reports/tr35/tr35-dates.html#months_days_quarters_eras
						// http://unicode.org/cldr/trac/ticket/6790
						properties.days[ chr ][ length ] = cldr.main([
								"dates/calendars/gregorian/days",
								chr === "c" ? "stand-alone" : "format",
								"short"
							]) || cldr.main([
								"dates/calendars/gregorian/days",
								chr === "c" ? "stand-alone" : "format",
								"abbreviated"
							]);
					} else {
						properties.days[ chr ][ length ] = cldr.main([
							"dates/calendars/gregorian/days",
							chr === "c" ? "stand-alone" : "format",
							widths[ length < 3 ? 0 : length - 3 ]
						]);
					}
					break;

				// Period (AM or PM)
				case "a":
					properties.dayPeriods = cldr.main(
						"dates/calendars/gregorian/dayPeriods/format/wide"
					);
					break;

				// Hour
				case "h": // 1-12
				case "H": // 0-23
				case "K": // 0-11
				case "k": // 1-24

				// Minute
				case "m":

				// Second
				case "s":
				case "S":
				case "A":
					formatNumber = true;
					break;

				// Zone
				case "z":
				case "O":
					// O: "{gmtFormat}+H;{gmtFormat}-H" or "{gmtZeroFormat}", eg. "GMT-8" or "GMT".
					// OOOO: "{gmtFormat}{hourFormat}" or "{gmtZeroFormat}", eg. "GMT-08:00" or "GMT".
					properties.gmtFormat = cldr.main( "dates/timeZoneNames/gmtFormat" );
					properties.gmtZeroFormat = cldr.main( "dates/timeZoneNames/gmtZeroFormat" );
					properties.tzLongHourFormat = cldr.main( "dates/timeZoneNames/hourFormat" );

				/* falls through */
				case "Z":
				case "X":
				case "x":
					setNumberFormatterPattern( 1 );
					setNumberFormatterPattern( 2 );
					break;

				case "v":
				case "V":
					throw createErrorUnsupportedFeature({
						feature: "timezone pattern `" + chr + "`"
					});
			}

			if ( formatNumber ) {
				setNumberFormatterPattern( length );
			}
		});

		return properties;
	};




	/**
	 * isLeapYear( year )
	 *
	 * @year [Number]
	 *
	 * Returns an indication whether the specified year is a leap year.
	 */
	var dateIsLeapYear = function( year ) {
		return new Date(year, 1, 29).getMonth() === 1;
	};




	/**
	 * lastDayOfMonth( date )
	 *
	 * @date [Date]
	 *
	 * Return the last day of the given date's month
	 */
	var dateLastDayOfMonth = function( date ) {
		return new Date( date.getFullYear(), date.getMonth() + 1, 0).getDate();
	};




	/**
	 * Differently from native date.setDate(), this function returns a date whose
	 * day remains inside the month boundaries. For example:
	 *
	 * setDate( FebDate, 31 ): a "Feb 28" date.
	 * setDate( SepDate, 31 ): a "Sep 30" date.
	 */
	var dateSetDate = function( date, day ) {
		var lastDay = new Date( date.getFullYear(), date.getMonth() + 1, 0 ).getDate();

		date.setDate( day < 1 ? 1 : day < lastDay ? day : lastDay );
	};




	/**
	 * Differently from native date.setMonth(), this function adjusts date if
	 * needed, so final month is always the one set.
	 *
	 * setMonth( Jan31Date, 1 ): a "Feb 28" date.
	 * setDate( Jan31Date, 8 ): a "Sep 30" date.
	 */
	var dateSetMonth = function( date, month ) {
		var originalDate = date.getDate();

		date.setDate( 1 );
		date.setMonth( month );
		dateSetDate( date, originalDate );
	};




	var outOfRange = function( value, low, high ) {
		return value < low || value > high;
	};




	/**
	 * parse( value, tokens, properties )
	 *
	 * @value [String] string date.
	 *
	 * @tokens [Object] tokens returned by date/tokenizer.
	 *
	 * @properties [Object] output returned by date/tokenizer-properties.
	 *
	 * ref: http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
	 */
	var dateParse = function( value, tokens, properties ) {
		var amPm, day, daysOfYear, era, hour, hour12, timezoneOffset, valid,
			YEAR = 0,
			MONTH = 1,
			DAY = 2,
			HOUR = 3,
			MINUTE = 4,
			SECOND = 5,
			MILLISECONDS = 6,
			date = new Date(),
			truncateAt = [],
			units = [ "year", "month", "day", "hour", "minute", "second", "milliseconds" ];

		if ( !tokens.length ) {
			return null;
		}

		valid = tokens.every(function( token ) {
			var century, chr, value, length;

			if ( token.type === "literal" ) {
				// continue
				return true;
			}

			chr = token.type.charAt( 0 );
			length = token.type.length;

			if ( chr === "j" ) {
				// Locale preferred hHKk.
				// http://www.unicode.org/reports/tr35/tr35-dates.html#Time_Data
				chr = properties.preferredTimeData;
			}

			switch ( chr ) {

				// Era
				case "G":
					truncateAt.push( YEAR );
					era = +token.value;
					break;

				// Year
				case "y":
					value = token.value;
					if ( length === 2 ) {
						if ( outOfRange( value, 0, 99 ) ) {
							return false;
						}
						// mimic dojo/date/locale: choose century to apply, according to a sliding
						// window of 80 years before and 20 years after present year.
						century = Math.floor( date.getFullYear() / 100 ) * 100;
						value += century;
						if ( value > date.getFullYear() + 20 ) {
							value -= 100;
						}
					}
					date.setFullYear( value );
					truncateAt.push( YEAR );
					break;

				case "Y": // Year in "Week of Year"
					throw createErrorUnsupportedFeature({
						feature: "year pattern `" + chr + "`"
					});

				// Quarter (skip)
				case "Q":
				case "q":
					break;

				// Month
				case "M":
				case "L":
					if ( length <= 2 ) {
						value = token.value;
					} else {
						value = +token.value;
					}
					if ( outOfRange( value, 1, 12 ) ) {
						return false;
					}
					dateSetMonth( date, value - 1 );
					truncateAt.push( MONTH );
					break;

				// Week (skip)
				case "w": // Week of Year.
				case "W": // Week of Month.
					break;

				// Day
				case "d":
					day = token.value;
					truncateAt.push( DAY );
					break;

				case "D":
					daysOfYear = token.value;
					truncateAt.push( DAY );
					break;

				case "F":
					// Day of Week in month. eg. 2nd Wed in July.
					// Skip
					break;

				// Week day
				case "e":
				case "c":
				case "E":
					// Skip.
					// value = arrayIndexOf( dateWeekDays, token.value );
					break;

				// Period (AM or PM)
				case "a":
					amPm = token.value;
					break;

				// Hour
				case "h": // 1-12
					value = token.value;
					if ( outOfRange( value, 1, 12 ) ) {
						return false;
					}
					hour = hour12 = true;
					date.setHours( value === 12 ? 0 : value );
					truncateAt.push( HOUR );
					break;

				case "K": // 0-11
					value = token.value;
					if ( outOfRange( value, 0, 11 ) ) {
						return false;
					}
					hour = hour12 = true;
					date.setHours( value );
					truncateAt.push( HOUR );
					break;

				case "k": // 1-24
					value = token.value;
					if ( outOfRange( value, 1, 24 ) ) {
						return false;
					}
					hour = true;
					date.setHours( value === 24 ? 0 : value );
					truncateAt.push( HOUR );
					break;

				case "H": // 0-23
					value = token.value;
					if ( outOfRange( value, 0, 23 ) ) {
						return false;
					}
					hour = true;
					date.setHours( value );
					truncateAt.push( HOUR );
					break;

				// Minute
				case "m":
					value = token.value;
					if ( outOfRange( value, 0, 59 ) ) {
						return false;
					}
					date.setMinutes( value );
					truncateAt.push( MINUTE );
					break;

				// Second
				case "s":
					value = token.value;
					if ( outOfRange( value, 0, 59 ) ) {
						return false;
					}
					date.setSeconds( value );
					truncateAt.push( SECOND );
					break;

				case "A":
					date.setHours( 0 );
					date.setMinutes( 0 );
					date.setSeconds( 0 );

				/* falls through */
				case "S":
					value = Math.round( token.value * Math.pow( 10, 3 - length ) );
					date.setMilliseconds( value );
					truncateAt.push( MILLISECONDS );
					break;

				// Zone
				case "Z":
				case "z":
				case "O":
				case "X":
				case "x":
					timezoneOffset = token.value - date.getTimezoneOffset();
					break;
			}

			return true;
		});

		if ( !valid ) {
			return null;
		}

		// 12-hour format needs AM or PM, 24-hour format doesn't, ie. return null
		// if amPm && !hour12 || !amPm && hour12.
		if ( hour && !( !amPm ^ hour12 ) ) {
			return null;
		}

		if ( era === 0 ) {
			// 1 BC = year 0
			date.setFullYear( date.getFullYear() * -1 + 1 );
		}

		if ( day !== undefined ) {
			if ( outOfRange( day, 1, dateLastDayOfMonth( date ) ) ) {
				return null;
			}
			date.setDate( day );
		} else if ( daysOfYear !== undefined ) {
			if ( outOfRange( daysOfYear, 1, dateIsLeapYear( date.getFullYear() ) ? 366 : 365 ) ) {
				return null;
			}
			date.setMonth(0);
			date.setDate( daysOfYear );
		}

		if ( hour12 && amPm === "pm" ) {
			date.setHours( date.getHours() + 12 );
		}

		if ( timezoneOffset ) {
			date.setMinutes( date.getMinutes() + timezoneOffset );
		}

		// Truncate date at the most precise unit defined. Eg.
		// If value is "12/31", and pattern is "MM/dd":
		// => new Date( <current Year>, 12, 31, 0, 0, 0, 0 );
		truncateAt = Math.max.apply( null, truncateAt );
		date = dateStartOf( date, units[ truncateAt ] );

		return date;
	};




	/**
	 * parseProperties( cldr )
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return parser properties.
	 */
	var dateParseProperties = function( cldr ) {
		return {
			preferredTimeData: cldr.supplemental.timeData.preferred()
		};
	};




	/**
	 * Generated by:
	 *
	 * regenerate().add( require( "unicode-7.0.0/categories/N/symbols" ) ).toString();
	 *
	 * https://github.com/mathiasbynens/regenerate
	 * https://github.com/mathiasbynens/unicode-7.0.0
	 */
	var regexpN = /[0-9\xB2\xB3\xB9\xBC-\xBE\u0660-\u0669\u06F0-\u06F9\u07C0-\u07C9\u0966-\u096F\u09E6-\u09EF\u09F4-\u09F9\u0A66-\u0A6F\u0AE6-\u0AEF\u0B66-\u0B6F\u0B72-\u0B77\u0BE6-\u0BF2\u0C66-\u0C6F\u0C78-\u0C7E\u0CE6-\u0CEF\u0D66-\u0D75\u0DE6-\u0DEF\u0E50-\u0E59\u0ED0-\u0ED9\u0F20-\u0F33\u1040-\u1049\u1090-\u1099\u1369-\u137C\u16EE-\u16F0\u17E0-\u17E9\u17F0-\u17F9\u1810-\u1819\u1946-\u194F\u19D0-\u19DA\u1A80-\u1A89\u1A90-\u1A99\u1B50-\u1B59\u1BB0-\u1BB9\u1C40-\u1C49\u1C50-\u1C59\u2070\u2074-\u2079\u2080-\u2089\u2150-\u2182\u2185-\u2189\u2460-\u249B\u24EA-\u24FF\u2776-\u2793\u2CFD\u3007\u3021-\u3029\u3038-\u303A\u3192-\u3195\u3220-\u3229\u3248-\u324F\u3251-\u325F\u3280-\u3289\u32B1-\u32BF\uA620-\uA629\uA6E6-\uA6EF\uA830-\uA835\uA8D0-\uA8D9\uA900-\uA909\uA9D0-\uA9D9\uA9F0-\uA9F9\uAA50-\uAA59\uABF0-\uABF9\uFF10-\uFF19]|\uD800[\uDD07-\uDD33\uDD40-\uDD78\uDD8A\uDD8B\uDEE1-\uDEFB\uDF20-\uDF23\uDF41\uDF4A\uDFD1-\uDFD5]|\uD801[\uDCA0-\uDCA9]|\uD802[\uDC58-\uDC5F\uDC79-\uDC7F\uDCA7-\uDCAF\uDD16-\uDD1B\uDE40-\uDE47\uDE7D\uDE7E\uDE9D-\uDE9F\uDEEB-\uDEEF\uDF58-\uDF5F\uDF78-\uDF7F\uDFA9-\uDFAF]|\uD803[\uDE60-\uDE7E]|\uD804[\uDC52-\uDC6F\uDCF0-\uDCF9\uDD36-\uDD3F\uDDD0-\uDDD9\uDDE1-\uDDF4\uDEF0-\uDEF9]|\uD805[\uDCD0-\uDCD9\uDE50-\uDE59\uDEC0-\uDEC9]|\uD806[\uDCE0-\uDCF2]|\uD809[\uDC00-\uDC6E]|\uD81A[\uDE60-\uDE69\uDF50-\uDF59\uDF5B-\uDF61]|\uD834[\uDF60-\uDF71]|\uD835[\uDFCE-\uDFFF]|\uD83A[\uDCC7-\uDCCF]|\uD83C[\uDD00-\uDD0C]/;




	/**
	 * tokenizer( value, pattern, properties )
	 *
	 * @value [String] string date.
	 *
	 * @properties [Object] output returned by date/tokenizer-properties.
	 *
	 * Returns an Array of tokens, eg. value "5 o'clock PM", pattern "h 'o''clock' a":
	 * [{
	 *   type: "h",
	 *   lexeme: "5"
	 * }, {
	 *   type: "literal",
	 *   lexeme: " "
	 * }, {
	 *   type: "literal",
	 *   lexeme: "o'clock"
	 * }, {
	 *   type: "literal",
	 *   lexeme: " "
	 * }, {
	 *   type: "a",
	 *   lexeme: "PM",
	 *   value: "pm"
	 * }]
	 *
	 * OBS: lexeme's are always String and may return invalid ranges depending of the token type.
	 * Eg. "99" for month number.
	 *
	 * Return an empty Array when not successfully parsed.
	 */
	var dateTokenizer = function( value, numberParser, properties ) {
		var valid,
			timeSeparator = properties.timeSeparator,
			tokens = [],
			widths = [ "abbreviated", "wide", "narrow" ];

		valid = properties.pattern.match( datePatternRe ).every(function( current ) {
			var chr, length, numeric, tokenRe,
				token = {};

			function hourFormatParse( tokenRe, numberParser ) {
				var aux = value.match( tokenRe );
				numberParser = numberParser || function( value ) {
					return +value;
				};

				if ( !aux ) {
					return false;
				}

				// hourFormat containing H only, e.g., `+H;-H`
				if ( aux.length < 8 ) {
					token.value =
						( aux[ 1 ] ? -numberParser( aux[ 1 ] ) : numberParser( aux[ 4 ] ) ) * 60;

				// hourFormat containing H and m, e.g., `+HHmm;-HHmm`
				} else {
					token.value =
						( aux[ 1 ] ? -numberParser( aux[ 1 ] ) : numberParser( aux[ 7 ] ) ) * 60 +
						( aux[ 1 ] ? -numberParser( aux[ 4 ] ) : numberParser( aux[ 10 ] ) );
				}

				return true;
			}

			// Transform:
			// - "+H;-H" -> /\+(\d\d?)|-(\d\d?)/
			// - "+HH;-HH" -> /\+(\d\d)|-(\d\d)/
			// - "+HHmm;-HHmm" -> /\+(\d\d)(\d\d)|-(\d\d)(\d\d)/
			// - "+HH:mm;-HH:mm" -> /\+(\d\d):(\d\d)|-(\d\d):(\d\d)/
			//
			// If gmtFormat is GMT{0}, the regexp must fill {0} in each side, e.g.:
			// - "+H;-H" -> /GMT\+(\d\d?)|GMT-(\d\d?)/
			function hourFormatRe( hourFormat, gmtFormat, timeSeparator ) {
				var re;

				if ( !gmtFormat ) {
					gmtFormat = "{0}";
				}

				re = hourFormat
					.replace( "+", "\\+" )

					// Unicode equivalent to (\\d\\d)
					.replace( /HH|mm/g, "((" + regexpN.source + ")(" + regexpN.source + "))" )

					// Unicode equivalent to (\\d\\d?)
					.replace( /H|m/g, "((" + regexpN.source + ")(" + regexpN.source + ")?)" );

				if ( timeSeparator ) {
					re = re.replace( /:/g, timeSeparator );
				}

				re = re.split( ";" ).map(function( part ) {
					return gmtFormat.replace( "{0}", part );
				}).join( "|" );

				return new RegExp( re );
			}

			function oneDigitIfLengthOne() {
				if ( length === 1 ) {

					// Unicode equivalent to /\d/
					numeric = true;
					return tokenRe = regexpN;
				}
			}

			function oneOrTwoDigitsIfLengthOne() {
				if ( length === 1 ) {

					// Unicode equivalent to /\d\d?/
					numeric = true;
					return tokenRe = new RegExp( "(" + regexpN.source + ")(" + regexpN.source + ")?" );
				}
			}

			function twoDigitsIfLengthTwo() {
				if ( length === 2 ) {

					// Unicode equivalent to /\d\d/
					numeric = true;
					return tokenRe = new RegExp( "(" + regexpN.source + ")(" + regexpN.source + ")" );
				}
			}

			// Brute-force test every locale entry in an attempt to match the given value.
			// Return the first found one (and set token accordingly), or null.
			function lookup( path ) {
				var i, re,
					data = properties[ path.join( "/" ) ];

				for ( i in data ) {
					re = new RegExp( "^" + data[ i ] );
					if ( re.test( value ) ) {
						token.value = i;
						return tokenRe = new RegExp( data[ i ] );
					}
				}
				return null;
			}

			token.type = current;
			chr = current.charAt( 0 ),
			length = current.length;

			if ( chr === "Z" ) {
				// Z..ZZZ: same as "xxxx".
				if ( length < 4 ) {
					chr = "x";
					length = 4;

				// ZZZZ: same as "OOOO".
				} else if ( length < 5 ) {
					chr = "O";
					length = 4;

				// ZZZZZ: same as "XXXXX"
				} else {
					chr = "X";
					length = 5;
				}
			}

			switch ( chr ) {

				// Era
				case "G":
					lookup([
						"gregorian/eras",
						length <= 3 ? "eraAbbr" : ( length === 4 ? "eraNames" : "eraNarrow" )
					]);
					break;

				// Year
				case "y":
				case "Y":
					numeric = true;

					// number l=1:+, l=2:{2}, l=3:{3,}, l=4:{4,}, ...
					if ( length === 1 ) {

						// Unicode equivalent to /\d+/.
						tokenRe = new RegExp( "(" + regexpN.source + ")+" );
					} else if ( length === 2 ) {

						// Unicode equivalent to /\d\d/
						tokenRe = new RegExp( "(" + regexpN.source + ")(" + regexpN.source + ")" );
					} else {

						// Unicode equivalent to /\d{length,}/
						tokenRe = new RegExp( "(" + regexpN.source + "){" + length + ",}" );
					}
					break;

				// Quarter
				case "Q":
				case "q":
					// number l=1:{1}, l=2:{2}.
					// lookup l=3...
					oneDigitIfLengthOne() || twoDigitsIfLengthTwo() || lookup([
						"gregorian/quarters",
						chr === "Q" ? "format" : "stand-alone",
						widths[ length - 3 ]
					]);
					break;

				// Month
				case "M":
				case "L":
					// number l=1:{1,2}, l=2:{2}.
					// lookup l=3...
					oneOrTwoDigitsIfLengthOne() || twoDigitsIfLengthTwo() || lookup([
						"gregorian/months",
						chr === "M" ? "format" : "stand-alone",
						widths[ length - 3 ]
					]);
					break;

				// Day
				case "D":
					// number {l,3}.
					if ( length <= 3 ) {

						// Unicode equivalent to /\d{length,3}/
						numeric = true;
						tokenRe = new RegExp( "(" + regexpN.source + "){" + length + ",3}" );
					}
					break;

				case "W":
				case "F":
					// number l=1:{1}.
					oneDigitIfLengthOne();
					break;

				// Week day
				case "e":
				case "c":
					// number l=1:{1}, l=2:{2}.
					// lookup for length >=3.
					if ( length <= 2 ) {
						oneDigitIfLengthOne() || twoDigitsIfLengthTwo();
						break;
					}

				/* falls through */
				case "E":
					if ( length === 6 ) {
						// Note: if short day names are not explicitly specified, abbreviated day
						// names are used instead http://www.unicode.org/reports/tr35/tr35-dates.html#months_days_quarters_eras
						lookup([
							"gregorian/days",
							[ chr === "c" ? "stand-alone" : "format" ],
							"short"
						]) || lookup([
							"gregorian/days",
							[ chr === "c" ? "stand-alone" : "format" ],
							"abbreviated"
						]);
					} else {
						lookup([
							"gregorian/days",
							[ chr === "c" ? "stand-alone" : "format" ],
							widths[ length < 3 ? 0 : length - 3 ]
						]);
					}
					break;

				// Period (AM or PM)
				case "a":
					lookup([
						"gregorian/dayPeriods/format/wide"
					]);
					break;

				// Week, Day, Hour, Minute, or Second
				case "w":
				case "d":
				case "h":
				case "H":
				case "K":
				case "k":
				case "j":
				case "m":
				case "s":
					// number l1:{1,2}, l2:{2}.
					oneOrTwoDigitsIfLengthOne() || twoDigitsIfLengthTwo();
					break;

				case "S":
					// number {l}.

					// Unicode equivalent to /\d{length}/
					numeric = true;
					tokenRe = new RegExp( "(" + regexpN.source + "){" + length + "}" );
					break;

				case "A":
					// number {l+5}.

					// Unicode equivalent to /\d{length+5}/
					numeric = true;
					tokenRe = new RegExp( "(" + regexpN.source + "){" + ( length + 5 ) + "}" );
					break;

				// Zone
				case "z":
				case "O":
					// O: "{gmtFormat}+H;{gmtFormat}-H" or "{gmtZeroFormat}", eg. "GMT-8" or "GMT".
					// OOOO: "{gmtFormat}{hourFormat}" or "{gmtZeroFormat}", eg. "GMT-08:00" or "GMT".
					if ( value === properties[ "timeZoneNames/gmtZeroFormat" ] ) {
						token.value = 0;
						tokenRe = new RegExp( properties[ "timeZoneNames/gmtZeroFormat" ] );
					} else {
						tokenRe = hourFormatRe(
							length < 4 ? "+H;-H" : properties[ "timeZoneNames/hourFormat" ],
							properties[ "timeZoneNames/gmtFormat" ],
							timeSeparator
						);
						if ( !hourFormatParse( tokenRe, numberParser ) ) {
							return null;
						}
					}
					break;

				case "X":
					// Same as x*, except it uses "Z" for zero offset.
					if ( value === "Z" ) {
						token.value = 0;
						tokenRe = /Z/;
						break;
					}

				/* falls through */
				case "x":
					// x: hourFormat("+HH;-HH")
					// xx or xxxx: hourFormat("+HHmm;-HHmm")
					// xxx or xxxxx: hourFormat("+HH:mm;-HH:mm")
					tokenRe = hourFormatRe(
						length === 1 ? "+HH;-HH" : ( length % 2 ? "+HH:mm;-HH:mm" : "+HHmm;-HHmm" )
					);
					if ( !hourFormatParse( tokenRe ) ) {
						return null;
					}
					break;

				case "'":
					token.type = "literal";
					current = current.replace( /''/, "'" );
					if ( length > 2 ) {
						current = current.slice( 1, -1 );
					}
					tokenRe = new RegExp( regexpEscape( current ) );
					break;

				default:
					token.type = "literal";
					tokenRe = /./;
			}

			if ( !tokenRe ) {
				return false;
			}

			// Get lexeme and consume it.
			value = value.replace( new RegExp( "^" + tokenRe.source ), function( lexeme ) {
				token.lexeme = lexeme;
				if ( numeric ) {
					token.value = numberParser( lexeme );
				}
				return "";
			});

			if ( !token.lexeme ) {
				return false;
			}

			tokens.push( token );
			return true;
		});

		return valid ? tokens : [];
	};




	/**
	 * tokenizerProperties( pattern, cldr )
	 *
	 * @pattern [String] raw pattern.
	 *
	 * @cldr [Cldr instance].
	 *
	 * Return Object with data that will be used by tokenizer.
	 */
	var dateTokenizerProperties = function( pattern, cldr ) {
		var properties = {
				pattern: pattern,
				timeSeparator: numberSymbol( "timeSeparator", cldr )
			},
			widths = [ "abbreviated", "wide", "narrow" ];

		function populateProperties( path, value ) {

			// The `dates` and `calendars` trim's purpose is to reduce properties' key size only.
			properties[ path.replace( /^.*\/dates\//, "" ).replace( /calendars\//, "" ) ] = value;
		}

		cldr.on( "get", populateProperties );

		pattern.match( datePatternRe ).forEach(function( current ) {
			var chr, length;

			chr = current.charAt( 0 ),
			length = current.length;

			if ( chr === "Z" && length < 5 ) {
					chr = "O";
					length = 4;
			}

			switch ( chr ) {

				// Era
				case "G":
					cldr.main([
						"dates/calendars/gregorian/eras",
						length <= 3 ? "eraAbbr" : ( length === 4 ? "eraNames" : "eraNarrow" )
					]);
					break;

				// Year
				case "u": // Extended year. Need to be implemented.
				case "U": // Cyclic year name. Need to be implemented.
					throw createErrorUnsupportedFeature({
						feature: "year pattern `" + chr + "`"
					});

				// Quarter
				case "Q":
				case "q":
					if ( length > 2 ) {
						cldr.main([
							"dates/calendars/gregorian/quarters",
							chr === "Q" ? "format" : "stand-alone",
							widths[ length - 3 ]
						]);
					}
					break;

				// Month
				case "M":
				case "L":
					// number l=1:{1,2}, l=2:{2}.
					// lookup l=3...
					if ( length > 2 ) {
						cldr.main([
							"dates/calendars/gregorian/months",
							chr === "M" ? "format" : "stand-alone",
							widths[ length - 3 ]
						]);
					}
					break;

				// Day
				case "g":
					// Modified Julian day. Need to be implemented.
					throw createErrorUnsupportedFeature({
						feature: "Julian day pattern `g`"
					});

				// Week day
				case "e":
				case "c":
					// lookup for length >=3.
					if ( length <= 2 ) {
						break;
					}

				/* falls through */
				case "E":
					if ( length === 6 ) {
						// Note: if short day names are not explicitly specified, abbreviated day
						// names are used instead http://www.unicode.org/reports/tr35/tr35-dates.html#months_days_quarters_eras
						cldr.main([
							"dates/calendars/gregorian/days",
							[ chr === "c" ? "stand-alone" : "format" ],
							"short"
						]) || cldr.main([
							"dates/calendars/gregorian/days",
							[ chr === "c" ? "stand-alone" : "format" ],
							"abbreviated"
						]);
					} else {
						cldr.main([
							"dates/calendars/gregorian/days",
							[ chr === "c" ? "stand-alone" : "format" ],
							widths[ length < 3 ? 0 : length - 3 ]
						]);
					}
					break;

				// Period (AM or PM)
				case "a":
					cldr.main([
						"dates/calendars/gregorian/dayPeriods/format/wide"
					]);
					break;

				// Zone
				case "z":
				case "O":
					cldr.main( "dates/timeZoneNames/gmtFormat" );
					cldr.main( "dates/timeZoneNames/gmtZeroFormat" );
					cldr.main( "dates/timeZoneNames/hourFormat" );
					break;

				case "v":
				case "V":
					throw createErrorUnsupportedFeature({
						feature: "timezone pattern `" + chr + "`"
					});
			}
		});

		cldr.off( "get", populateProperties );

		return properties;
	};




	function validateRequiredCldr( path, value ) {
		validateCldr( path, value, {
			skip: [
				/dates\/calendars\/gregorian\/dateTimeFormats\/availableFormats/,
				/dates\/calendars\/gregorian\/days\/.*\/short/,
				/supplemental\/timeData\/(?!001)/,
				/supplemental\/weekData\/(?!001)/
			]
		});
	}

	/**
	 * .dateFormatter( options )
	 *
	 * @options [Object] see date/expand_pattern for more info.
	 *
	 * Return a date formatter function (of the form below) according to the given options and the
	 * default/instance locale.
	 *
	 * fn( value )
	 *
	 * @value [Date]
	 *
	 * Return a function that formats a date according to the given `format` and the default/instance
	 * locale.
	 */
	Globalize.dateFormatter =
	Globalize.prototype.dateFormatter = function( options ) {
		var cldr, numberFormatters, pad, pattern, properties;

		validateParameterTypePlainObject( options, "options" );

		cldr = this.cldr;
		options = options || { skeleton: "yMd" };

		validateDefaultLocale( cldr );

		cldr.on( "get", validateRequiredCldr );
		pattern = dateExpandPattern( options, cldr );
		properties = dateFormatProperties( pattern, cldr );
		cldr.off( "get", validateRequiredCldr );

		// Create needed number formatters.
		numberFormatters = properties.numberFormatters;
		delete properties.numberFormatters;
		for ( pad in numberFormatters ) {
			numberFormatters[ pad ] = this.numberFormatter({
				raw: numberFormatters[ pad ]
			});
		}

		return function( value ) {
			validateParameterPresence( value, "value" );
			validateParameterTypeDate( value, "value" );
			return dateFormat( value, numberFormatters, properties );
		};
	};

	/**
	 * .dateParser( options )
	 *
	 * @options [Object] see date/expand_pattern for more info.
	 *
	 * Return a function that parses a string date according to the given `formats` and the
	 * default/instance locale.
	 */
	Globalize.dateParser =
	Globalize.prototype.dateParser = function( options ) {
		var cldr, numberParser, parseProperties, pattern, tokenizerProperties;

		validateParameterTypePlainObject( options, "options" );

		cldr = this.cldr;
		options = options || { skeleton: "yMd" };

		validateDefaultLocale( cldr );

		cldr.on( "get", validateRequiredCldr );
		pattern = dateExpandPattern( options, cldr );
		tokenizerProperties = dateTokenizerProperties( pattern, cldr );
		parseProperties = dateParseProperties( cldr );
		cldr.off( "get", validateRequiredCldr );

		numberParser = this.numberParser({ raw: "0" });

		return function( value ) {
			var tokens;

			validateParameterPresence( value, "value" );
			validateParameterTypeString( value, "value" );

			tokens = dateTokenizer( value, numberParser, tokenizerProperties );
			return dateParse( value, tokens, parseProperties ) || null;
		};
	};

	/**
	 * .formatDate( value, options )
	 *
	 * @value [Date]
	 *
	 * @options [Object] see date/expand_pattern for more info.
	 *
	 * Formats a date or number according to the given options string and the default/instance locale.
	 */
	Globalize.formatDate =
	Globalize.prototype.formatDate = function( value, options ) {
		validateParameterPresence( value, "value" );
		validateParameterTypeDate( value, "value" );

		return this.dateFormatter( options )( value );
	};

	/**
	 * .parseDate( value, options )
	 *
	 * @value [String]
	 *
	 * @options [Object] see date/expand_pattern for more info.
	 *
	 * Return a Date instance or null.
	 */
	Globalize.parseDate =
	Globalize.prototype.parseDate = function( value, options ) {
		validateParameterPresence( value, "value" );
		validateParameterTypeString( value, "value" );

		return this.dateParser( options )( value );
	};

	return Globalize;




	}));


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
	 * CLDR JavaScript Library v0.4.1
	 * http://jquery.com/
	 *
	 * Copyright 2013 Rafael Xavier de Souza
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-02-25T13:51Z
	 */
	/*!
	 * CLDR JavaScript Library v0.4.1 2015-02-25T13:51Z MIT license © Rafael Xavier
	 * http://git.io/h4lmVg
	 */
	(function( root, factory ) {

		if ( true ) {
			// AMD.
			!(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if ( typeof module === "object" && typeof module.exports === "object" ) {
			// Node. CommonJS.
			module.exports = factory();
		} else {
			// Global
			root.Cldr = factory();
		}

	}( this, function() {


		var arrayIsArray = Array.isArray || function( obj ) {
			return Object.prototype.toString.call( obj ) === "[object Array]";
		};




		var pathNormalize = function( path, attributes ) {
			if ( arrayIsArray( path ) ) {
				path = path.join( "/" );
			}
			if ( typeof path !== "string" ) {
				throw new Error( "invalid path \"" + path + "\"" );
			}
			// 1: Ignore leading slash `/`
			// 2: Ignore leading `cldr/`
			path = path
				.replace( /^\// , "" ) /* 1 */
				.replace( /^cldr\// , "" ); /* 2 */

			// Replace {attribute}'s
			path = path.replace( /{[a-zA-Z]+}/g, function( name ) {
				name = name.replace( /^{([^}]*)}$/, "$1" );
				return attributes[ name ];
			});

			return path.split( "/" );
		};




		var arraySome = function( array, callback ) {
			var i, length;
			if ( array.some ) {
				return array.some( callback );
			}
			for ( i = 0, length = array.length; i < length; i++ ) {
				if ( callback( array[ i ], i, array ) ) {
					return true;
				}
			}
			return false;
		};




		/**
		 * Return the maximized language id as defined in
		 * http://www.unicode.org/reports/tr35/#Likely_Subtags
		 * 1. Canonicalize.
		 * 1.1 Make sure the input locale is in canonical form: uses the right
		 * separator, and has the right casing.
		 * TODO Right casing? What df? It seems languages are lowercase, scripts are
		 * Capitalized, territory is uppercase. I am leaving this as an exercise to
		 * the user.
		 *
		 * 1.2 Replace any deprecated subtags with their canonical values using the
		 * <alias> data in supplemental metadata. Use the first value in the
		 * replacement list, if it exists. Language tag replacements may have multiple
		 * parts, such as "sh" ➞ "sr_Latn" or mo" ➞ "ro_MD". In such a case, the
		 * original script and/or region are retained if there is one. Thus
		 * "sh_Arab_AQ" ➞ "sr_Arab_AQ", not "sr_Latn_AQ".
		 * TODO What <alias> data?
		 *
		 * 1.3 If the tag is grandfathered (see <variable id="$grandfathered"
		 * type="choice"> in the supplemental data), then return it.
		 * TODO grandfathered?
		 *
		 * 1.4 Remove the script code 'Zzzz' and the region code 'ZZ' if they occur.
		 * 1.5 Get the components of the cleaned-up source tag (languages, scripts,
		 * and regions), plus any variants and extensions.
		 * 2. Lookup. Lookup each of the following in order, and stop on the first
		 * match:
		 * 2.1 languages_scripts_regions
		 * 2.2 languages_regions
		 * 2.3 languages_scripts
		 * 2.4 languages
		 * 2.5 und_scripts
		 * 3. Return
		 * 3.1 If there is no match, either return an error value, or the match for
		 * "und" (in APIs where a valid language tag is required).
		 * 3.2 Otherwise there is a match = languagem_scriptm_regionm
		 * 3.3 Let xr = xs if xs is not empty, and xm otherwise.
		 * 3.4 Return the language tag composed of languager _ scriptr _ regionr +
		 * variants + extensions.
		 *
		 * @subtags [Array] normalized language id subtags tuple (see init.js).
		 */
		var coreLikelySubtags = function( Cldr, cldr, subtags, options ) {
			var match, matchFound,
				language = subtags[ 0 ],
				script = subtags[ 1 ],
				sep = Cldr.localeSep,
				territory = subtags[ 2 ];
			options = options || {};

			// Skip if (language, script, territory) is not empty [3.3]
			if ( language !== "und" && script !== "Zzzz" && territory !== "ZZ" ) {
				return [ language, script, territory ];
			}

			// Skip if no supplemental likelySubtags data is present
			if ( typeof cldr.get( "supplemental/likelySubtags" ) === "undefined" ) {
				return;
			}

			// [2]
			matchFound = arraySome([
				[ language, script, territory ],
				[ language, territory ],
				[ language, script ],
				[ language ],
				[ "und", script ]
			], function( test ) {
				return match = !(/\b(Zzzz|ZZ)\b/).test( test.join( sep ) ) /* [1.4] */ && cldr.get( [ "supplemental/likelySubtags", test.join( sep ) ] );
			});

			// [3]
			if ( matchFound ) {
				// [3.2 .. 3.4]
				match = match.split( sep );
				return [
					language !== "und" ? language : match[ 0 ],
					script !== "Zzzz" ? script : match[ 1 ],
					territory !== "ZZ" ? territory : match[ 2 ]
				];
			} else if ( options.force ) {
				// [3.1.2]
				return cldr.get( "supplemental/likelySubtags/und" ).split( sep );
			} else {
				// [3.1.1]
				return;
			}
		};



		/**
		 * Given a locale, remove any fields that Add Likely Subtags would add.
		 * http://www.unicode.org/reports/tr35/#Likely_Subtags
		 * 1. First get max = AddLikelySubtags(inputLocale). If an error is signaled,
		 * return it.
		 * 2. Remove the variants from max.
		 * 3. Then for trial in {language, language _ region, language _ script}. If
		 * AddLikelySubtags(trial) = max, then return trial + variants.
		 * 4. If you do not get a match, return max + variants.
		 * 
		 * @maxLanguageId [Array] maxLanguageId tuple (see init.js).
		 */
		var coreRemoveLikelySubtags = function( Cldr, cldr, maxLanguageId ) {
			var match, matchFound,
				language = maxLanguageId[ 0 ],
				script = maxLanguageId[ 1 ],
				territory = maxLanguageId[ 2 ];

			// [3]
			matchFound = arraySome([
				[ [ language, "Zzzz", "ZZ" ], [ language ] ],
				[ [ language, "Zzzz", territory ], [ language, territory ] ],
				[ [ language, script, "ZZ" ], [ language, script ] ]
			], function( test ) {
				var result = coreLikelySubtags( Cldr, cldr, test[ 0 ] );
				match = test[ 1 ];
				return result && result[ 0 ] === maxLanguageId[ 0 ] &&
					result[ 1 ] === maxLanguageId[ 1 ] &&
					result[ 2 ] === maxLanguageId[ 2 ];
			});

			// [4]
			return matchFound ?  match : maxLanguageId;
		};




		/**
		 * subtags( locale )
		 *
		 * @locale [String]
		 */
		var coreSubtags = function( locale ) {
			var aux, unicodeLanguageId,
				subtags = [];

			locale = locale.replace( /_/, "-" );

			// Unicode locale extensions.
			aux = locale.split( "-u-" );
			if ( aux[ 1 ] ) {
				aux[ 1 ] = aux[ 1 ].split( "-t-" );
				locale = aux[ 0 ] + ( aux[ 1 ][ 1 ] ? "-t-" + aux[ 1 ][ 1 ] : "");
				subtags[ 4 /* unicodeLocaleExtensions */ ] = aux[ 1 ][ 0 ];
			}

			// TODO normalize transformed extensions. Currently, skipped.
			// subtags[ x ] = locale.split( "-t-" )[ 1 ];
			unicodeLanguageId = locale.split( "-t-" )[ 0 ];

			// unicode_language_id = "root"
			//   | unicode_language_subtag         
			//     (sep unicode_script_subtag)? 
			//     (sep unicode_region_subtag)?
			//     (sep unicode_variant_subtag)* ;
			//
			// Although unicode_language_subtag = alpha{2,8}, I'm using alpha{2,3}. Because, there's no language on CLDR lengthier than 3.
			aux = unicodeLanguageId.match( /^(([a-z]{2,3})(-([A-Z][a-z]{3}))?(-([A-Z]{2}|[0-9]{3}))?)(-[a-zA-Z0-9]{5,8}|[0-9][a-zA-Z0-9]{3})*$|^(root)$/ );
			if ( aux === null ) {
				return [ "und", "Zzzz", "ZZ" ];
			}
			subtags[ 0 /* language */ ] = aux[ 9 ] /* root */ || aux[ 2 ] || "und";
			subtags[ 1 /* script */ ] = aux[ 4 ] || "Zzzz";
			subtags[ 2 /* territory */ ] = aux[ 6 ] || "ZZ";
			subtags[ 3 /* variant */ ] = aux[ 7 ];

			// 0: language
			// 1: script
			// 2: territory (aka region)
			// 3: variant
			// 4: unicodeLocaleExtensions
			return subtags;
		};




		var arrayForEach = function( array, callback ) {
			var i, length;
			if ( array.forEach ) {
				return array.forEach( callback );
			}
			for ( i = 0, length = array.length; i < length; i++ ) {
				callback( array[ i ], i, array );
			}
		};




		/**
		 * bundleLookup( minLanguageId )
		 *
		 * @Cldr [Cldr class]
		 *
		 * @cldr [Cldr instance]
		 *
		 * @minLanguageId [String] requested languageId after applied remove likely subtags.
		 */
		var bundleLookup = function( Cldr, cldr, minLanguageId ) {
			var availableBundleMap = Cldr._availableBundleMap,
				availableBundleMapQueue = Cldr._availableBundleMapQueue;

			if ( availableBundleMapQueue.length ) {
				arrayForEach( availableBundleMapQueue, function( bundle ) {
					var existing, maxBundle, minBundle, subtags;
					subtags = coreSubtags( bundle );
					maxBundle = coreLikelySubtags( Cldr, cldr, subtags, { force: true } ) || subtags;
					minBundle = coreRemoveLikelySubtags( Cldr, cldr, maxBundle );
					minBundle = minBundle.join( Cldr.localeSep );
					existing = availableBundleMapQueue[ minBundle ];
					if ( existing && existing.length < bundle.length ) {
						return;
					}
					availableBundleMap[ minBundle ] = bundle;
				});
				Cldr._availableBundleMapQueue = [];
			}

			return availableBundleMap[ minLanguageId ] || null;
		};




		var objectKeys = function( object ) {
			var i,
				result = [];

			if ( Object.keys ) {
				return Object.keys( object );
			}

			for ( i in object ) {
				result.push( i );
			}

			return result;
		};




		var createError = function( code, attributes ) {
			var error, message;

			message = code + ( attributes && JSON ? ": " + JSON.stringify( attributes ) : "" );
			error = new Error( message );
			error.code = code;

			// extend( error, attributes );
			arrayForEach( objectKeys( attributes ), function( attribute ) {
				error[ attribute ] = attributes[ attribute ];
			});

			return error;
		};




		var validate = function( code, check, attributes ) {
			if ( !check ) {
				throw createError( code, attributes );
			}
		};




		var validatePresence = function( value, name ) {
			validate( "E_MISSING_PARAMETER", typeof value !== "undefined", {
				name: name
			});
		};




		var validateType = function( value, name, check, expected ) {
			validate( "E_INVALID_PAR_TYPE", check, {
				expected: expected,
				name: name,
				value: value
			});
		};




		var validateTypePath = function( value, name ) {
			validateType( value, name, typeof value === "string" || arrayIsArray( value ), "String or Array" );
		};




		/**
		 * Function inspired by jQuery Core, but reduced to our use case.
		 */
		var isPlainObject = function( obj ) {
			return obj !== null && "" + obj === "[object Object]";
		};




		var validateTypePlainObject = function( value, name ) {
			validateType( value, name, typeof value === "undefined" || isPlainObject( value ), "Plain Object" );
		};




		var validateTypeString = function( value, name ) {
			validateType( value, name, typeof value === "string", "a string" );
		};




		// @path: normalized path
		var resourceGet = function( data, path ) {
			var i,
				node = data,
				length = path.length;

			for ( i = 0; i < length - 1; i++ ) {
				node = node[ path[ i ] ];
				if ( !node ) {
					return undefined;
				}
			}
			return node[ path[ i ] ];
		};




		/**
		 * setAvailableBundles( Cldr, json )
		 *
		 * @Cldr [Cldr class]
		 *
		 * @json resolved/unresolved cldr data.
		 *
		 * Set available bundles queue based on passed json CLDR data. Considers a bundle as any String at /main/{bundle}.
		 */
		var coreSetAvailableBundles = function( Cldr, json ) {
			var bundle,
				availableBundleMapQueue = Cldr._availableBundleMapQueue,
				main = resourceGet( json, [ "main" ] );

			if ( main ) {
				for ( bundle in main ) {
					if ( main.hasOwnProperty( bundle ) && bundle !== "root" ) {
						availableBundleMapQueue.push( bundle );
					}
				}
			}
		};



		var alwaysArray = function( somethingOrArray ) {
			return arrayIsArray( somethingOrArray ) ?  somethingOrArray : [ somethingOrArray ];
		};


		var jsonMerge = (function() {

		// Returns new deeply merged JSON.
		//
		// Eg.
		// merge( { a: { b: 1, c: 2 } }, { a: { b: 3, d: 4 } } )
		// -> { a: { b: 3, c: 2, d: 4 } }
		//
		// @arguments JSON's
		// 
		var merge = function() {
			var destination = {},
				sources = [].slice.call( arguments, 0 );
			arrayForEach( sources, function( source ) {
				var prop;
				for ( prop in source ) {
					if ( prop in destination && arrayIsArray( destination[ prop ] ) ) {

						// Concat Arrays
						destination[ prop ] = destination[ prop ].concat( source[ prop ] );

					} else if ( prop in destination && typeof destination[ prop ] === "object" ) {

						// Merge Objects
						destination[ prop ] = merge( destination[ prop ], source[ prop ] );

					} else {

						// Set new values
						destination[ prop ] = source[ prop ];

					}
				}
			});
			return destination;
		};

		return merge;

	}());


		/**
		 * load( Cldr, source, jsons )
		 *
		 * @Cldr [Cldr class]
		 *
		 * @source [Object]
		 *
		 * @jsons [arguments]
		 */
		var coreLoad = function( Cldr, source, jsons ) {
			var i, j, json;

			validatePresence( jsons[ 0 ], "json" );

			// Support arbitrary parameters, e.g., `Cldr.load({...}, {...})`.
			for ( i = 0; i < jsons.length; i++ ) {

				// Support array parameters, e.g., `Cldr.load([{...}, {...}])`.
				json = alwaysArray( jsons[ i ] );

				for ( j = 0; j < json.length; j++ ) {
					validateTypePlainObject( json[ j ], "json" );
					source = jsonMerge( source, json[ j ] );
					coreSetAvailableBundles( Cldr, json[ j ] );
				}
			}

			return source;
		};



		var itemGetResolved = function( Cldr, path, attributes ) {
			// Resolve path
			var normalizedPath = pathNormalize( path, attributes );

			return resourceGet( Cldr._resolved, normalizedPath );
		};




		/**
		 * new Cldr()
		 */
		var Cldr = function( locale ) {
			this.init( locale );
		};

		// Build optimization hack to avoid duplicating functions across modules.
		Cldr._alwaysArray = alwaysArray;
		Cldr._coreLoad = coreLoad;
		Cldr._createError = createError;
		Cldr._itemGetResolved = itemGetResolved;
		Cldr._jsonMerge = jsonMerge;
		Cldr._pathNormalize = pathNormalize;
		Cldr._resourceGet = resourceGet;
		Cldr._validatePresence = validatePresence;
		Cldr._validateType = validateType;
		Cldr._validateTypePath = validateTypePath;
		Cldr._validateTypePlainObject = validateTypePlainObject;

		Cldr._availableBundleMap = {};
		Cldr._availableBundleMapQueue = [];
		Cldr._resolved = {};

		// Allow user to override locale separator "-" (default) | "_". According to http://www.unicode.org/reports/tr35/#Unicode_language_identifier, both "-" and "_" are valid locale separators (eg. "en_GB", "en-GB"). According to http://unicode.org/cldr/trac/ticket/6786 its usage must be consistent throughout the data set.
		Cldr.localeSep = "-";

		/**
		 * Cldr.load( json [, json, ...] )
		 *
		 * @json [JSON] CLDR data or [Array] Array of @json's.
		 *
		 * Load resolved cldr data.
		 */
		Cldr.load = function() {
			Cldr._resolved = coreLoad( Cldr, Cldr._resolved, arguments );
		};

		/**
		 * .init() automatically run on instantiation/construction.
		 */
		Cldr.prototype.init = function( locale ) {
			var attributes, language, maxLanguageId, minLanguageId, script, subtags, territory, unicodeLocaleExtensions, variant,
				sep = Cldr.localeSep;

			validatePresence( locale, "locale" );
			validateTypeString( locale, "locale" );

			subtags = coreSubtags( locale );

			unicodeLocaleExtensions = subtags[ 4 ];
			variant = subtags[ 3 ];

			// Normalize locale code.
			// Get (or deduce) the "triple subtags": language, territory (also aliased as region), and script subtags.
			// Get the variant subtags (calendar, collation, currency, etc).
			// refs:
			// - http://www.unicode.org/reports/tr35/#Field_Definitions
			// - http://www.unicode.org/reports/tr35/#Language_and_Locale_IDs
			// - http://www.unicode.org/reports/tr35/#Unicode_locale_identifier

			// When a locale id does not specify a language, or territory (region), or script, they are obtained by Likely Subtags.
			maxLanguageId = coreLikelySubtags( Cldr, this, subtags, { force: true } ) || subtags;
			language = maxLanguageId[ 0 ];
			script = maxLanguageId[ 1 ];
			territory = maxLanguageId[ 2 ];

			minLanguageId = coreRemoveLikelySubtags( Cldr, this, maxLanguageId ).join( sep );

			// Set attributes
			this.attributes = attributes = {
				bundle: bundleLookup( Cldr, this, minLanguageId ),

				// Unicode Language Id
				minlanguageId: minLanguageId,
				maxLanguageId: maxLanguageId.join( sep ),

				// Unicode Language Id Subtabs
				language: language,
				script: script,
				territory: territory,
				region: territory, /* alias */
				variant: variant
			};

			// Unicode locale extensions.
			unicodeLocaleExtensions && ( "-" + unicodeLocaleExtensions ).replace( /-[a-z]{3,8}|(-[a-z]{2})-([a-z]{3,8})/g, function( attribute, key, type ) {

				if ( key ) {

					// Extension is in the `keyword` form.
					attributes[ "u" + key ] = type;
				} else {

					// Extension is in the `attribute` form.
					attributes[ "u" + attribute ] = true;
				}
			});

			this.locale = locale;
		};

		/**
		 * .get()
		 */
		Cldr.prototype.get = function( path ) {

			validatePresence( path, "path" );
			validateTypePath( path, "path" );

			return itemGetResolved( Cldr, path, this.attributes );
		};

		/**
		 * .main()
		 */
		Cldr.prototype.main = function( path ) {
			validatePresence( path, "path" );
			validateTypePath( path, "path" );

			validate( "E_MISSING_BUNDLE", this.attributes.bundle !== null, {
				locale: this.locale
			});

			path = alwaysArray( path );
			return this.get( [ "main/{bundle}" ].concat( path ) );
		};

		return Cldr;




	}));


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
	 * CLDR JavaScript Library v0.4.1
	 * http://jquery.com/
	 *
	 * Copyright 2013 Rafael Xavier de Souza
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-02-25T13:51Z
	 */
	/*!
	 * CLDR JavaScript Library v0.4.1 2015-02-25T13:51Z MIT license © Rafael Xavier
	 * http://git.io/h4lmVg
	 */
	(function( factory ) {

		if ( true ) {
			// AMD.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(4) ], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if ( typeof module === "object" && typeof module.exports === "object" ) {
			// Node. CommonJS.
			module.exports = factory( require( "cldrjs" ) );
		} else {
			// Global
			factory( Cldr );
		}

	}(function( Cldr ) {

		// Build optimization hack to avoid duplicating functions across modules.
		var pathNormalize = Cldr._pathNormalize,
			validatePresence = Cldr._validatePresence,
			validateType = Cldr._validateType;

	/*!
	 * EventEmitter v4.2.7 - git.io/ee
	 * Oliver Caldwell
	 * MIT license
	 * @preserve
	 */

	var EventEmitter;
	/* jshint ignore:start */
	EventEmitter = (function () {
		

		/**
		 * Class for managing events.
		 * Can be extended to provide event functionality in other classes.
		 *
		 * @class EventEmitter Manages event registering and emitting.
		 */
		function EventEmitter() {}

		// Shortcuts to improve speed and size
		var proto = EventEmitter.prototype;
		var exports = this;
		var originalGlobalValue = exports.EventEmitter;

		/**
		 * Finds the index of the listener for the event in it's storage array.
		 *
		 * @param {Function[]} listeners Array of listeners to search through.
		 * @param {Function} listener Method to look for.
		 * @return {Number} Index of the specified listener, -1 if not found
		 * @api private
		 */
		function indexOfListener(listeners, listener) {
			var i = listeners.length;
			while (i--) {
				if (listeners[i].listener === listener) {
					return i;
				}
			}

			return -1;
		}

		/**
		 * Alias a method while keeping the context correct, to allow for overwriting of target method.
		 *
		 * @param {String} name The name of the target method.
		 * @return {Function} The aliased method
		 * @api private
		 */
		function alias(name) {
			return function aliasClosure() {
				return this[name].apply(this, arguments);
			};
		}

		/**
		 * Returns the listener array for the specified event.
		 * Will initialise the event object and listener arrays if required.
		 * Will return an object if you use a regex search. The object contains keys for each matched event. So /ba[rz]/ might return an object containing bar and baz. But only if you have either defined them with defineEvent or added some listeners to them.
		 * Each property in the object response is an array of listener functions.
		 *
		 * @param {String|RegExp} evt Name of the event to return the listeners from.
		 * @return {Function[]|Object} All listener functions for the event.
		 */
		proto.getListeners = function getListeners(evt) {
			var events = this._getEvents();
			var response;
			var key;

			// Return a concatenated array of all matching events if
			// the selector is a regular expression.
			if (evt instanceof RegExp) {
				response = {};
				for (key in events) {
					if (events.hasOwnProperty(key) && evt.test(key)) {
						response[key] = events[key];
					}
				}
			}
			else {
				response = events[evt] || (events[evt] = []);
			}

			return response;
		};

		/**
		 * Takes a list of listener objects and flattens it into a list of listener functions.
		 *
		 * @param {Object[]} listeners Raw listener objects.
		 * @return {Function[]} Just the listener functions.
		 */
		proto.flattenListeners = function flattenListeners(listeners) {
			var flatListeners = [];
			var i;

			for (i = 0; i < listeners.length; i += 1) {
				flatListeners.push(listeners[i].listener);
			}

			return flatListeners;
		};

		/**
		 * Fetches the requested listeners via getListeners but will always return the results inside an object. This is mainly for internal use but others may find it useful.
		 *
		 * @param {String|RegExp} evt Name of the event to return the listeners from.
		 * @return {Object} All listener functions for an event in an object.
		 */
		proto.getListenersAsObject = function getListenersAsObject(evt) {
			var listeners = this.getListeners(evt);
			var response;

			if (listeners instanceof Array) {
				response = {};
				response[evt] = listeners;
			}

			return response || listeners;
		};

		/**
		 * Adds a listener function to the specified event.
		 * The listener will not be added if it is a duplicate.
		 * If the listener returns true then it will be removed after it is called.
		 * If you pass a regular expression as the event name then the listener will be added to all events that match it.
		 *
		 * @param {String|RegExp} evt Name of the event to attach the listener to.
		 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.addListener = function addListener(evt, listener) {
			var listeners = this.getListenersAsObject(evt);
			var listenerIsWrapped = typeof listener === 'object';
			var key;

			for (key in listeners) {
				if (listeners.hasOwnProperty(key) && indexOfListener(listeners[key], listener) === -1) {
					listeners[key].push(listenerIsWrapped ? listener : {
						listener: listener,
						once: false
					});
				}
			}

			return this;
		};

		/**
		 * Alias of addListener
		 */
		proto.on = alias('addListener');

		/**
		 * Semi-alias of addListener. It will add a listener that will be
		 * automatically removed after it's first execution.
		 *
		 * @param {String|RegExp} evt Name of the event to attach the listener to.
		 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.addOnceListener = function addOnceListener(evt, listener) {
			return this.addListener(evt, {
				listener: listener,
				once: true
			});
		};

		/**
		 * Alias of addOnceListener.
		 */
		proto.once = alias('addOnceListener');

		/**
		 * Defines an event name. This is required if you want to use a regex to add a listener to multiple events at once. If you don't do this then how do you expect it to know what event to add to? Should it just add to every possible match for a regex? No. That is scary and bad.
		 * You need to tell it what event names should be matched by a regex.
		 *
		 * @param {String} evt Name of the event to create.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.defineEvent = function defineEvent(evt) {
			this.getListeners(evt);
			return this;
		};

		/**
		 * Uses defineEvent to define multiple events.
		 *
		 * @param {String[]} evts An array of event names to define.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.defineEvents = function defineEvents(evts) {
			for (var i = 0; i < evts.length; i += 1) {
				this.defineEvent(evts[i]);
			}
			return this;
		};

		/**
		 * Removes a listener function from the specified event.
		 * When passed a regular expression as the event name, it will remove the listener from all events that match it.
		 *
		 * @param {String|RegExp} evt Name of the event to remove the listener from.
		 * @param {Function} listener Method to remove from the event.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.removeListener = function removeListener(evt, listener) {
			var listeners = this.getListenersAsObject(evt);
			var index;
			var key;

			for (key in listeners) {
				if (listeners.hasOwnProperty(key)) {
					index = indexOfListener(listeners[key], listener);

					if (index !== -1) {
						listeners[key].splice(index, 1);
					}
				}
			}

			return this;
		};

		/**
		 * Alias of removeListener
		 */
		proto.off = alias('removeListener');

		/**
		 * Adds listeners in bulk using the manipulateListeners method.
		 * If you pass an object as the second argument you can add to multiple events at once. The object should contain key value pairs of events and listeners or listener arrays. You can also pass it an event name and an array of listeners to be added.
		 * You can also pass it a regular expression to add the array of listeners to all events that match it.
		 * Yeah, this function does quite a bit. That's probably a bad thing.
		 *
		 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add to multiple events at once.
		 * @param {Function[]} [listeners] An optional array of listener functions to add.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.addListeners = function addListeners(evt, listeners) {
			// Pass through to manipulateListeners
			return this.manipulateListeners(false, evt, listeners);
		};

		/**
		 * Removes listeners in bulk using the manipulateListeners method.
		 * If you pass an object as the second argument you can remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
		 * You can also pass it an event name and an array of listeners to be removed.
		 * You can also pass it a regular expression to remove the listeners from all events that match it.
		 *
		 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to remove from multiple events at once.
		 * @param {Function[]} [listeners] An optional array of listener functions to remove.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.removeListeners = function removeListeners(evt, listeners) {
			// Pass through to manipulateListeners
			return this.manipulateListeners(true, evt, listeners);
		};

		/**
		 * Edits listeners in bulk. The addListeners and removeListeners methods both use this to do their job. You should really use those instead, this is a little lower level.
		 * The first argument will determine if the listeners are removed (true) or added (false).
		 * If you pass an object as the second argument you can add/remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
		 * You can also pass it an event name and an array of listeners to be added/removed.
		 * You can also pass it a regular expression to manipulate the listeners of all events that match it.
		 *
		 * @param {Boolean} remove True if you want to remove listeners, false if you want to add.
		 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add/remove from multiple events at once.
		 * @param {Function[]} [listeners] An optional array of listener functions to add/remove.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.manipulateListeners = function manipulateListeners(remove, evt, listeners) {
			var i;
			var value;
			var single = remove ? this.removeListener : this.addListener;
			var multiple = remove ? this.removeListeners : this.addListeners;

			// If evt is an object then pass each of it's properties to this method
			if (typeof evt === 'object' && !(evt instanceof RegExp)) {
				for (i in evt) {
					if (evt.hasOwnProperty(i) && (value = evt[i])) {
						// Pass the single listener straight through to the singular method
						if (typeof value === 'function') {
							single.call(this, i, value);
						}
						else {
							// Otherwise pass back to the multiple function
							multiple.call(this, i, value);
						}
					}
				}
			}
			else {
				// So evt must be a string
				// And listeners must be an array of listeners
				// Loop over it and pass each one to the multiple method
				i = listeners.length;
				while (i--) {
					single.call(this, evt, listeners[i]);
				}
			}

			return this;
		};

		/**
		 * Removes all listeners from a specified event.
		 * If you do not specify an event then all listeners will be removed.
		 * That means every event will be emptied.
		 * You can also pass a regex to remove all events that match it.
		 *
		 * @param {String|RegExp} [evt] Optional name of the event to remove all listeners for. Will remove from every event if not passed.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.removeEvent = function removeEvent(evt) {
			var type = typeof evt;
			var events = this._getEvents();
			var key;

			// Remove different things depending on the state of evt
			if (type === 'string') {
				// Remove all listeners for the specified event
				delete events[evt];
			}
			else if (evt instanceof RegExp) {
				// Remove all events matching the regex.
				for (key in events) {
					if (events.hasOwnProperty(key) && evt.test(key)) {
						delete events[key];
					}
				}
			}
			else {
				// Remove all listeners in all events
				delete this._events;
			}

			return this;
		};

		/**
		 * Alias of removeEvent.
		 *
		 * Added to mirror the node API.
		 */
		proto.removeAllListeners = alias('removeEvent');

		/**
		 * Emits an event of your choice.
		 * When emitted, every listener attached to that event will be executed.
		 * If you pass the optional argument array then those arguments will be passed to every listener upon execution.
		 * Because it uses `apply`, your array of arguments will be passed as if you wrote them out separately.
		 * So they will not arrive within the array on the other side, they will be separate.
		 * You can also pass a regular expression to emit to all events that match it.
		 *
		 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
		 * @param {Array} [args] Optional array of arguments to be passed to each listener.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.emitEvent = function emitEvent(evt, args) {
			var listeners = this.getListenersAsObject(evt);
			var listener;
			var i;
			var key;
			var response;

			for (key in listeners) {
				if (listeners.hasOwnProperty(key)) {
					i = listeners[key].length;

					while (i--) {
						// If the listener returns true then it shall be removed from the event
						// The function is executed either with a basic call or an apply if there is an args array
						listener = listeners[key][i];

						if (listener.once === true) {
							this.removeListener(evt, listener.listener);
						}

						response = listener.listener.apply(this, args || []);

						if (response === this._getOnceReturnValue()) {
							this.removeListener(evt, listener.listener);
						}
					}
				}
			}

			return this;
		};

		/**
		 * Alias of emitEvent
		 */
		proto.trigger = alias('emitEvent');

		/**
		 * Subtly different from emitEvent in that it will pass its arguments on to the listeners, as opposed to taking a single array of arguments to pass on.
		 * As with emitEvent, you can pass a regex in place of the event name to emit to all events that match it.
		 *
		 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
		 * @param {...*} Optional additional arguments to be passed to each listener.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.emit = function emit(evt) {
			var args = Array.prototype.slice.call(arguments, 1);
			return this.emitEvent(evt, args);
		};

		/**
		 * Sets the current value to check against when executing listeners. If a
		 * listeners return value matches the one set here then it will be removed
		 * after execution. This value defaults to true.
		 *
		 * @param {*} value The new value to check for when executing listeners.
		 * @return {Object} Current instance of EventEmitter for chaining.
		 */
		proto.setOnceReturnValue = function setOnceReturnValue(value) {
			this._onceReturnValue = value;
			return this;
		};

		/**
		 * Fetches the current value to check against when executing listeners. If
		 * the listeners return value matches this one then it should be removed
		 * automatically. It will return true by default.
		 *
		 * @return {*|Boolean} The current value to check for or the default, true.
		 * @api private
		 */
		proto._getOnceReturnValue = function _getOnceReturnValue() {
			if (this.hasOwnProperty('_onceReturnValue')) {
				return this._onceReturnValue;
			}
			else {
				return true;
			}
		};

		/**
		 * Fetches the events object and creates one if required.
		 *
		 * @return {Object} The events storage object.
		 * @api private
		 */
		proto._getEvents = function _getEvents() {
			return this._events || (this._events = {});
		};

		/**
		 * Reverts the global {@link EventEmitter} to its previous value and returns a reference to this version.
		 *
		 * @return {Function} Non conflicting EventEmitter class.
		 */
		EventEmitter.noConflict = function noConflict() {
			exports.EventEmitter = originalGlobalValue;
			return EventEmitter;
		};

		return EventEmitter;
	}());
	/* jshint ignore:end */



		var validateTypeFunction = function( value, name ) {
			validateType( value, name, typeof value === "undefined" || typeof value === "function", "Function" );
		};




		var superGet, superInit,
			globalEe = new EventEmitter();

		function validateTypeEvent( value, name ) {
			validateType( value, name, typeof value === "string" || value instanceof RegExp, "String or RegExp" );
		}

		function validateThenCall( method, self ) {
			return function( event, listener ) {
				validatePresence( event, "event" );
				validateTypeEvent( event, "event" );

				validatePresence( listener, "listener" );
				validateTypeFunction( listener, "listener" );

				return self[ method ].apply( self, arguments );
			};
		}

		function off( self ) {
			return validateThenCall( "off", self );
		}

		function on( self ) {
			return validateThenCall( "on", self );
		}

		function once( self ) {
			return validateThenCall( "once", self );
		}

		Cldr.off = off( globalEe );
		Cldr.on = on( globalEe );
		Cldr.once = once( globalEe );

		/**
		 * Overload Cldr.prototype.init().
		 */
		superInit = Cldr.prototype.init;
		Cldr.prototype.init = function() {
			var ee;
			this.ee = ee = new EventEmitter();
			this.off = off( ee );
			this.on = on( ee );
			this.once = once( ee );
			superInit.apply( this, arguments );
		};

		/**
		 * getOverload is encapsulated, because of cldr/unresolved. If it's loaded
		 * after cldr/event (and note it overwrites .get), it can trigger this
		 * overload again.
		 */
		function getOverload() {

			/**
			 * Overload Cldr.prototype.get().
			 */
			superGet = Cldr.prototype.get;
			Cldr.prototype.get = function( path ) {
				var value = superGet.apply( this, arguments );
				path = pathNormalize( path, this.attributes ).join( "/" );
				globalEe.trigger( "get", [ path, value ] );
				this.ee.trigger( "get", [ path, value ] );
				return value;
			};
		}

		Cldr._eventInit = getOverload;
		getOverload();

		return Cldr;




	}));


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
	 * CLDR JavaScript Library v0.4.1
	 * http://jquery.com/
	 *
	 * Copyright 2013 Rafael Xavier de Souza
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-02-25T13:51Z
	 */
	/*!
	 * CLDR JavaScript Library v0.4.1 2015-02-25T13:51Z MIT license © Rafael Xavier
	 * http://git.io/h4lmVg
	 */
	(function( factory ) {

		if ( true ) {
			// AMD.
			!(__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(4) ], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		} else if ( typeof module === "object" && typeof module.exports === "object" ) {
			// Node. CommonJS.
			module.exports = factory( require( "cldrjs" ) );
		} else {
			// Global
			factory( Cldr );
		}

	}(function( Cldr ) {

		// Build optimization hack to avoid duplicating functions across modules.
		var alwaysArray = Cldr._alwaysArray;



		var supplementalMain = function( cldr ) {

			var prepend, supplemental;
			
			prepend = function( prepend ) {
				return function( path ) {
					path = alwaysArray( path );
					return cldr.get( [ prepend ].concat( path ) );
				};
			};

			supplemental = prepend( "supplemental" );

			// Week Data
			// http://www.unicode.org/reports/tr35/tr35-dates.html#Week_Data
			supplemental.weekData = prepend( "supplemental/weekData" );

			supplemental.weekData.firstDay = function() {
				return cldr.get( "supplemental/weekData/firstDay/{territory}" ) ||
					cldr.get( "supplemental/weekData/firstDay/001" );
			};

			supplemental.weekData.minDays = function() {
				var minDays = cldr.get( "supplemental/weekData/minDays/{territory}" ) ||
					cldr.get( "supplemental/weekData/minDays/001" );
				return parseInt( minDays, 10 );
			};

			// Time Data
			// http://www.unicode.org/reports/tr35/tr35-dates.html#Time_Data
			supplemental.timeData = prepend( "supplemental/timeData" );

			supplemental.timeData.allowed = function() {
				return cldr.get( "supplemental/timeData/{territory}/_allowed" ) ||
					cldr.get( "supplemental/timeData/001/_allowed" );
			};

			supplemental.timeData.preferred = function() {
				return cldr.get( "supplemental/timeData/{territory}/_preferred" ) ||
					cldr.get( "supplemental/timeData/001/_preferred" );
			};

			return supplemental;

		};




		var initSuper = Cldr.prototype.init;

		/**
		 * .init() automatically ran on construction.
		 *
		 * Overload .init().
		 */
		Cldr.prototype.init = function() {
			initSuper.apply( this, arguments );
			this.supplemental = supplementalMain( this );
		};

		return Cldr;




	}));


/***/ }
/******/ ]);