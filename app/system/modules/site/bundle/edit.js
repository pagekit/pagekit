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

	var __vue_template__ = "<form v-show=\"node.type\" class=\"uk-form uk-form-horizontal\" name=\"form\" v-on=\"valid: save\">\n\n        <div class=\"uk-clearfix uk-margin\">\n\n            <div class=\"uk-float-left\">\n\n                <h2 v-if=\"node.id\" class=\"uk-h2\">{{ node.title }} ({{ type.label }})</h2>\n                <h2 v-if=\"!node.id\" class=\"uk-h2\">{{ 'Add %type%' | trans {type:type.label} }}</h2>\n\n            </div>\n\n            <div class=\"uk-float-right\">\n\n                <a class=\"uk-button\" v-on=\"click: cancel()\">{{ 'Cancel' | trans }}</a>\n                <button class=\"uk-button uk-button-primary\" type=\"submit\" v-attr=\"disabled: form.invalid\">{{ 'Save' | trans }}</button>\n\n            </div>\n\n        </div>\n\n        <div v-el=\"edit\"></div>\n\n    </form>";
	module.exports = {

	        inherit: true,

	        data: function() {
	            return { node: {} }
	        },

	        watch: {

	            selected: 'reload'

	        },

	        computed: {

	            type: function() {
	                return (_.find(this.types, { id: this.node.type }) || {});
	            },

	            path: function() {
	                return (this.node.path ? this.node.path.split('/').slice(0, -1).join('/') : '') + '/' + (this.node.slug || '');
	            },

	            isFrontpage: function() {
	                return this.node.id === this.frontpage;
	            }

	        },

	        methods: {

	            reload: function() {

	                var self = this;

	                if (!this.selected) {
	                    this.node = {};
	                    return;
	                }

	                this.$http.get(this.$url('admin/site/edit', (this.selected.id ? { id: this.selected.id } : { type: this.selected.type })), function(data) {

	                    if (self.edit) {
	                        self.edit.$destroy();
	                    }

	                    data.node.menu = self.selected.menu;

	                    self.$set('node', data.node);

	                    $(self.$$.edit).empty().html(data.view);

	                    self.edit = self.$addChild({

	                        inherit: true,
	                        data: data.data,
	                        el: self.$$.edit,

	                        ready: function() {
	                            UIkit.tab(this.$$.tab, { connect: this.$$.content });
	                        }

	                    });
	                });
	            },

	            save: function (e) {

	                e.preventDefault();

	                var data = _.merge($(":input", e.target).serialize().parse(), { node: this.node });

	                this.$broadcast('save', data);

	                this.Nodes.save({ id: this.node.id }, data, function(node) {

	                    vm.selected.id = parseInt(node.id);
	                    vm.load();

	                    if (data.frontpage) {
	                        vm.$set('frontpage', node.id);
	                    }
	                });
	            },

	            cancel: function() {
	                if (this.node.id) {
	                    this.reload();
	                } else {
	                    this.select();
	                }
	            }

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);