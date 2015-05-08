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

	var __vue_template__ = "<div class=\"uk-margin uk-flex uk-flex-space-between uk-flex-wrap\" data-uk-margin=\"\">\n        <div data-uk-margin=\"\">\n\n            <h2 class=\"uk-margin-remove\">{{ 'Cache' | trans }}</h2>\n\n        </div>\n        <div data-uk-margin=\"\">\n\n            <button class=\"uk-button uk-button-primary\" type=\"submit\">{{ 'Save' | trans }}</button>\n\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <span class=\"uk-form-label\">{{ 'Cache' | trans }}</span>\n        <div class=\"uk-form-controls uk-form-controls-text\">\n            <p class=\"uk-form-controls-condensed\" v-repeat=\"cache: caches\">\n                <label><input type=\"radio\" value=\"{{ $key }}\" v-model=\"config.caches.cache.storage\" v-attr=\"disabled: !cache.supported\"> {{ cache.name }}</label>\n            </p>\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <span class=\"uk-form-label\">{{ 'Developer' | trans }}</span>\n        <div class=\"uk-form-controls uk-form-controls-text\">\n            <p class=\"uk-form-controls-condensed\">\n                <label><input type=\"checkbox\" value=\"1\" v-model=\"config.nocache\"> {{ 'Disable cache' | trans }}</label>\n            </p>\n            <p>\n                <button class=\"uk-button uk-button-primary\" v-on=\"click: open\">{{ 'Clear Cache' | trans }}</button>\n            </p>\n        </div>\n    </div>\n\n    <div class=\"uk-modal\" v-el=\"modal\">\n        <div class=\"uk-modal-dialog uk-form-stacked\">\n\n            <div class=\"uk-modal-header\">\n                <h2>{{ 'Select Cache to Clear' | trans }}</h2>\n            </div>\n\n            <div class=\"uk-form-row\">\n                <p class=\"uk-form-controls-condensed\">\n                    <label><input type=\"checkbox\" v-model=\"clear.cache\"> {{ 'System Cache' | trans }}</label>\n                </p>\n                <p class=\"uk-form-controls-condensed\">\n                    <label><input type=\"checkbox\" v-model=\"clear.temp\"> {{ 'Temporary Files' | trans }}</label>\n                </p>\n            </div>\n\n            <div class=\"uk-modal-footer uk-text-right\">\n                <button class=\"uk-button uk-button-link uk-modal-close\" type=\"submit\" v-on=\"click: cancel\">{{ 'Cancel' | trans }}</button>\n                <button class=\"uk-button uk-button-link\" type=\"submit\" v-on=\"click: clear\">{{ 'Clear' | trans }}</button>\n            </div>\n\n        </div>\n    </div>";
	var Settings = __webpack_require__(15);

	    module.exports = {

	        data: function() {
	            return { caches: window.$caches };
	        },

	        name: 'system/cache',
	        label: 'Cache',
	        priority: 30,

	        template: __vue_template__,

	        methods: {

	            open: function(e) {
	                e.preventDefault();

	                this.$set('clear', { cache: true });

	                this.modal = UIkit.modal(this.$$.modal);
	                this.modal.show();
	            },

	            clear: function(e) {
	                e.preventDefault();

	                this.$http.post('admin/system/cache/clear', { caches: this.clear });
	                this.cancel(e);
	            },

	            cancel: function(e) {
	                e.preventDefault();

	                this.modal.hide();
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