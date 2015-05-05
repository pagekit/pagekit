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
/******/ ({

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-margin uk-flex uk-flex-space-between uk-flex-wrap\" data-uk-margin=\"\">\n        <div data-uk-margin=\"\">\n\n            <h2 class=\"uk-margin-remove\">{{ 'OAuth' | trans }}</h2>\n\n        </div>\n        <div data-uk-margin=\"\">\n\n            <button class=\"uk-button uk-button-primary\" type=\"submit\">{{ 'Save' | trans }}</button>\n\n        </div>\n    </div>\n\n    <div class=\"uk-button-dropdown\" data-uk-dropdown=\"\">\n        <div class=\"uk-button\">{{ 'Add Service' | trans }} <i class=\"uk-icon-caret-down\"></i></div>\n        <div class=\"uk-dropdown uk-dropdown-scrollable\">\n            <ul class=\"uk-nav uk-nav-dropdown\" id=\"oauth-service-dropdown\">\n                <li id=\"{{ $value }}_link\" v-repeat=\"providers | configured\">\n                    <a href=\"#\" v-on=\"click: addProvider($value)\">{{ $value }}</a>\n                </li>\n            </ul>\n        </div>\n    </div>\n\n    <p>{{ $trans('Redirect URL: %url%', {url: redirect_url}) }}</p>\n\n    <div id=\"oauth-service-list\" class=\"uk-form-row\">\n\n        <div id=\"{{ $key }}-container\" v-repeat=\"provider: options.provider\">\n\n            <h2>{{ $key }}</h2>\n            <a class=\"uk-close uk-close-alt uk-float-right\" href=\"#\" v-on=\"click: removeProvider($key)\"></a>\n\n            <div class=\"uk-form-row\">\n                <label for=\"client_id_{{ $key }}\" class=\"uk-form-label\">{{ 'Client ID' | trans }}</label>\n                <div class=\"uk-form-controls\">\n                    <input id=\"client_id_{{ $key }}\" class=\"uk-form-width-large\" type=\"text\" v-model=\"provider.client_id\">\n                </div>\n            </div>\n\n            <div class=\"uk-form-row\">\n                <label for=\"client_secret_{{ $key }}\" class=\"uk-form-label\">{{ 'Client Secret' | trans }}</label>\n                <div class=\"uk-form-controls\">\n                    <input id=\"client_secret_{{ $key }}\" class=\"uk-form-width-large\" type=\"text\" v-model=\"provider.client_secret\">\n                </div>\n            </div>\n\n        </div>\n\n    </div>";
	var Settings = __webpack_require__(15);

	    module.exports = {

	        name: 'system/oauth',
	        label: 'OAuth',
	        priority: 50,

	        data: function() {
	            return window.$oauth
	        },

	        template: __vue_template__,

	        ready: function () {

	            if (Vue.util.isArray(this.options.provider)) {
	                this.options.$delete('provider');
	                this.options.$add('provider', {});
	            }

	            this.providers.sort();
	        },

	        methods: {

	            addProvider: function (provider) {
	                this.options.provider.$add(provider, {'client_id': '', 'client_secret': ''});
	            },

	            removeProvider: function (provider) {
	                this.options.provider.$delete(provider);
	            }

	        },

	        filters: {

	            configured: function (services) {

	                var results = [], self = this;

	                services.forEach(function (service) {
	                    if (!self.options.provider.hasOwnProperty(service)) {
	                        results.push(service);
	                    }
	                });

	                return results;
	            }

	        }

	    };

	    Settings.register(module.exports);
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },

/***/ 15:
/***/ function(module, exports, __webpack_require__) {

	module.exports = Settings;

/***/ }

/******/ });