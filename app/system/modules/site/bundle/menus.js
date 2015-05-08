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

	var __vue_template__ = "<div class=\"uk-margin\" v-repeat=\"menu: menus\">\n        <div class=\"uk-flex\">\n            <span class=\"uk-panel-title uk-flex-item-1\" v-on=\"click: edit(menu)\">{{ menu.label }}</span>\n\n            <div class=\"uk-button-dropdown\" data-uk-dropdown=\"{ mode: 'click' }\">\n                <a v-on=\"click: $event.preventDefault()\"><i class=\"uk-icon uk-icon-plus\"></i></a>\n                <div class=\"uk-dropdown uk-dropdown-small\">\n                    <ul class=\"uk-nav uk-nav-dropdown\">\n                        <li v-repeat=\"type: types | unmounted\"><a v-on=\"click: add(menu, type)\">{{ type.label }}</a></li>\n                    </ul>\n                </div>\n            </div>\n        </div>\n\n        <node-list class=\"uk-nestable\"></node-list>\n\n    </div>\n\n    <p>\n        <a v-on=\"click: edit()\"><i class=\"uk-icon-th-list\"></i> {{ 'Create Menu' | trans }}</a>\n    </p>\n\n    <div class=\"uk-modal\" v-el=\"modal\">\n\n        <div class=\"uk-modal-dialog\" v-if=\"menu\">\n\n            <form name=\"menuform\" v-on=\"valid: save\">\n\n                <p>\n                    <input class=\"uk-width-1-1 uk-form-large\" name=\"label\" type=\"text\" placeholder=\"{{ 'Enter Menu Name' | trans }}\" v-model=\"menu.label\" v-valid=\"alphaNum\">\n                    <span class=\"uk-form-help-block uk-text-danger\" v-show=\"menuform.label.invalid\">{{ 'Invalid name.' | trans }}</span>\n                </p>\n                <p>\n                    <input class=\"uk-width-1-1 uk-form-large\" name=\"id\" type=\"text\" placeholder=\"{{ 'Enter Menu Slug' | trans }}\" v-model=\"menu.id\" v-valid=\"alphaNum, unique\">\n                    <span class=\"uk-form-help-block uk-text-danger\" v-show=\"menuform.id.invalid\">{{ 'Invalid slug.' | trans }}</span>\n                </p>\n\n                <button class=\"uk-button uk-button-primary\" v-attr=\"disabled: menuform.invalid\">{{ 'Save' | trans }}</button>\n                <button class=\"uk-button uk-modal-close\" v-on=\"click: cancel\">{{ 'Cancel' | trans }}</button>\n                <button class=\"uk-button uk-button-danger uk-float-right\" v-show=\"menu.oldId\" v-on=\"click: delete\">{{ 'Delete' | trans }}</button>\n\n            </form>\n        </div>\n\n    </div>";
	module.exports = {

	        inherit : true,

	        data: function() {
	            return { menu: null, unmounted: [] };
	        },

	        methods: {

	            add: function(menu, type) {
	                this.select({ menu: menu.id, type: type.id })
	            },

	            edit: function (menu) {

	                menu = Vue.util.extend({}, menu || { label: '', id: '' });
	                menu.oldId = menu.id;

	                if (menu.fixed) return;

	                this.$set('menu', menu);

	                this.modal = UIkit.modal(this.$$.modal);
	                this.modal.show();
	            },

	            save: function (e) {
	                if (e) e.preventDefault();
	                this.Menus[this.menu.oldId ? 'update' : 'save']({ id: this.menu.id }, this.menu, this.load);
	                this.cancel();
	            },

	            'delete': function (e) {
	                if (e) e.preventDefault();
	                this.Menus.delete({ id: this.menu.id }, this.load);
	                this.cancel();
	            },

	            cancel: function (e) {
	                if (e) e.preventDefault();
	                this.$set('menu', null);
	                this.modal.hide();
	            }

	        },

	        filters: {

	            unmounted: function(types) {

	                var self = this;

	                return types.filter(function(type) {
	                    return !type.controllers || !_.some(self.nodes, { type: type.id });
	                })

	            }

	        },

	        components: {

	            'node-list': __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"./list.vue\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);