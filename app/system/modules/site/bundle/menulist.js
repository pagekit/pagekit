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

	var __vue_template__ = "<div class=\"uk-margin\" v-repeat=\"menu: menus\">\n        <div class=\"uk-flex\">\n            <span class=\"uk-panel-title uk-flex-item-1\" v-on=\"click: edit(menu)\">{{ menu.label }}</span>\n\n            <div class=\"uk-button-dropdown\" data-uk-dropdown=\"{ mode: 'click' }\" v-component=\"type-dropdown\" inline-template=\"\">\n                <a v-on=\"click: $event.preventDefault()\"><i class=\"uk-icon uk-icon-plus\"></i></a>\n                <div class=\"uk-dropdown uk-dropdown-small\">\n                    <ul class=\"uk-nav uk-nav-dropdown\">\n                        <li v-repeat=\"type: types | unmounted\"><a v-on=\"click: add(menu, type)\">{{ type.label }}</a></li>\n                    </ul>\n                </div>\n            </div>\n        </div>\n\n        <node-list class=\"uk-nestable\"></node-list>\n\n    </div>\n\n    <p>\n        <a v-on=\"click: edit()\"><i class=\"uk-icon-th-list\"></i> {{ 'Create Menu' | trans }}</a>\n    </p>\n\n    <div v-el=\"modal\" class=\"uk-modal\">\n\n        <div v-if=\"menu\" class=\"uk-modal-dialog\">\n\n            <form v-on=\"valid: save\" name=\"menuform\">\n\n                <p>\n                    <input class=\"uk-width-1-1 uk-form-large\" name=\"label\" type=\"text\" v-model=\"menu.label\" placeholder=\"{{ 'Enter Menu Name' | trans }}\" v-valid=\"alphaNum\">\n                    <span class=\"uk-form-help-block uk-text-danger\" v-show=\"menuform.label.invalid\">{{ 'Invalid name.' | trans }}</span>\n                </p>\n                <p>\n                    <input class=\"uk-width-1-1 uk-form-large\" name=\"id\" type=\"text\" v-model=\"menu.id\" placeholder=\"{{ 'Enter Menu Slug' | trans }}\" v-valid=\"alphaNum, unique\">\n                    <span class=\"uk-form-help-block uk-text-danger\" v-show=\"menuform.id.invalid\">{{ 'Invalid slug.' | trans }}</span>\n                </p>\n\n                <button class=\"uk-button uk-button-primary\" v-attr=\"disabled: menuform.invalid\">{{ 'Save' | trans }}</button>\n                <button class=\"uk-button uk-modal-close\" v-on=\"click: cancel\">{{ 'Cancel' | trans }}</button>\n                <button v-show=\"menu.oldId\" class=\"uk-button uk-button-danger uk-float-right\" v-on=\"click: delete\">{{ 'Delete' | trans }}</button>\n\n            </form>\n        </div>\n\n    </div>";
	module.exports = {

	        inherit : true,

	        data: function() {
	            return { menu: null, unmounted: [] };
	        },

	        methods: {

	            add: function(menu, type) {
	                vm.select({ menu: menu.id, type: type.id })
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
	                this.Menus[this.menu.oldId ? 'update' : 'save']({ id: this.menu.id }, this.menu, vm.load);
	                this.cancel();
	            },

	            'delete': function (e) {
	                if (e) e.preventDefault();
	                this.Menus.delete({ id: this.menu.id }, vm.load);
	                this.cancel();
	            },

	            cancel: function (e) {
	                if (e) e.preventDefault();
	                this.$set('menu', null);
	                this.modal.hide();
	            }

	        },

	        components: {

	            'type-dropdown': {

	                inherit: true,

	                filters: {

	                    unmounted: function(types) {

	                        return types.filter(function(type) {
	                            return !type.controllers || !_.some(vm.nodes, { type: type.id });
	                        })

	                    }

	                }

	            },

	            'node-list': {

	                inherit: true,
	                template: '<node-item v-repeat="item: tree[menu.id]"></node-item>',

	                ready: function () {
	                    var self = this;
	                    UIkit.nestable(this.$el, { maxDepth: 20, group: 'site.nodes' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
	                        if (type !== 'removed') {
	                            vm.Nodes.save({ id: 'updateOrder' }, { menu: self.menu.id, nodes: nestable.list() }, vm.load);
	                        }
	                    });
	                }
	            },

	            'node-item': {

	                inherit: true,
	                replace: true,
	                template: '#node-item',

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

	                        this.Nodes.delete({ id: this.node.id }, vm.load);
	                    }

	                }
	            },

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);