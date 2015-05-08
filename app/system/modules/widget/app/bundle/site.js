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

	var Site = __webpack_require__(1);
	var Widgets = __webpack_require__(2);

	Site.register(Widgets.extend({

	    name: 'widgets',
	    label: 'Widgets',
	    priority: 20

	}).options);


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Site;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-margin uk-flex uk-flex-space-between uk-flex-wrap\" data-uk-margin=\"\">\n        <div data-uk-margin=\"\">\n\n            <div class=\"uk-button-dropdown\" data-uk-dropdown=\"{ mode: 'click' }\">\n                <button class=\"uk-button uk-button-primary\" type=\"button\">{{ 'Add Widget' | trans }}</button>\n                <div class=\"uk-dropdown uk-dropdown-small\">\n                    <ul class=\"uk-nav uk-nav-dropdown\">\n                        <li v-repeat=\"type: config.types\"><a v-on=\"click: add(type)\">{{ type.name }}</a></li>\n                    </ul>\n                </div>\n            </div>\n\n            <a class=\"uk-button pk-button-danger\" v-show=\"selected.length\" v-on=\"click: remove\">{{ 'Delete' | trans }}</a>\n\n            <div class=\"uk-button-dropdown\" v-show=\"selected.length\" data-uk-dropdown=\"{ mode: 'click' }\">\n                <button class=\"uk-button\" type=\"button\">{{ 'More' | trans }} <i class=\"uk-icon-caret-down\"></i></button>\n                <div class=\"uk-dropdown uk-dropdown-small\">\n                    <ul class=\"uk-nav uk-nav-dropdown\">\n                        <li><a v-on=\"click: copy\">{{ 'Copy' | trans }}</a></li>\n                    </ul>\n                </div>\n            </div>\n\n        </div>\n        <div data-uk-margin=\"\">\n            <input type=\"text\" v-model=\"search\" placeholder=\"{{ 'Search' | trans }}\" v-on=\"keypress: $event.preventDefault() | key enter\" debounce=\"200\">\n        </div>\n    </div>\n\n    <div class=\"uk-overflow-container\">\n\n        <div class=\"pk-table-fake pk-table-fake-header pk-table-fake-header-indent pk-table-fake-border\">\n            <div class=\"pk-table-width-minimum\"><input type=\"checkbox\" v-check-all=\"selected: input[name=id]\"></div>\n            <div class=\"pk-table-min-width-100\">{{ 'Title' | trans }}</div>\n            <div class=\"pk-table-width-150\">{{ 'Position' | trans }}</div>\n            <div class=\"pk-table-width-150\">{{ 'Type' | trans }}</div>\n        </div>\n\n        <div v-repeat=\"position: positions\" v-show=\"position | hasWidgets\">\n\n            <div class=\"pk-table-fake pk-table-fake-header pk-table-fake-subheading\">\n                <div>\n                    {{ position.name | trans }}\n                    <span v-if=\"position.description\" class=\"uk-text-muted\">{{ position.description | trans }}</span>\n                </div>\n            </div>\n\n            <widget-list></widget-list>\n\n        </div>\n\n    </div>";
	var $ = __webpack_require__(3);

	    module.exports = Vue.extend({

	        template: __vue_template__,

	        data: function() {

	            return {
	                search: '',
	                positions: [],
	                config: window.$widgets
	            };

	        },

	        created: function() {

	            this.$addChild(__webpack_require__(4));

	            this.Widgets = this.$resource('api/widget/:id');
	            this.load();
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

	                this.Widgets.query({ id: 'config' }, function (data) {
	                    self.$set('config.configs', data);
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
	                this.$set('widget', _.extend({}, widget));
	            }

	        },

	        components: {

	            'widget-list': __webpack_require__(5)

	        }

	    });
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-modal\" v-el=\"modal\">\n\n        <div class=\"uk-modal-dialog uk-modal-dialog-large\">\n\n            <form class=\"uk-form uk-container uk-container-center\" name=\"widgetform\" v-if=\"widget\" v-on=\"valid: save\">\n\n                <div class=\"uk-clearfix uk-margin\" data-uk-margin=\"\">\n\n                    <div class=\"uk-float-left\">\n\n                        <h2 class=\"uk-h2\" v-if=\"widget.id\">{{ widget.title }} ({{ typeName }})</h2>\n                        <h2 class=\"uk-h2\" v-if=\"!widget.id\">{{ 'Add %type%' | trans {type:typeName} }}</h2>\n\n                    </div>\n\n                    <div class=\"uk-float-right\">\n\n                        <a class=\"uk-button\" v-on=\"click: cancel()\">{{ 'Cancel' | trans }}</a>\n                        <button class=\"uk-button uk-button-primary\" type=\"submit\">{{ 'Save' | trans }}</button>\n\n                    </div>\n\n                </div>\n\n                <widget-edit v-with=\"widget: widget, config: widgetConfig, position: position, form: widgetform\" v-ref=\"edit\"></widget-edit>\n\n            </form>\n\n        </div>\n    </div>";
	module.exports = {

	        inherit: true,

	        created: function () {
	            var container = document.createElement('div');
	            document.body.appendChild(container);
	            this.$mount(container);
	        },

	        ready: function() {
	            this.modal = UIkit.modal(this.$$.modal);
	            this.modal.on('hide.uk.modal', this.cancel);
	        },

	        watch: {

	            widget: function (widget) {
	                this.modal[widget ? 'show' : 'hide']();
	            }

	        },

	        events: {

	            saved: function() {

	                this.load();
	                this.cancel();

	            }

	        },

	        computed: {

	            type: function() {
	                return _.find(this.config.types, { id: this.widget.type });
	            },

	            typeName: function() {
	                return this.type ? this.type.name : this.$trans('Extension not loaded');
	            },

	            position: function() {

	                var id = this.widget.id;

	                var position = _.find(this.positions, function(position) {
	                    return _.find(position.widgets, { id: id });
	                });

	                return position && position.id;
	            },

	            widgetConfig: function() {
	                return this.config.configs[this.widget.id] || {};
	            }

	        },

	        methods: {

	            save: function (e) {
	                this.$.edit.save(e);
	            },

	            cancel: function() {
	                this.$set('widget', null);
	            }

	        },

	        components: {

	            'widget-edit': __webpack_require__(6)

	        }
	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<ul class=\"uk-nestable uk-form\">\n\n        <li data-id=\"{{ widget.id }}\" v-repeat=\"widget: position.widgets\" class=\"uk-nestable-list-item\" v-show=\"widget | showWidget\">\n\n            <widget-item></widget-item>\n\n        </li>\n\n    </ul>";
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

	            'widget-item': __webpack_require__(7)

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-grid\" data-uk-grid-margin=\"\">\n\n        <div class=\"uk-width-medium-3-4 uk-form-horizontal\">\n\n            <ul class=\"uk-tab\" v-el=\"tab\">\n                <li v-repeat=\"section: sections | active | orderBy 'priority'\"><a>{{ section.label | trans }}</a></li>\n            </ul>\n\n            <div class=\"uk-switcher uk-margin\" v-el=\"content\">\n                <div v-repeat=\"section: sections | active | orderBy 'priority'\">\n                    <div v-component=\"{{ section.name }}\" v-with=\"widget: widget, form: form, config: config\"></div>\n                </div>\n            </div>\n\n        </div>\n\n        <div class=\"uk-width-medium-1-4\">\n\n            <div class=\"uk-panel uk-panel-divider uk-form-stacked\">\n\n                <div class=\"uk-form-row\">\n                    <label for=\"form-position\" class=\"uk-form-label\">{{ 'Position' | trans }}</label>\n                    <div class=\"uk-form-controls\">\n                        <select id=\"form-position\" name=\"position\" v-model=\"position\" class=\"uk-width-1-1\" options=\"positionOptions\"></select>\n                    </div>\n                </div>\n\n                <div class=\"uk-form-row\">\n                    <span class=\"uk-form-label\">{{ 'Restrict Access' | trans }}</span>\n                    <div class=\"uk-form-controls uk-form-controls-text\">\n                        <p v-repeat=\"role: roles\" class=\"uk-form-controls-condensed\">\n                            <label><input type=\"checkbox\" value=\"{{ role.id }}\" v-checkbox=\"widget.roles\"> {{ role.name }}</label>\n                        </p>\n                    </div>\n                </div>\n\n                <div class=\"uk-form-row\">\n                    <span class=\"uk-form-label\">{{ 'Options' | trans }}</span>\n                    <div class=\"uk-form-controls\">\n                        <label><input type=\"checkbox\" v-model=\"widget.settings.show_title\"> {{ 'Show Title' | trans }}</label>\n                    </div>\n                </div>\n\n            </div>\n\n        </div>\n\n    </div>";
	var Widgets = __webpack_require__(8);

	    Widgets.addSection(__webpack_require__(9));
	    Widgets.addSection(__webpack_require__(10));

	    module.exports = {

	        sections: [],

	        data: function() {
	            return _.merge({}, window.$widgets);
	        },

	        created: function () {
	            var self = this;

	            Widgets.sections.forEach(function(options) {
	                self.$options.components[options.name] = Vue.extend(options);
	            });

	            this.Widgets = this.$resource('api/widget/:id');
	        },

	        ready: function() {
	            UIkit.tab(this.$$.tab, { connect: this.$$.content });
	        },

	        watch: {

	            widget: function(widget) {

	                if (widget) {
	                    this.$set('widget.settings', _.defaults({}, widget.settings, this.type.defaults));
	                }

	            }

	        },

	        filters: {

	            active: function(sections) {

	                var type = this.$get('type.id');

	                return sections.filter(function(section) {
	                    return !section.active || type && type.match(section.active);
	                });
	            }

	        },

	        computed: {

	            positionOptions: function() {
	                return [{ text: this.$trans('- Assign -'), value: '' }].concat(
	                    _.map(this.positions, function(position) {
	                        return { text: this.$trans(position.name), value: position.id };
	                    }.bind(this))
	                );
	            },

	            type: function() {
	                return _.find(this.types, { id: this.widget.type });
	            },

	            typeName: function() {
	                return this.type ? this.type.name : this.$trans('Extension not loaded');
	            },

	            sections: function() {
	                return Widgets.sections;
	            }

	        },

	        methods: {

	            save: function (e) {

	                e.preventDefault();

	                var self = this, data = { widget: this.widget, config: this.config, position: this.position };

	                this.$broadcast('save', data);

	                this.Widgets.save({ id: this.widget.id }, data, function() {
	                    self.$dispatch('saved', data);
	                });

	            }

	        },

	        partials: {

	            settings: __webpack_require__(11)

	        }

	    };
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 7 */
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


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Widgets;

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "{{&gt; settings}}\n\n    <div class=\"uk-form-row\">\n        <div class=\"uk-form-controls\">\n\n            <!-- TODO: integrate editor-->\n            <textarea autocomplete=\"off\" style=\"visibility:hidden; height:543px\" data-finder-options=\"{root:'\/storage'}\" v-model=\"widget.settings.content\" v-el=\"editor\"></textarea>\n\n            <p class=\"uk-form-controls-condensed\">\n                <label><input type=\"checkbox\" name=\"widget[settings][markdown]\" v-model=\"widget.settings.markdown\"> {{ 'Enable Markdown' | trans }}</label>\n            </p>\n        </div>\n    </div>";
	module.exports = {

	        name: 'site-text',
	        label: 'Settings',
	        active: 'site.text',
	        priority: 0,
	        template: __vue_template__,

	        ready: function() {
	            this.editor = UIkit.htmleditor(this.$$.editor, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, { markdown: this.$get('widget.settings.markdown') }));
	        },

	        watch: {

	            'widget.settings.markdown': function (markdown) {
	                this.editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
	            }

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div v-show=\"tree[menu.id].length\" v-repeat=\"menu: menus\" class=\"uk-form-row\">\n        <label for=\"form-h-it\" class=\"uk-form-label\">{{ menu.label }} {{ 'Menu' | trans }}</label>\n        <div class=\"uk-form-controls uk-form-controls-text\">\n\n            <ul class=\"uk-list uk-margin-top-remove\">\n                <li v-partial=\"#node-item\" v-repeat=\"item: tree[menu.id]\"></li>\n            </ul>\n        </div>\n    </div>\n\n    <script id=\"node-item\" type=\"text/template\">\n\n        <label>\n            <input type=\"checkbox\" value=\"{{ item.node.id }}\" v-checkbox=\"widget.nodes\">\n            {{ item.node.title }}\n        </label>\n\n        <ul v-if=\"item.children.length\" class=\"uk-list\">\n            <li v-partial=\"#node-item\" v-repeat=\"item: item.children\"></li>\n        </ul>\n\n    </script>";
	module.exports = {

	        name: 'assignment',
	        label: 'Assignment',
	        priority: 100,
	        template: __vue_template__,

	        mixins: [__webpack_require__(12)]

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = "<div class=\"uk-form-row\">\n    <label for=\"form-title\" class=\"uk-form-label\">{{ 'Title' | trans }}</label>\n    <div class=\"uk-form-controls\">\n        <input id=\"form-title\" class=\"uk-form-width-large\" type=\"text\" name=\"title\" v-model=\"widget.title\" v-valid=\"required\">\n        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"form.title.invalid\">{{ 'Title cannot be blank.' | trans }}</p>\n    </div>\n</div>\n";

/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	var $ = __webpack_require__(3);
	var _ = __webpack_require__(13);

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
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = _;

/***/ }
/******/ ]);