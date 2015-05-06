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

	var __vue_template__ = "<div v-cloak=\"\">\n        <div class=\"pk-toolbar uk-form uk-clearfix\">\n            <div v-if=\"isWritable()\" class=\"uk-float-left\">\n\n                <button class=\"uk-button uk-button-primary uk-form-file\">\n                    {{ 'Upload' | trans }}\n                    <input type=\"file\" name=\"files[]\" multiple=\"multiple\">\n                </button>\n\n                <button class=\"uk-button\" v-on=\"click: createFolder()\">{{ 'Add Folder' | trans }}</button>\n\n                <button class=\"uk-button pk-button-danger\" v-show=\"selected.length\" v-on=\"click: remove\">{{ 'Delete' | trans }}</button>\n                <button class=\"uk-button\" v-show=\"selected.length === 1\" v-on=\"click: rename\">{{ 'Rename' | trans }}</button>\n\n            </div>\n            <div class=\"uk-float-right uk-hidden-small\">\n\n                <input type=\"text\" placeholder=\"{{ 'Search' | trans }}\" v-model=\"search\">\n\n                <div class=\"uk-button-group\">\n                    <button class=\"uk-button uk-icon-bars\" v-class=\"'uk-active': view == 'table'\" v-on=\"click: view = 'table'\"></button>\n                    <button class=\"uk-button uk-icon-th\" v-class=\"'uk-active': view == 'thumbnail'\" v-on=\"click: view = 'thumbnail'\"></button>\n                </div>\n\n            </div>\n        </div>\n\n        <ul class=\"uk-breadcrumb pk-breadcrumb\">\n            <li v-repeat=\"breadcrumbs\" v-class=\"'uk-active': current\">\n                <span v-show=\"current\">{{ title }}</span>\n                <a v-show=\"!current\" v-on=\"click: setPath(path)\">{{ title }}</a>\n            </li>\n        </ul>\n\n        <div v-show=\"upload.running\" class=\"uk-progress uk-progress-striped uk-active\">\n            <div class=\"uk-progress-bar\" v-style=\"width: upload.progress + '%'\">{{ upload.progress }}%</div>\n        </div>\n\n        <div v-partial=\"{{ view }}\"></div>\n\n        <div v-if=\"isWritable()\" class=\"uk-placeholder uk-text-center uk-text-muted\">\n            <img v-attr=\"src: $url('app/system/assets/images/finder-droparea.svg', true)\" width=\"22\" height=\"22\" alt=\"{{ 'Droparea' | trans }}\"> {{ 'Drop files here.' | trans }}\n        </div>\n\n    </div>";
	var $ = __webpack_require__(1);
	    var Vue = __webpack_require__(3);
	    var UIkit = __webpack_require__(2);

	    var defaults = {
	        root    : '/',
	        mode    : 'write',
	        view    : 'table',
	        path    : '/',
	        selected: [],
	        upload  : {}
	    };

	    var definition = {

	        replace : true,

	        template: __vue_template__,

	        data: function () {
	            return Vue.util.extend({}, defaults);
	        },

	        ready: function () {

	            this.resource = this.$resource('system/finder/:cmd');

	            this.load().success(function () {
	                this.$dispatch('ready.finder', this);
	            }.bind(this));
	        },

	        watch: {

	            path: function () {
	                this.load();
	            },

	            selected: function () {
	                this.$dispatch('select.finder', this.getSelected(), this)
	            }

	        },

	        filters: {

	            searched: function (files) {
	                var query = this.search;
	                return query ? files.filter(function (file) {
	                    return file.name.toLowerCase().indexOf(query.toLowerCase()) !== -1;
	                }) : files;
	            }

	        },

	        computed: {

	            breadcrumbs: function () {

	                var path = '',
	                    crumbs = [{ path: '/', title: this.$trans('Home') }]
	                        .concat(this.path.substr(1).split('/')
	                            .filter(function (str) {
	                                return str.length;
	                            })
	                            .map(function (part) {
	                                return { path: path += '/' + part, title: part };
	                            })
	                    );

	                crumbs[crumbs.length - 1].current = true;

	                return crumbs;
	            }

	        },

	        methods: {

	            /**
	             * API
	             */

	            setPath: function (path) {
	                this.$set('path', path);
	            },

	            getPath: function () {
	                return this.path;
	            },

	            getFullPath: function () {
	                return (this.root+this.path).replace(/^\/+|\/+$/g, '')+'/';
	            },

	            getSelected: function () {
	                var path = this.getFullPath();
	                return this.selected.map(function (name) {
	                    return path+name;
	                });
	            },

	            toggleSelect: function (name) {

	                if (name.targetVM) {
	                    if (name.target.tagName == 'INPUT' || name.target.tagName == 'A')  return;
	                    name = name.targetVM.$data.name;
	                }

	                var index  = this.selected.indexOf(name);
	                -1 === index ? this.selected.push(name) : this.selected.splice(index, 1);
	            },

	            createFolder: function () {
	                var name = prompt(this.$trans('Folder Name'), '');

	                if (!name) return;

	                this.command('createfolder', { name: name });
	            },

	            rename: function (oldname) {

	                if (oldname.target) {
	                    oldname = this.selected[0];
	                }

	                if (!oldname) return;

	                var newname = prompt(this.$trans('New Name'), oldname);

	                if (!newname) return;

	                this.command('rename', { oldname: oldname, newname: newname });
	            },

	            remove: function (names) {

	                if (names.target) {
	                    names = this.selected;
	                }

	                if (!names || !confirm(this.$trans('Are you sure?'))) return;

	                this.command('removefiles', { names: names });
	            },

	            /**
	             * Helper functions
	             */

	            encodeURI: function (url) {
	                return encodeURI(url).replace(/'/g, '%27');
	            },

	            isWritable: function () {
	                return this.mode === 'w' || this.mode === 'write';
	            },

	            isImage: function (url) {
	                return url.match(/\.(?:gif|jpe?g|png|svg)/i);
	            },

	            command: function (cmd, params) {

	                var self = this;

	                return this.resource.save({ cmd: cmd }, $.extend({ path: this.path, root: this.root }, params), function (data) {

	                    UIkit.notify(data.message, data.error ? 'danger' : '');

	                    self.load();

	                }).fail(function (jqXHR) {
	                    UIkit.notify(jqXHR.status == 500 ? 'Unknown error.' : jqXHR.responseText, 'danger');
	                });
	            },

	            load: function () {
	                return this.resource.get({ path: this.path, root: this.root }, function (data) {

	                    this.$set('selected', []);
	                    this.$set('folders', data.folders || []);
	                    this.$set('files', data.files || []);

	                    this.$dispatch('path.finder', this.getFullPath(), this);

	                }.bind(this));
	            }

	        },

	        events: {

	            /**
	             * Init upload
	             */

	            'hook:ready': function () {

	                var finder = this,
	                    settings = {

	                        action: this.$url('system/finder/upload'),

	                        before: function (options) {
	                            $.extend(options.params, { path: finder.path, root: finder.root });
	                        },

	                        loadstart: function () {
	                            finder.$set('upload.running', true);
	                            finder.$set('upload.progress', 0);
	                        },

	                        progress: function (percent) {
	                            finder.$set('upload.progress', Math.ceil(percent));
	                        },

	                        allcomplete: function (response) {

	                            var data = $.parseJSON(response);

	                            finder.load();

	                            UIkit.notify(data.message, data.error ? 'danger' : '');

	                            finder.$set('upload.progress', 100);
	                            setTimeout(function () {
	                                finder.$set('upload.running', false);
	                            }, 1500);
	                        }

	                    };

	                UIkit.uploadSelect(this.$el.querySelector('.uk-form-file > input'), settings);
	                UIkit.uploadDrop(this.$el, settings);
	            }

	        },

	        partials: {

	            'table': __webpack_require__(19),
	            'thumbnail': __webpack_require__(20)

	        }

	    };

	    Vue.component('v-finder', Vue.util.extend({}, definition));

	    var Finder = function (element, options) {
	        return new Vue($.extend(true, {}, definition, { el: element, data: $.extend(true, {}, defaults, options)} ));
	    };

	    $(function () {
	        $('[data-finder]').each(function () {
	            new Finder(this, $(this).data('finder'));
	        });
	    });

	    window.Finder = window.Finder || Finder;
	;(typeof module.exports === "function"? module.exports.options: module.exports).template = __vue_template__;


/***/ },

/***/ 1:
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },

/***/ 2:
/***/ function(module, exports, __webpack_require__) {

	module.exports = UIkit;

/***/ },

/***/ 3:
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue;

/***/ },

/***/ 19:
/***/ function(module, exports, __webpack_require__) {

	module.exports = "<div v-if=\"files || folders\" class=\"uk-overflow-container\">\n    <table class=\"uk-table uk-table-hover uk-table-middle pk-finder-table\">\n        <thead>\n            <th class=\"pk-table-width-minimum\"><input type=\"checkbox\" v-check-all=\"selected: input[name=name]\"></th>\n            <th colspan=\"2\">{{ 'Name' | trans }}</th>\n            <th class=\"pk-table-width-minimum uk-text-center\">{{ 'Size' | trans }}</th>\n            <th class=\"pk-table-width-minimum\">{{ 'Modified' | trans }}</th>\n        </thead>\n        <tbody>\n\n            <tr v-repeat=\"folders | searched\" class=\"uk-visible-hover\" v-on=\"click: toggleSelect\">\n                <td><input type=\"checkbox\" name=\"name\" value=\"{{ name }}\"></td>\n                <td class=\"pk-table-width-minimum\">\n                    <i class=\"uk-icon-folder-o pk-finder-icon-folder\"></i>\n                </td>\n                <td class=\"pk-table-text-break pk-table-min-width-200\"><a v-on=\"click: setPath(path)\">{{ name }}</a></td>\n                <td></td>\n                <td></td>\n            </tr>\n\n            <tr v-repeat=\"files | searched\" class=\"uk-visible-hover\" v-on=\"click: toggleSelect\">\n                <td><input type=\"checkbox\" name=\"name\" value=\"{{ name }}\"></td>\n                <td class=\"pk-table-width-minimum\">\n                    <i v-if=\"isImage(url)\" class=\"pk-thumbnail-icon pk-finder-icon-file\" style=\"background-image: url('{{ encodeURI(url) }}');\"></i>\n                    <i v-if=\"!isImage(url)\" class=\"uk-icon-file-o pk-finder-icon-file\"></i>\n                </td>\n                <td class=\"pk-table-text-break pk-table-min-width-200\">{{ name }}</td>\n                <td class=\"uk-text-right uk-text-nowrap\">{{ size }}</td>\n                <td class=\"uk-text-nowrap\">{{ lastmodified | date medium }}</td>\n            </tr>\n\n        </tbody>\n    </table>\n</div>\n";

/***/ },

/***/ 20:
/***/ function(module, exports, __webpack_require__) {

	module.exports = "<ul v-if=\"files || folders\" class=\"uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-4 pk-thumbnail-border-remove\" data-uk-grid-margin data-uk-grid-match=\"{ target:'.uk-panel' }\">\n\n    <li v-repeat=\"folders | searched\" v-on=\"click: toggleSelect\">\n        <div class=\"uk-panel uk-panel-box uk-text-center uk-visible-hover\">\n            <div class=\"uk-panel-teaser\">\n                <div class=\"pk-thumbnail pk-thumbnail-folder\"></div>\n            </div>\n            <div class=\"uk-text-truncate\">\n                <input type=\"checkbox\" value=\"{{ name }}\" v-checkbox=\"selected\">\n                <a v-on=\"click: setPath(path, $event)\">{{ name }}</a>\n            </div>\n        </div>\n    </li>\n\n    <li v-repeat=\"files | searched\" v-on=\"click: toggleSelect\">\n        <div class=\"uk-panel uk-panel-box uk-text-center uk-visible-hover\">\n            <div class=\"uk-panel-teaser\">\n                <div v-if=\"isImage(url)\" class=\"pk-thumbnail\" style=\"background-image: url('{{ encodeURI(url) }}');\"></div>\n                <div v-if=\"!isImage(url)\" class=\"pk-thumbnail pk-thumbnail-file\"></div>\n            </div>\n            <div class=\"uk-text-nowrap uk-text-truncate\">\n                <input type=\"checkbox\" value=\"{{ name }}\" v-checkbox=\"selected\">\n                {{ name }}\n            </div>\n        </div>\n    </li>\n\n</ul>\n";

/***/ }

/******/ });