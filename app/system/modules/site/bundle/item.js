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

	var __vue_template__ = "<li class=\"uk-nestable-list-item\" v-class=\"uk-parent: isParent, uk-active: isActive\" data-id=\"{{ node.id }}\">\n\n        <div class=\"uk-nestable-item uk-visible-hover-inline\" v-on=\"click: select(node)\">\n            <div class=\"uk-nestable-handle\"></div>\n            <div data-nestable-action=\"toggle\"></div>\n            {{ node.title }}\n\n            <i class=\"uk-float-right uk-icon-home\" title=\"{{ 'Frontpage' | trans }}\" v-show=\"isFrontpage\"></i>\n            <a class=\"uk-hidden uk-float-right\" title=\"{{ 'Delete' | trans }}\" v-on=\"click: delete\"><i class=\"uk-icon-minus-circle\"></i></a>\n        </div>\n\n        <ul class=\"uk-nestable-list\" v-if=\"isParent\">\n            <node-item v-repeat=\"item: item.children\"></node-item>\n        </ul>\n\n    </li>";
	module.exports = {

	        inherit: true,
	        replace: true,

	        computed: {

	            node: function() {
	                return this.item.node;
	            },

	            isActive: function() {
	                return this.node === this.selected;
	            },

	            isParent: function() {
	                return this.item.children.length;
	            },

	            isFrontpage: function() {
	                return this.node.id === this.frontpage;
	            }

	        },

	        methods: {

	            'delete': function(e) {

	                e.preventDefault();
	                e.stopPropagation();

	                this.Nodes.delete({ id: this.node.id }, this.load);
	            }

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);