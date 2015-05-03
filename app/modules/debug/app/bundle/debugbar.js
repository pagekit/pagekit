var Debugbar =
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

	var Debugbar = __webpack_require__(1);

	Debugbar.register('system', __webpack_require__(2));
	Debugbar.register('routes', __webpack_require__(3));
	Debugbar.register('events', __webpack_require__(4));
	Debugbar.register('time', __webpack_require__(5));
	Debugbar.register('memory', __webpack_require__(6));
	Debugbar.register('database', __webpack_require__(7));
	Debugbar.register('request', __webpack_require__(8));
	Debugbar.register('auth', __webpack_require__(9));
	Debugbar.register('log', __webpack_require__(10));

	$(function () {

	    new Debugbar().$appendTo('body');

	});

	module.exports = Debugbar;


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div id=\"pk-profiler\" class=\"pf-profiler\">\n\n        <div class=\"pf-navbar\">\n\n            <ul class=\"pf-navbar-nav\" v-repeat=\"navbar | orderBy 'priority'\">\n                <li v-html=\"html\" v-on=\"click: open(panel)\"></li>\n            </ul>\n\n            <a class=\"pf-close\" v-on=\"click: close\"></a>\n\n        </div>\n\n        <div v-repeat=\"panels\">\n            <div class=\"pf-profiler-panel\" v-style=\"\n                display: $value === panel ? 'block' : 'none',\n                height: height\n            \" v-component=\"{{ $value }}\" v-with=\"data[$value]\"></div>\n        </div>\n\n    </div>";
	var $ = __webpack_require__(11);
	    var Vue = __webpack_require__(12);
	    var config = window.$debugbar;

	    module.exports = Vue.extend({

	        el: function () {
	            return document.createElement('div');
	        },

	        data: function () {
	            return {
	                data: {},
	                navbar: [],
	                panels: [],
	                panel: null
	            }
	        },

	        created: function () {

	            var self = this;

	            $.getJSON(config.url, function (data) {

	                self.$set('data', data);

	                $.each(self.$options.components, function (name) {
	                    if (data[name]) {
	                        self.panels.push(name);
	                    }
	                });

	            });

	        },

	        computed: {

	            height: function() {
	                return Math.ceil(window.innerHeight / 2) + 'px';
	            }

	        },

	        methods: {

	            add: function (collector, navbar, options) {

	                this.navbar.push($.extend({ html: collector.$interpolate(navbar || '') }, options));

	            },

	            open: function (panel) {

	                if (panel) {
	                    this.$set('panel', panel);
	                }

	            },

	            close: function () {

	                this.$set('panel', null);

	            }

	        }

	    });

	    module.exports.register = function (name, options) {
	        this.options.components[name] = Vue.extend(options);
	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<h1>Information</h1>\n\n    <h2>System</h2>\n    <table class=\"pf-table pf-table-dropdown\">\n        <tbody>\n            <tr>\n                <td>Pagekit</td>\n                <td>{{ version }}</td>\n            </tr>\n            <tr>\n                <td>Server</td>\n                <td>{{ server }}</td>\n            </tr>\n            <tr>\n                <td>Useragent</td>\n                <td>{{ useragent }}</td>\n            </tr>\n        </tbody>\n    </table>\n\n    <h2>PHP</h2>\n    <table class=\"pf-table pf-table-dropdown\">\n        <tbody>\n            <tr>\n                <td>PHP</td>\n                <td>{{ phpversion }}</td>\n            </tr>\n            <tr>\n                <td>PHP SAPI</td>\n                <td>{{ sapi_name }}</td>\n            </tr>\n            <tr>\n                <td>System</td>\n                <td>{{ php }}</td>\n            </tr>\n            <tr>\n                <td>Extensions</td>\n                <td>{{ extensions }}</td>\n            </tr>\n        </tbody>\n    </table>\n\n    <h2>Database</h2>\n    <table class=\"pf-table pf-table-dropdown\">\n        <tbody>\n            <tr>\n                <td>Driver</td>\n                <td>{{ dbdriver }}</td>\n            </tr>\n            <tr>\n                <td>Version</td>\n                <td>{{ dbversion }}</td>\n            </tr>\n            <tr>\n                <td>Client</td>\n                <td>{{ dbclient }}</td>\n            </tr>\n        </tbody>\n    </table>";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, '<a title="System Information"><div class="pf-icon-large pf-icon-pagekit"></div></a>', {priority: 10, panel: 'system'});
	    }

	  };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<h1>Routes</h1>\n\n    <table class=\"pf-table\">\n        <thead>\n            <tr>\n                <th>Name</th>\n                <th>Pattern</th>\n                <th>Controller</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr v-repeat=\"routes\">\n                <td>{{ name }}</td>\n                <td>{{ pattern }} {{ methods | str }}</td>\n                <td><abbr title=\"{{ controller }}\">{{ controller | short }}</abbr></td>\n            </tr>\n        </tbody>\n    </table>";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, '<a title="Routes"><div class="pf-icon pf-icon-routes"></div> Routes</a>', {priority: 20, panel: 'routes'});
	    },

	    filters: {

	        str: function (methods) {
	            return methods.length ? '(' + methods + ')' : '';
	        },

	        short: function (controller) {
	            return controller.split('\\').pop();
	        }

	    }

	  };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, '<a title="Events"><div class="pf-icon pf-icon-events"></div> Events</a>', {priority: 10});
	    }

	  };


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "";
	module.exports = {

	    ready: function () {
	        this.$parent.add(this, '<a title="Time"><div class="pf-icon pf-icon-time"></div> {{ duration_str }}</a>', {priority: 30});
	    }

	  };


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, '<a title="Memory"><div class="pf-icon pf-icon-memory"></div> {{ peak_usage_str }}</a>', {priority: 40});
	    }

	  };


/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<h1>Queries</h1>\n\n    <p v-show=\"!nb_statements\">\n        <em>No queries.</em>\n    </p>\n\n    <div v-repeat=\"statements\">\n\n        <pre><code>{{ sql }}</code></pre>\n\n        <p class=\"pf-submenu\">\n            <span>{{ duration_str }}</span>\n            <span>{{ params | json }}</span>\n        </p>\n\n    </div>\n\n    <div v-el=\"navbar\" style=\"display: none\">\n\n        <a title=\"Database\" class=\"pf-parent\">\n            <div class=\"pf-icon pf-icon-database\"></div> {{ nb_statements }}\n        </a>\n\n        <div class=\"pf-dropdown\">\n\n            <table class=\"pf-table pf-table-dropdown\">\n                <tbody>\n                    <tr>\n                        <td>Queries</td>\n                        <td>{{ nb_statements }}</td>\n                    </tr>\n                    <tr>\n                        <td>Time</td>\n                        <td>{{ accumulated_duration_str }}</td>\n                    </tr>\n                    <tr>\n                        <td>Driver</td>\n                        <td>{{ driver }}</td>\n                    </tr>\n                </tbody>\n            </table>\n\n        </div>\n\n    </div>";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, $(this.$$.navbar).html(), {priority: 50, panel: 'database'});
	    }

	  };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, '<a title="Request"><div class="pf-icon pf-icon-request"></div> <span class="pf-badge">200</span> @test</a>', {priority: 10});
	    }

	  };


/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div v-el=\"navbar\" style=\"display: none\">\n\n        <a title=\"User\"><div class=\"pf-icon pf-icon-auth\" v-class=\"pf-parent: user\"></div> {{ label }}</a>\n\n        <div class=\"pf-dropdown\" v-show=\"user\">\n\n            <table class=\"pf-table pf-table-dropdown\">\n                <tbody>\n                    <tr>\n                        <td>Username</td>\n                        <td>{{ user }}</td>\n                    </tr>\n                    <tr>\n                        <td>Roles</td>\n                        <td>{{ roles | json }}</td>\n                    </tr>\n                    <tr>\n                        <td>Authenticated</td>\n                        <td>{{ authenticated ? 'yes' : 'no' }}</td>\n                    </tr>\n                    <tr>\n                        <td>Class</td>\n                        <td>{{ user_class }}</td>\n                    </tr>\n                </tbody>\n            </table>\n\n        </div>\n\n    </div>";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, $(this.$$.navbar).html(), {priority: 60});
	    },

	    computed: {

	        label: function () {

	            if (this.user) {
	                return this.user;
	            }

	            return this.enabled ? 'You are not authenticated.' : 'Authentication is disabled.';
	        }

	    }

	  };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<h1>Logs</h1>\n\n    <table class=\"pf-table\">\n        <thead>\n            <tr>\n                <th>Message</th>\n                <th>Level</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr v-repeat=\"records\">\n                <td>{{ message }}</td>\n                <td>{{ level_name }}</td>\n            </tr>\n        </tbody>\n    </table>";
	module.exports = {

	    ready: function () {
	      this.$parent.add(this, '<a title="Log">Log ({{ records.length }})</a>', {priority: 70, panel: 'log'});
	    }

	  };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue;

/***/ }
/******/ ]);