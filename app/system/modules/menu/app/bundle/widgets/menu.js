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

	var __vue_template__ = "{{&gt; settings}}\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-menu\" class=\"uk-form-label\">{{ 'Menu' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select id=\"form-menu\" class=\"uk-form-width-large\" v-model=\"widget.settings.menu\" options=\"menuOptions\"></select>\n        </div>\n    </div>\n    <div class=\"uk-form-row\">\n        <label for=\"form-style\" class=\"uk-form-label\">{{ 'Start Level' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select id=\"form-style\" class=\"uk-form-width-large\" v-model=\"widget.settings.start_level\">\n                <option value=\"1\">1</option>\n                <option value=\"2\">2</option>\n                <option value=\"3\">3</option>\n                <option value=\"4\">4</option>\n                <option value=\"5\">5</option>\n                <option value=\"6\">6</option>\n                <option value=\"7\">7</option>\n                <option value=\"8\">8</option>\n                <option value=\"9\">9</option>\n                <option value=\"10\">10</option>\n            </select>\n        </div>\n    </div>\n    <div class=\"uk-form-row\">\n        <label for=\"form-style\" class=\"uk-form-label\">{{ 'Depth' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select id=\"form-style\" class=\"uk-form-width-large\" v-model=\"widget.settings.depth\">\n                <option value=\"\">{{ 'No Limit' | trans }}</option>\n                <option value=\"1\">1</option>\n                <option value=\"2\">2</option>\n                <option value=\"3\">3</option>\n                <option value=\"4\">4</option>\n                <option value=\"5\">5</option>\n                <option value=\"6\">6</option>\n                <option value=\"7\">7</option>\n                <option value=\"8\">8</option>\n                <option value=\"9\">9</option>\n                <option value=\"10\">10</option>\n            </select>\n        </div>\n    </div>\n    <div class=\"uk-form-row\">\n        <span class=\"uk-form-label\">{{ 'Sub Items' | trans }}</span>\n        <div class=\"uk-form-controls uk-form-controls-text\">\n            <p class=\"uk-form-controls-condensed\">\n                <label><input type=\"radio\" value=\"all\" v-model=\"widget.settings.mode\"> {{ 'Show all' | trans }}</label>\n            </p>\n            <p class=\"uk-form-controls-condensed\">\n                <label><input type=\"radio\" value=\"active\" v-model=\"widget.settings.mode\"> {{ 'Show only for active item' | trans }}</label>\n            </p>\n        </div>\n    </div>";
	module.exports = {

	        name: 'site-menu',
	        label: 'Settings',
	        active: 'site.menu',
	        priority: 0,
	        template: __vue_template__,

	        data: function () {
	            return { menus: window.$menus };
	        },

	        computed: {

	            menuOptions: function() {
	                return _.map(this.menus, function(menu) { return { text: menu.label, value: menu.id }; });
	            }

	        }

	    };

	    window.Widgets.addSection(module.exports)
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);