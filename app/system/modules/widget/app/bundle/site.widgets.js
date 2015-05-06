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

	var __vue_template__ = "<div class=\"uk-margin uk-flex uk-flex-space-between uk-flex-wrap\" data-uk-margin=\"\">\n        <div data-uk-margin=\"\">\n\n            <div class=\"uk-button-dropdown\" data-uk-dropdown=\"{ mode: 'click' }\">\n                <button class=\"uk-button uk-button-primary\" type=\"button\">{{ 'Add Widget' | trans }}</button>\n                <div class=\"uk-dropdown uk-dropdown-small\">\n                    <ul class=\"uk-nav uk-nav-dropdown\">\n                        <li v-repeat=\"type: config.types\"><a v-on=\"click: add(type)\">{{ type.name }}</a></li>\n                    </ul>\n                </div>\n            </div>\n\n            <a class=\"uk-button pk-button-danger\" v-show=\"selected.length\" v-on=\"click: remove\">{{ 'Delete' | trans }}</a>\n\n            <div class=\"uk-button-dropdown\" v-show=\"selected.length\" data-uk-dropdown=\"{ mode: 'click' }\">\n                <button class=\"uk-button\" type=\"button\">{{ 'More' | trans }} <i class=\"uk-icon-caret-down\"></i></button>\n                <div class=\"uk-dropdown uk-dropdown-small\">\n                    <ul class=\"uk-nav uk-nav-dropdown\">\n                        <li><a v-on=\"click: copy\">{{ 'Copy' | trans }}</a></li>\n                    </ul>\n                </div>\n            </div>\n\n        </div>\n        <div data-uk-margin=\"\">\n            <input type=\"text\" v-model=\"search\" placeholder=\"{{ 'Search' | trans }}\" v-on=\"keypress: $event.preventDefault() | key enter\" debounce=\"200\">\n        </div>\n    </div>\n\n    <div class=\"uk-overflow-container\">\n\n        <div class=\"pk-table-fake pk-table-fake-header pk-table-fake-header-indent pk-table-fake-border\">\n            <div class=\"pk-table-width-minimum\"><input type=\"checkbox\" v-check-all=\"selected: input[name=id]\"></div>\n            <div class=\"pk-table-min-width-100\">{{ 'Title' | trans }}</div>\n            <div class=\"pk-table-width-150\">{{ 'Position' | trans }}</div>\n            <div class=\"pk-table-width-150\">{{ 'Type' | trans }}</div>\n        </div>\n\n        <div v-repeat=\"position: positions\" v-show=\"position | hasWidgets\">\n\n            <div class=\"pk-table-fake pk-table-fake-header pk-table-fake-subheading\">\n                <div>\n                    {{ position.name | trans }}\n                    <span v-if=\"position.description\" class=\"uk-text-muted\">{{ position.description | trans }}</span>\n                </div>\n            </div>\n\n            <widget-list v-ref=\"nestables\"></widget-list>\n\n        </div>\n\n    </div>\n\n    <div v-el=\"modal\" class=\"uk-modal\" v-on=\"close: cancel()\">\n        <div class=\"uk-modal-dialog uk-modal-dialog-large\">\n            <iframe v-attr=\"src: editUrl\" class=\"uk-width-1-1\" height=\"800\"></iframe>\n        </div>\n    </div>";
	var Site = __webpack_require__(1);

	    module.exports = {

	        name: 'widgets',
	        label: 'Widgets',
	        priority: 20,

	        template: __vue_template__,

	        data: function() {

	            return {
	                search: '',
	                positions: [],
	                config: window.$widgets
	            };

	        },

	        created: function() {
	            this.Widgets = this.$resource('api/widget/:id');
	            this.load();
	        },

	        ready: function() {
	            this.modal = UIkit.modal(this.$$.modal);
	            this.modal.on('hide.uk.modal', this.cancel);
	        },

	        computed: {

	            positionOptions: function() {
	                return [{ text: this.$trans('- Assign -'), value: '' }].concat(
	                    _.map(this.config.positions, function(position) { return { text: this.$trans(position.name), value: position.id };}.bind(this))
	                );
	            }

	        },

	        filters: {

	            hasWidgets: function(position) {
	                return position.widgets.filter(function(widget) { return this.applyFilter(widget) }.bind(this)).length;
	            },

	            showWidget: function(widget) {
	                return this.applyFilter(widget);
	            }

	        },

	        methods: {

	            applyFilter: function(widget) {
	                return !this.search || widget.title.toLowerCase().indexOf(this.search.toLowerCase()) !== -1;
	            },

	            load: function() {

	                var self = this;

	                this.Widgets.query({ grouped: true }, function (data) {
	                    self.$set('selected', []);

	                    var positions = self.config.positions.concat({ id: '', name: self.$trans('Unassigned Widgets')}).map(function(position) {
	                        return _.extend({ widgets: data[position.id] || [] }, position);
	                    });

	                    self.$set('positions', positions);
	                });
	            },

	            copy: function() {

	                var widgets = _.merge([], this.getSelected());

	                widgets.forEach(function(widget) {
	                    delete widget.id;
	                });

	                this.Widgets.save({ id: 'bulk' }, { widgets: widgets }, this.load);
	            },

	            remove: function() {
	                this.Widgets.delete({ id: 'bulk' }, { ids: this.selected }, this.load);
	            },

	            reorder: function(position, widgets) {
	                this.Widgets.save({ id: 'positions' }, { position: position, widgets: _.pluck(widgets, 'id') }, this.load);
	            },

	            getSelected: function() {
	                return this.widgets.filter(function(widgets) {
	                    return this.selected.indexOf(widgets.id.toString()) !== -1;
	                }.bind(this));
	            },

	            add: function(type) {
	                this.edit({ type: type.id });
	            },

	            edit: function(widget) {
	                var edit = _.extend({}, widget);

	                this.$set('editUrl', this.$url('admin/widgets/edit', (widget.id ? { id: widget.id } : { type: widget.type })));

	                this.modal.show();
	            },

	            cancel: function() {
	                if (this.modal) {
	                    this.modal.hide();
	                }

	                this.editUrl = null;

	                this.load();
	            }

	        },

	        components: {

	            'widget-list': __webpack_require__(2)

	        }

	    };

	    Site.register(module.exports);
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Site;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<ul class=\"uk-nestable uk-form\">\n\n        <li data-id=\"{{ widget.id }}\" v-repeat=\"widget: position.widgets\" class=\"uk-nestable-list-item\" v-show=\"widget | showWidget\">\n\n            <div class=\"uk-nestable-item pk-table-fake\" v-component=\"widget-item\"></div>\n\n        </li>\n\n    </ul>";
	module.exports = {

	        inherit: true,

	        ready: function() {
	            var self = this;

	            UIkit.nestable(this.$el, { maxDepth: 1, group: 'widgets' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
	                if (type !== 'removed' && e.target.tagName == 'UL') {
	                    self.reorder(self.position.id, nestable.list());
	                }
	            });
	        },

	        components: {

	            'widget-item': __webpack_require__(3)

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-nestable-item pk-table-fake\">\n\n        <div class=\"pk-table-width-minimum\">\n            <div class=\"uk-nestable-handle\">​</div>\n        </div>\n        <div class=\"pk-table-width-minimum\"><input type=\"checkbox\" name=\"id\" value=\"{{ widget.id }}\"></div>\n        <div class=\"pk-table-min-width-100\">\n            <a v-show=\"type\" v-on=\"click: edit(widget)\">{{ widget.title }}</a>\n            <span v-show=\"!type\">{{ widget.title }}</span>\n        </div>\n        <div class=\"pk-table-width-150\">\n            <div class=\"uk-form-select\" v-el=\"select\">\n                <a></a>\n                <select v-model=\"position.id\" class=\"uk-width-1-1\" options=\"positionOptions\" v-on=\"input: reassign\"></select>\n            </div>\n        </div>\n        <div class=\"pk-table-width-150\">{{ typeName }}</div>\n\n    </div>";
	module.exports = {

	        inherit: true,

	        ready: function() {
	            UIkit.formSelect(this.$$.select, { target: 'a' });
	        },

	        computed: {

	            type: function() {
	                return _.find(this.config.types, { id: this.widget.type });
	            },

	            typeName: function() {
	                return this.type ? this.type.name : this.$trans('Extension not loaded');
	            }

	        },

	        methods: {

	            reassign: function(e) {

	                e.preventDefault();
	                e.stopPropagation();

	                this.reorder(e.target.value, _.find(this.positions, {id : e.target.value }).widgets.concat(this.widget))

	            }

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);