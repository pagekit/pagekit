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

	var __vue_template__ = "<div data-uk-observe=\"\">\n\n       <ul class=\"uk-grid uk-grid-width-small-1-2 uk-grid-width-xlarge-1-3\" data-uk-grid-margin=\"\" data-uk-grid-match=\"{target:'.uk-panel'}\">\n            <li v-repeat=\"pkg: packages\">\n                <a class=\"uk-panel uk-panel-box pk-marketplace-panel uk-overlay-hover\">\n                    <div class=\"uk-panel-teaser\">\n                        <img width=\"800\" height=\"600\" alt=\"{{ pkg.title }}\" v-attr=\"src: pkg.extra.teaser\">\n                    </div>\n                    <h2 class=\"uk-panel-title uk-margin-remove\">{{ pkg.title }}</h2>\n                    <p class=\"uk-margin-remove uk-text-small uk-text-muted\">{{ pkg.author.name }}</p>\n                    <div class=\"uk-overlay-panel uk-overlay-background uk-flex uk-flex-center uk-flex-middle\">\n                        <div>\n                            <button class=\"uk-button uk-button-primary uk-button-large\" v-on=\"click: details(pkg)\">{{ 'Details' | trans }}</button>\n                        </div>\n                    </div>\n                </a>\n            </li>\n        </ul>\n\n        <v-pagination v-with=\"page: page, pages: pages\" v-show=\"pages > 1\"></v-pagination>\n\n        <div class=\"uk-modal\" v-el=\"modal\">\n            <div class=\"uk-modal-dialog uk-modal-dialog-large pk-marketplace-modal-dialog\">\n\n                <div class=\"pk-marketplace-modal-action\">\n                    <button class=\"uk-button\" disabled=\"disabled\" v-show=\"isInstalled(pkg)\">{{ 'Installed' | trans }}</button>\n                    <button class=\"uk-button uk-button-primary\" v-on=\"click: install(pkg)\" v-show=\"!isInstalled(pkg)\">\n                        {{ 'Install' | trans }} <i class=\"uk-icon-spinner uk-icon-spin\" v-show=\"status == 'installing'\"></i>\n                    </button>\n                </div>\n\n                <iframe class=\"uk-width-1-1 uk-height-1-1\" v-attr=\"src: iframe\"></iframe>\n\n            </div>\n        </div>\n\n        <p class=\"uk-alert uk-alert-info\" v-show=\"!packages.length\">{{ 'Nothing found.' | trans }}</p>\n        <p class=\"uk-alert uk-alert-warning\" v-show=\"status == 'error'\">{{ 'Cannot connect to the marketplace. Please try again later.' | trans }}</p>\n\n    </div>";
	var $ = __webpack_require__(1);
	    var _ = __webpack_require__(14);
	    var Vue = __webpack_require__(2);

	    module.exports = {

	        replace: true,

	        template: __vue_template__,

	        mixins: [
	            __webpack_require__(16)
	        ],

	        data: function () {
	            return {
	                api: {},
	                search: '',
	                type: 'extension',
	                pkg: null,
	                packages: null,
	                updates: null,
	                installed: [],
	                page: 0,
	                pages: 0,
	                iframe: '',
	                status: ''
	            };
	        },

	        ready: function () {

	            var vm = this;

	            this.query();
	            this.queryUpdates(this.api, this.installed).done(function (data) {
	                vm.$set('updates', data.packages.length ? data.packages : null);
	            });
	        },

	        watch: {

	            search: function () {
	                this.query();
	            },

	            type: function () {
	                this.query();
	            },

	            page: function () {
	                this.query(this.page);
	            }

	        },

	        methods: {

	            query: function (page) {

	                var vm = this, url = this.api.url + '/package/search';

	                $.post(url, {q: this.search, type: this.type, page: page || 0}, function (data) {
	                    vm.$set('packages', data.packages);
	                    vm.$set('pages', data.pages);
	                }, 'jsonp').fail(function () {
	                    vm.$set('packages', null);
	                    vm.$set('status', 'error');
	                });
	            },

	            details: function (pkg) {

	                if (!this.modal) {
	                    this.modal = UIkit.modal(this.$$.modal);
	                }

	                this.$set('iframe', this.api.url.replace(/\/api$/, '') + '/marketplace/frame/' + pkg.name);
	                this.$set('pkg', pkg);

	                this.modal.show();
	            },

	            install: function (pkg) {

	                var vm = this;

	                vm.$set('status', 'installing');

	                this.installPackage(pkg, this.installed).error(function (data) {
	                    UIkit.notify(data, 'danger');
	                }).always(function (data) {
	                    vm.$set('status', '');
	                });
	            },

	            isInstalled: function (pkg) {
	                return _.isObject(pkg) ? _.find(this.installed, 'name', pkg.name) : undefined;
	            }
	        }

	    };

	    Vue.component('v-marketplace', module.exports);
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },

/***/ 1:
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },

/***/ 2:
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue;

/***/ },

/***/ 14:
/***/ function(module, exports, __webpack_require__) {

	module.exports = _;

/***/ },

/***/ 16:
/***/ function(module, exports, __webpack_require__) {

	var $ = __webpack_require__(1);

	module.exports = {

	    methods: {

	        queryUpdates: function (api, packages) {

	            var pkgs = {};

	            $.each(packages, function (name, pkg) {
	                pkgs[pkg.name] = pkg.version;
	            });

	            return $.ajax(api.url + '/package/update', {
	                data: {'api_key': api.key, 'packages': JSON.stringify(pkgs)},
	                dataType: 'jsonp'
	            });
	        },

	        enablePackage: function (pkg) {
	            return this.$http.post('admin/system/package/enable', {name: pkg.name}, function (data) {
	                if (!data.error) {
	                    pkg.enabled = true;
	                }
	            });
	        },

	        disablePackage: function (pkg) {
	            return this.$http.post('admin/system/package/disable', {name: pkg.name}, function (data) {
	                if (!data.error) {
	                    pkg.enabled = false;
	                }
	            });
	        },

	        installPackage: function (pkg, packages) {
	            return this.$http.post('admin/system/package/install',  {'package': pkg.version}, function (data) {
	                if (packages && data.message) {
	                    packages.push(pkg);
	                }
	            });
	        },

	        uninstallPackage: function (pkg, packages) {
	            return this.$http.post('admin/system/package/uninstall', {name: pkg.name}, function (data) {
	                if (packages && !data.error) {
	                    packages.splice(packages.indexOf(pkg), 1);
	                }
	            });
	        }

	    }

	};


/***/ }

/******/ });