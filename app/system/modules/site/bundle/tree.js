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

	module.exports = {

	    created: function () {

	        this.Nodes = this.$resource('api/site/node/:id');
	        this.Menus = this.$resource('api/site/menu/:id', {}, { 'update': { method: 'PUT' }});

	        this.$add('nodes', []);
	        this.$add('menus', []);
	        this.$add('tree', {});

	        this.load();

	    },

	    events: {

	        loaded: function() {

	            var parents = _(this.nodes).sortBy('priority').groupBy('parentId').value(),
	                build = function (collection) {
	                    return collection.map(function(node) {
	                        return { node: node, children: build(parents[node.id] || [])}
	                    })
	                };

	            this.$set('tree', _.groupBy(build(parents[0] || []), function(node) { return node.node.menu }));
	        }

	    },

	    methods: {

	        load: function () {

	            var d1 = $.Deferred(), d2 = $.Deferred(), deferred = $.when(d1, d2);

	            deferred.done(function(nodes, menus) {

	                this.$set('nodes', nodes);
	                this.$set('menus', menus);

	                this.$emit('loaded');

	            }.bind(this));

	            this.Nodes.query(function (nodes) {
	                d1.resolve(nodes);
	            });

	            this.Menus.query(function (menus) {
	                d2.resolve(menus);
	            });

	            return deferred;
	        }

	    }

	};


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = _;

/***/ }
/******/ ]);