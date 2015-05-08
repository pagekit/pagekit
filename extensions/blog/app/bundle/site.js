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

	Site.component('blog', __webpack_require__(2));
	Site.component('blog-post', __webpack_require__(3));


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Site;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "{{&gt; settings}}";
	module.exports = {

	        section: {
	            name: 'blog',
	            label: 'Settings',
	            priority: 0,
	            active: 'blog$'
	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "{{&gt; settings}}\n\n    <div class=\"uk-form-row\">\n\n        <label for=\"form-post\" class=\"uk-form-label\">{{ 'Post' | trans }}</label>\n        <div class=\"uk-form-controls\">\n            <select class=\"uk-form-width-large\" v-model=\"node.data.variables.id\">\n                <option value=\"\">- {{ 'Select Post' | trans }} -</option>\n            </select>\n        </div>\n\n    </div>";
	module.exports = {

	        section: {
	            name: 'blog-post',
	            label: 'Settings',
	            priority: 0,
	            active: 'blog-post'
	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);