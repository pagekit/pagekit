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

	var $ = __webpack_require__(1);
	var Vue = __webpack_require__(2);

	$(function () {

	  var opts = __webpack_require__(8);
	  var app  = new Vue(opts).$mount('#themes');

	});


/***/ },

/***/ 1:
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },

/***/ 2:
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue;

/***/ },

/***/ 8:
/***/ function(module, exports, __webpack_require__) {

	var $ = __webpack_require__(1);
	var data = window.$themes;

	module.exports = {

	    mixins: [
	        __webpack_require__(16)
	    ],

	    data: $.extend(data, {
	        updates: null,
	        search: '',
	        status: ''
	    }),

	    ready: function () {
	        this.load();
	    },

	    methods: {

	        icon: function (pkg) {

	            var img;

	            if (pkg.extra.image) {
	                img = this.$url.static('themes/:name/:image', {name: pkg.name, image: pkg.extra.image});
	            } else {
	                img = this.$url.static('app/system/assets/images/placeholder-800x600.svg');
	            }

	            return img;
	        },

	        load: function () {

	            var vm = this;

	            this.$set('status', 'loading');

	            this.queryUpdates(this.api, this.packages).done(function (data) {
	                vm.$set('updates', data.packages.length ? data.packages : null);
	                vm.$set('status', '');
	            }).fail(function () {
	                vm.$set('status', 'error');
	            });
	        },

	        enable: function (pkg) {
	            this.enablePackage(pkg).success(function (data) {
	                UIkit.notify(data.message);
	            }).error(function (data) {
	                UIkit.notify(data, 'danger');
	            });
	        },

	        uninstall: function (pkg) {
	            this.uninstallPackage(pkg, this.packages).success(function (data) {
	                UIkit.notify(data.message);
	            }).error(function (data) {
	                UIkit.notify(data, 'danger');
	            });
	        }

	    }

	};




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