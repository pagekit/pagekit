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

	var __vue_template__ = "<div class=\"uk-form-horizontal\">\n\n        <div class=\"uk-form-row\">\n            <label for=\"form-theme-panel\" class=\"uk-form-label\">{{ 'Panel Style' | trans }}</label>\n            <div class=\"uk-form-controls\">\n                <select id=\"form-theme-panel\" class=\"uk-form-width-large\" v-model=\"config.panel\">\n                    <option value=\"\">{{ 'None' | trans }}</option>\n                    <option value=\"uk-panel-box\">{{ 'Box' | trans }}</option>\n                    <option value=\"uk-panel-box uk-panel-box-primary\">{{ 'Box Primary' | trans }}</option>\n                    <option value=\"uk-panel-box uk-panel-box-secondary\">{{ 'Box Secondary' | trans }}</option>\n                    <option value=\"uk-panel-header\">{{ 'Header' | trans }}</option>\n                    <option value=\"uk-panel-space\">{{ 'Space' | trans }}</option>\n                </select>\n            </div>\n        </div>\n\n        <div class=\"uk-form-row\">\n            <label for=\"form-theme-badge\" class=\"uk-form-label\">{{ 'Badge' | trans }}</label>\n            <div class=\"uk-form-controls\">\n                <input id=\"form-theme-badge\" class=\"uk-form-width-small\" type=\"text\" v-model=\"config.badge.text\">\n                <select class=\"uk-form-width-small\" v-model=\"config.badge.type\">\n                    <option value=\"uk-panel-badge uk-badge\">{{ 'Default' | trans }}</option>\n                    <option value=\"uk-panel-badge uk-badge uk-badge-success\">{{ 'Success' | trans }}</option>\n                    <option value=\"uk-panel-badge uk-badge uk-badge-warning\">{{ 'Warning' | trans }}</option>\n                    <option value=\"uk-panel-badge uk-badge uk-badge-danger\">{{ 'Danger' | trans }}</option>\n                </select>\n            </div>\n        </div>\n\n        <div class=\"uk-form-row\">\n            <span class=\"uk-form-label\">{{ 'Alignment' | trans }}</span>\n            <div class=\"uk-form-controls uk-form-controls-text\">\n                <label><input type=\"checkbox\" value=\"center-content\" v-model=\"config.alignment\"> {{ 'Center the title and content.' | trans }}</label>\n            </div>\n        </div>\n\n    </div>";
	module.exports = {

	        name: 'theme-settings',
	        label: 'Theme',
	        priority: 50,
	        template: __vue_template__

	    };

	    window.Widgets.addSection(module.exports)
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);