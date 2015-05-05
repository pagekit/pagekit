var Settings =
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

	var $ = __webpack_require__(1);
	var _ = __webpack_require__(2);
	var UIkit = __webpack_require__(3);

	var Settings = Vue.extend({

	    sections: [],

	    data: function () {
	        return window.$settings;
	    },

	    ready: function() {

	        UIkit.tab(this.$$.tab, { connect: this.$$.content})

	    },

	    methods: {

	        save: function(e) {

	            e.preventDefault();

	            var self = this;

	            this.$broadcast('save', this.$data);

	            this.$resource('admin/system/settings/save').save({ config: this.config, options: this.options }, function() {

	                UIkit.notify(self.$trans('Settings saved.'));

	            }, function (data) {

	                UIkit.notify(data, 'danger');
	            });

	        }

	    }

	});

	Settings.register = function (options) {
	    this.component(options.name, options);
	    this.options.sections.push(options);
	};

	Settings.register(__webpack_require__(4));
	Settings.register(__webpack_require__(5));
	Settings.register(__webpack_require__(6));

	$(function () {

	    var settings = new Settings();
	    settings.$mount('#settings');

	});

	module.exports = Settings;


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = _;

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = UIkit;

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-margin uk-flex uk-flex-space-between uk-flex-wrap\" data-uk-margin=\"\">\n        <div data-uk-margin=\"\">\n\n            <h2 class=\"uk-margin-remove\">{{ 'Site' | trans }}</h2>\n\n        </div>\n        <div data-uk-margin=\"\">\n\n            <button class=\"uk-button uk-button-primary\" type=\"submit\">{{ 'Save' | trans }}</button>\n\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-title\" class=\"uk-form-label\">{{ 'Title' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <input id=\"form-title\" class=\"uk-form-width-large\" type=\"text\" v-model=\"option.site.title\">\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-description\" class=\"uk-form-label\">{{ 'Description' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <textarea id=\"form-description\" class=\"uk-form-width-large\" rows=\"5\" v-model=\"option.site.description\"></textarea>\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <span class=\"uk-form-label\">{{ 'Maintenance' | trans }}</span>\n        <div class=\"uk-form-controls uk-form-controls-text\">\n            <p class=\"uk-form-controls-condensed\">\n                <label><input type=\"checkbox\" value=\"1\" v-model=\"option.maintenance.enabled\"> {{ 'Put the site offline and show the offline message.' | trans }}</label>\n            </p>\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-offlinemessage\" class=\"uk-form-label\">{{ 'Offline Message' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <textarea id=\"form-offlinemessage\" class=\"uk-form-width-large\" placeholder=\"{{ &quot;We'll be back soon.&quot; | trans }}\" rows=\"5\" v-model=\"option.maintenance.msg\"></textarea>\n        </div>\n    </div>";
	module.exports = {

	        name: 'settings-site',
	        label: 'Site',
	        priority: 0,

	        computed: {

	            option: function() {
	                return this.$root.$get('options.system');
	            }

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-margin uk-flex uk-flex-space-between uk-flex-wrap\" data-uk-margin=\"\">\n        <div data-uk-margin=\"\">\n\n            <h2 class=\"uk-margin-remove\">{{ 'System' | trans }}</h2>\n\n        </div>\n        <div data-uk-margin=\"\">\n\n            <button class=\"uk-button uk-button-primary\" type=\"submit\">{{ 'Save' | trans }}</button>\n\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-apikey\" class=\"uk-form-label\">{{ 'API Key' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <textarea id=\"form-apikey\" class=\"uk-form-width-large\" placeholder=\"{{ 'Enter your API key' | trans }}\" rows=\"6\" v-model=\"$root.options.system.api.key\"></textarea>\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-channel\" class=\"uk-form-label\">{{ 'Release Channel' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select id=\"form-channel\" class=\"uk-form-width-large\" v-model=\"$root.options.system.release_channel\">\n                <option value=\"stable\">{{ 'Stable' | trans }}</option>\n                <option value=\"nightly\">{{ 'Nightly' | trans }}</option>\n            </select>\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-uploadfolder\" class=\"uk-form-label\">{{ 'Storage' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <input id=\"form-uploadfolder\" class=\"uk-form-width-large\" type=\"text\" placeholder=\"/storage\" v-model=\"$root.config.system.storage\">\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <span class=\"uk-form-label\">{{ 'Developer' | trans }}</span>\n        <div class=\"uk-form-controls uk-form-controls-text\">\n            <p class=\"uk-form-controls-condensed\">\n                <label><input type=\"checkbox\" value=\"1\" v-model=\"$root.config.application.debug\"> {{ 'Enable debug mode' | trans }}</label>\n            </p>\n            <p class=\"uk-form-controls-condensed\">\n                <label><input type=\"checkbox\" value=\"1\" v-model=\"$root.config.debug.enabled\" v-attr=\"disabled: !sqlite\"> {{ 'Enable debug toolbar' | trans }}</label>\n            </p>\n            <p class=\"uk-form-help-block\" v-if=\"!sqlite\">{{ 'Please enable the SQLite database extension.' | trans }}</p>\n        </div>\n    </div>";
	module.exports = {

	        name: 'settings-system',
	        label: 'System',
	        priority: 10,

	        data: function() {
	            return window.$system;
	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-margin uk-flex uk-flex-space-between uk-flex-wrap\" data-uk-margin=\"\">\n        <div data-uk-margin=\"\">\n\n            <h2 class=\"uk-margin-remove\">{{ 'Localization' | trans }}</h2>\n\n        </div>\n        <div data-uk-margin=\"\">\n\n            <button class=\"uk-button uk-button-primary\" type=\"submit\">{{ 'Save' | trans }}</button>\n\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-sitelocale\" class=\"uk-form-label\">{{ 'Site Locale' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select id=\"form-sitelocale\" class=\"uk-form-width-large\" v-model=\"option.site.locale\" options=\"locales | toOptions\"></select>\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-adminlocale\" class=\"uk-form-label\">{{ 'Admin Locale' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select id=\"form-adminlocale\" class=\"uk-form-width-large\" v-model=\"option.admin.locale\" options=\"locales | toOptions\"></select>\n        </div>\n    </div>\n    \n    <div class=\"uk-form-row\">\n        <label for=\"form-timezone\" class=\"uk-form-label\">{{ 'Time Zone' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select id=\"form-timezone\" class=\"uk-form-width-large\" v-model=\"option.timezone\" options=\"timezones | toOptions\"></select>\n        </div>\n    </div>";
	module.exports = {

	        name: 'settings-locale',
	        label: 'Localization',
	        priority: 20,

	        data: function() {
	            return window.$system;
	        },

	        computed: {

	            option: function() {
	                return this.$root.$get('options.system');
	            }

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);