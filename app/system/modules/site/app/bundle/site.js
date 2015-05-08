var Site =
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
	var UIkit = __webpack_require__(3);

	Vue.validators['unique'] = function(value) {
	    var menu = _.find(this.menus, { id: value });
	    return !menu || this.menu.oldId == menu.id;
	};

	Vue.http.options = _.extend({}, Vue.http.options, { error: function (msg) {
	    UIkit.notify(msg, 'danger');
	}});

	var Site = Vue.extend({

	    sections: [],

	    mixins: [__webpack_require__(4)],

	    data: function() {
	        return _.merge({ selected: null }, window.$data);
	    },

	    events: {

	        loaded: 'select'

	    },

	    methods: {

	        select: function(node) {

	            if (!node) {
	                node = this.selected && _.find(this.nodes, { id: this.selected.id }) || this.selectFirst();
	            }

	            this.$set('selected', node);
	        },

	        selectFirst: function() {
	            var self = this, first = null;
	            this.menus.some(function (menu) {
	                return first = _.first(self.tree[menu.id]);
	            });

	            return first ? first.node : undefined;
	        }

	    },

	    components: {
	        'menu-list': __webpack_require__(5),
	        'node-edit': __webpack_require__(6)
	    }

	});

	Site.register = function (options) {
	    this.component(options.name, options);
	    this.options.sections.push(options);
	};

	Site.register(__webpack_require__(7));

	$(function () {

	    var site = new Site();
	    site.$mount('#site');

	});

	module.exports = Site;


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = _;

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = UIkit;

/***/ },
/* 4 */
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
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<div class=\"uk-margin\" v-repeat=\"menu: menus\">\n        <div class=\"uk-flex\">\n            <span class=\"uk-panel-title uk-flex-item-1\" v-on=\"click: edit(menu)\">{{ menu.label }}</span>\n\n            <div class=\"uk-button-dropdown\" data-uk-dropdown=\"{ mode: 'click' }\">\n                <a v-on=\"click: $event.preventDefault()\"><i class=\"uk-icon uk-icon-plus\"></i></a>\n                <div class=\"uk-dropdown uk-dropdown-small\">\n                    <ul class=\"uk-nav uk-nav-dropdown\">\n                        <li v-repeat=\"type: types | unmounted\"><a v-on=\"click: add(menu, type)\">{{ type.label }}</a></li>\n                    </ul>\n                </div>\n            </div>\n        </div>\n\n        <node-list></node-list>\n\n    </div>\n\n    <p>\n        <a v-on=\"click: edit()\"><i class=\"uk-icon-th-list\"></i> {{ 'Create Menu' | trans }}</a>\n    </p>\n\n    <div class=\"uk-modal\" v-el=\"modal\">\n\n        <div class=\"uk-modal-dialog\" v-if=\"menu\">\n\n            <form name=\"menuform\" v-on=\"valid: save\">\n\n                <p>\n                    <input class=\"uk-width-1-1 uk-form-large\" name=\"label\" type=\"text\" placeholder=\"{{ 'Enter Menu Name' | trans }}\" v-model=\"menu.label\" v-valid=\"alphaNum\">\n                    <span class=\"uk-form-help-block uk-text-danger\" v-show=\"menuform.label.invalid\">{{ 'Invalid name.' | trans }}</span>\n                </p>\n                <p>\n                    <input class=\"uk-width-1-1 uk-form-large\" name=\"id\" type=\"text\" placeholder=\"{{ 'Enter Menu Slug' | trans }}\" v-model=\"menu.id\" v-valid=\"alphaNum, unique\">\n                    <span class=\"uk-form-help-block uk-text-danger\" v-show=\"menuform.id.invalid\">{{ 'Invalid slug.' | trans }}</span>\n                </p>\n\n                <button class=\"uk-button uk-button-primary\" v-attr=\"disabled: menuform.invalid\">{{ 'Save' | trans }}</button>\n                <button class=\"uk-button uk-modal-close\" v-on=\"click: cancel\">{{ 'Cancel' | trans }}</button>\n                <button class=\"uk-button uk-button-danger uk-float-right\" v-show=\"menu.oldId\" v-on=\"click: delete\">{{ 'Delete' | trans }}</button>\n\n            </form>\n        </div>\n\n    </div>";
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

	            'node-list': __webpack_require__(8)

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<form class=\"uk-form uk-form-horizontal\" name=\"form\" v-show=\"node.type\" v-on=\"valid: save\">\n\n        <div class=\"uk-clearfix uk-margin\">\n\n            <div class=\"uk-float-left\">\n\n                <h2 class=\"uk-h2\" v-if=\"node.id\">{{ node.title }} ({{ type.label }})</h2>\n                <h2 class=\"uk-h2\" v-if=\"!node.id\">{{ 'Add %type%' | trans {type:type.label} }}</h2>\n\n            </div>\n\n            <div class=\"uk-float-right\">\n\n                <a class=\"uk-button\" v-on=\"click: cancel()\">{{ 'Cancel' | trans }}</a>\n                <button class=\"uk-button uk-button-primary\" type=\"submit\" v-attr=\"disabled: form.invalid\">{{ 'Save' | trans }}</button>\n\n            </div>\n\n        </div>\n\n        <ul class=\"uk-tab\" v-el=\"tab\">\n            <li v-repeat=\"section: sections | active | orderBy 'priority'\"><a>{{ section.label | trans }}</a></li>\n        </ul>\n\n        <div class=\"uk-switcher uk-margin\" v-el=\"content\">\n            <div v-repeat=\"section: sections | active | orderBy 'priority'\">\n                <div v-component=\"{{ section.name }}\" v-with=\"node: node\"></div>\n            </div>\n        </div>\n\n    </form>";
	var _ = __webpack_require__(2);
	    var UIkit = __webpack_require__(3);

	    module.exports = {

	        inherit: true,

	        data: function() {
	            return { node: {} }
	        },

	        ready: function() {
	            this.tab = UIkit.tab(this.$$.tab, { connect: this.$$.content });
	        },

	        watch: {

	            selected: function(node) {
	                this.$set('node', _.extend({}, node));
	                this.tab.switcher.show(0);
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

	            type: function() {
	                return _.find(this.types, { id: this.node.type }) || {};
	            },

	            path: function() {
	                return (this.node.path ? this.node.path.split('/').slice(0, -1).join('/') : '') + '/' + (this.node.slug || '');
	            },

	            isFrontpage: function() {
	                return this.node.id === this.frontpage;
	            },

	            sections: function() {
	                return this.$root.$options.sections
	            }

	        },

	        methods: {

	            save: function (e) {

	                e.preventDefault();

	                var self = this, data = { node: this.node };

	                this.$broadcast('save', data);

	                this.Nodes.save({ id: this.node.id }, data, function(node) {

	                    self.selected.id = parseInt(node.id);
	                    self.load();

	                    if (data.frontpage) {
	                        self.$set('frontpage', node.id);
	                    }
	                });
	            },

	            cancel: function() {
	                this.load()
	            }

	        },

	        partials: {

	            'settings': __webpack_require__(9)

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "{{&gt; settings}}\n\n    <div class=\"uk-form-row\">\n        <label for=\"form-alias-url\" class=\"uk-form-label\">{{ 'Url' | trans }}</label>\n\n        <div class=\"uk-form-controls\">\n            <input id=\"form-alias-url\" class=\"uk-form-width-large\" type=\"text\" v-model=\"node.data.url\">\n        </div>\n    </div>";
	module.exports = {

	        name: 'alias',
	        label: 'Settings',
	        priority: 0,
	        active: 'alias'

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<ul class=\"uk-nestable\">\n        <node-item v-repeat=\"item: tree[menu.id]\"></node-item>\n    </ul>";
	module.exports = {

	        inherit: true,
	        replace: true,

	        ready: function () {

	            var self = this;

	            UIkit.nestable(this.$el, { maxDepth: 20, group: 'site.nodes' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
	                if (type !== 'removed') {
	                    self.Nodes.save({ id: 'updateOrder' }, { menu: self.menu.id, nodes: nestable.list() }, self.load);
	                }
	            });

	        },

	        components: {

	            'node-item': __webpack_require__(10)

	        }
	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = "<div class=\"uk-form-row\">\n    <label for=\"form-title\" class=\"uk-form-label\">{{ 'Title' | trans }}</label>\n    <div class=\"uk-form-controls\">\n        <input id=\"form-title\" class=\"uk-form-width-large\" type=\"text\" name=\"node[title]\" v-model=\"node.title\" v-valid=\"alphaNum\">\n        <span class=\"uk-form-help-block uk-text-danger\" v-show=\"form['node[title]'].invalid\">{{ 'Invalid name.' | trans }}</span>\n    </div>\n</div>\n\n<div class=\"uk-form-row\">\n    <label for=\"form-slug\" class=\"uk-form-label\">{{ 'Slug' | trans }}</label>\n    <div class=\"uk-form-controls\">\n        <span>{{ path }}</span><br>\n        <input id=\"form-slug\" class=\"uk-form-width-large\" type=\"text\" name=\"node[slug]\" v-model=\"node.slug\" v-valid=\"alphaNum\">\n        <span class=\"uk-form-help-block uk-text-danger\" v-show=\"form['node[slug]'].invalid\">{{ 'Invalid slug.' | trans }}</span>\n    </div>\n</div>\n\n<div class=\"uk-form-row\">\n    <label for=\"form-status\" class=\"uk-form-label\">{{ 'Status' | trans }}</label>\n    <div class=\"uk-form-controls\">\n        <select id=\"form-status\" class=\"uk-form-width-large\" v-model=\"node.status\">\n            <option value=\"0\">{{ 'Disabled' | trans }}</option>\n            <option value=\"1\">{{ 'Enabled' | trans }}</option>\n        </select>\n    </div>\n</div>\n\n<div class=\"uk-form-row\" v-if=\"type.url\">\n    <span class=\"uk-form-label\">{{ 'Options' | trans }}</span>\n    <div class=\"uk-form-controls\">\n        <label><input type=\"checkbox\" name=\"frontpage\" v-model=\"isFrontpage\"> {{ 'Frontpage' | trans }}</label>\n    </div>\n</div>\n";

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var __vue_template__ = "<li class=\"uk-nestable-list-item\" v-class=\"uk-parent: isParent, uk-active: isActive\" data-id=\"{{ node.id }}\">\n\n        <div class=\"uk-nestable-item uk-visible-hover-inline\" v-on=\"click: select(node)\">\n            <div class=\"uk-nestable-handle\"></div>\n            <div data-nestable-action=\"toggle\"></div>\n            {{ node.title }}\n\n            <i class=\"uk-float-right uk-icon-home\" title=\"{{ 'Frontpage' | trans }}\" v-show=\"isFrontpage\"></i>\n            <a class=\"uk-hidden uk-float-right\" title=\"{{ 'Delete' | trans }}\" v-on=\"click: delete\"><i class=\"uk-icon-minus-circle\"></i></a>\n        </div>\n\n        <ul class=\"uk-nestable-list\" v-if=\"isParent\">\n            <node-item v-repeat=\"item: item.children\"></node-item>\n        </ul>\n\n    </li>";
	module.exports = {

	        inherit: true,
	        replace: true,

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

	                this.Nodes.delete({ id: this.node.id }, this.load);
	            }

	        }

	    }
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ }
/******/ ]);