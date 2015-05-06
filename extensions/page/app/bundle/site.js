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

	var Site = __webpack_require__(1);

	Site.register(__webpack_require__(2));
	Site.register(__webpack_require__(3));


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Site;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "{{&gt; settings-fields}}";
	module.exports = {

	        name: 'page-settings',
	        label: 'Settings',
	        priority: 0,
	        active: 'page',

	        template: __vue_template__

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-form-row\">\n        <label for=\"form-page-title\" class=\"uk-form-label\">{{ 'Page Title' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <input id=\"form-page-title\" class=\"uk-form-width-large\" type=\"text\" name=\"page[title]\" v-model=\"page.title\">\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-url\" class=\"uk-form-label\">{{ 'Content' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <!-- TODO: integrate editor-->\n            <textarea id=\"post-content\" name=\"page[content]\" autocomplete=\"off\" style=\"visibility:hidden; height:543px\" data-finder-options=\"{root:'\/storage'}\" v-model=\"page.content\" v-el=\"editor\"></textarea>\n        </div>\n    </div>\n\n    <div class=\"uk-form-row\">\n        <span class=\"uk-form-label\">{{ 'Options' | trans }}</span>\n        <div class=\"uk-form-controls\">\n            <label><input type=\"checkbox\" name=\"page[data][title]\" v-model=\"page.data.title\"> {{ 'Show Title' | trans }}</label>\n        </div>\n        <div class=\"uk-form-controls\">\n            <label><input type=\"checkbox\" name=\"page[data][markdown]\" v-model=\"page.data.markdown\"> {{ 'Enable Markdown' | trans }}</label>\n        </div>\n    </div>";
	module.exports = {

	        name: 'page-content',
	        label: 'Content',
	        priority: 10,
	        active: 'page',
	        template: __vue_template__,

	        data: function() {
	            // TODO test
	            return { page: {} }
	        },

	        ready: function() {
	            this.editor = UIkit.htmleditor(this.$$.editor, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, { markdown: this.$get('page.data.markdown') }));
	        },

	        events: {

	            save: function(data) {
	                data.page = this.page;
	            }

	        },

	        watch: {

	            'page.data.markdown': function(markdown) {
	                this.editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
	            }

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);