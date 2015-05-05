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

	var __vue_template__ = "<node-item v-repeat=\"item: tree[menu.id]\"></node-item>";
	module.exports = {

	        inherit: true,

	        ready: function () {

	            var self = this;

	            UIkit.nestable(this.$el, { maxDepth: 20, group: 'site.nodes' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
	                if (type !== 'removed') {
	                    self.Nodes.save({ id: 'updateOrder' }, { menu: self.menu.id, nodes: nestable.list() }, self.load);
	                }
	            });

	        },

	        components: {

	            'node-item': __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"./item.vue\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))

	        }
	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);