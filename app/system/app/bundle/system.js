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

	__webpack_require__(9);
	__webpack_require__(10);
	__webpack_require__(11);

	function install (Vue) {

	    var config = window.$pagekit;

	    /**
	     * Config
	     */

	    Vue.options.url.root = config.url;
	    Vue.options.http.emulateHTTP = true;
	    Vue.options.http.headers = {'X-XSRF-TOKEN': config.csrf, 'X-Requested-With': 'XMLHttpRequest'};

	    /**
	     * Methods
	     */

	    Vue.url.static = function(url, params) {

	        var options = url;

	        if (!_.isPlainObject(options)) {
	            options = {url: url, params: params};
	        }

	        Vue.util.extend(options, {
	            root: Vue.options.url.root.replace(/\/index.php$/i, '')
	        });

	        return Vue.url(options);
	    };

	    var formats = ['full', 'long', 'medium', 'short'];

	    Vue.prototype.$date = function(date, format) {

	        var options = format;

	        if (typeof date == 'string') {
	            date = new Date(date);
	        }

	        if (typeof options == 'string') {
	            if (formats.indexOf(format) != -1) {
	                options = {date: format};
	            } else {
	                options = {skeleton: format};
	            }
	        }

	        return Globalize.formatDate(date, options);
	    };

	    Vue.prototype.$trans = Globalize.trans;
	    Vue.prototype.$transChoice = Globalize.transChoice;

	    var partial = Vue.directive('partial'), insert = partial.insert;

	    partial.insert = function(id) {

	        var partial = this.vm.$options.partials[id];

	        if (undefined === id || partial) {
	            return insert.call(this, id);
	        }

	        var frag = Vue.parsers.template.parse(id);

	        if (frag) {
	            this.vm.$options.partials[id] = frag;
	            return insert.call(this, id);
	        }
	    };

	}

	if (window.Vue) {
	    Vue.use(install);
	}

	/**
	 * Copyright (c) 2013 Kevin van Zonneveld (http://kvz.io) and Contributors (http://phpjs.org/authors)
	 */

	String.prototype.parse = function (array) {

	    var strArr = this.replace(/^&/, '').replace(/&$/, '').split('&'),
	        sal = strArr.length,
	        i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
	        postLeftBracketPos, keys, keysLen,
	        fixStr = function(str) {
	            return decodeURIComponent(str.replace(/\+/g, '%20'));
	        };

	    if (!array) {
	        array = {};
	    }

	    for (i = 0; i < sal; i++) {
	        tmp = strArr[i].split('=');
	        key = fixStr(tmp[0]);
	        value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

	        while (key.charAt(0) === ' ') {
	            key = key.slice(1);
	        }
	        if (key.indexOf('\x00') > -1) {
	            key = key.slice(0, key.indexOf('\x00'));
	        }
	        if (key && key.charAt(0) !== '[') {
	            keys = [];
	            postLeftBracketPos = 0;
	            for (j = 0; j < key.length; j++) {
	                if (key.charAt(j) === '[' && !postLeftBracketPos) {
	                    postLeftBracketPos = j + 1;
	                }
	                else if (key.charAt(j) === ']') {
	                    if (postLeftBracketPos) {
	                        if (!keys.length) {
	                            keys.push(key.slice(0, postLeftBracketPos - 1));
	                        }
	                        keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
	                        postLeftBracketPos = 0;
	                        if (key.charAt(j + 1) !== '[') {
	                            break;
	                        }
	                    }
	                }
	            }
	            if (!keys.length) {
	                keys = [key];
	            }
	            for (j = 0; j < keys[0].length; j++) {
	                chr = keys[0].charAt(j);
	                if (chr === ' ' || chr === '.' || chr === '[') {
	                    keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
	                }
	                if (chr === '[') {
	                    break;
	                }
	            }

	            obj = array;
	            for (j = 0, keysLen = keys.length; j < keysLen; j++) {
	                key = keys[j].replace(/^['"]/, '').replace(/['"]$/, '');
	                lastIter = j !== keys.length - 1;
	                lastObj = obj;
	                if ((key !== '' && key !== ' ') || j === 0) {
	                    if (obj[key] === undef) {
	                        obj[key] = {};
	                    }
	                    obj = obj[key];
	                }
	                else { // To insert new dimension
	                    ct = -1;
	                    for (p in obj) {
	                        if (obj.hasOwnProperty(p)) {
	                            if (+p > ct && p.match(/^\d+$/g)) {
	                                ct = +p;
	                            }
	                        }
	                    }
	                    key = ct + 1;
	                }
	            }
	            lastObj[key] = value;
	        }
	    }

	    return array;
	};


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 2 */,
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue;

/***/ },
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Vue Directives
	 */

	var $ = __webpack_require__(1);
	var Vue = __webpack_require__(3);

	Vue.directive('gravatar', {

	    update: function(value) {

	        var el = $(this.el), options = { size: (el.attr('height') || 50) * 2, backup: 'mm', rating: 'g' };

	        el.attr('src', gravatar(value || '', options));
	    }

	});

	Vue.directive('check-all', {

	    isLiteral: true,

	    bind: function() {

	        var self = this, vm = this.vm, el = $(this.el), keypath = this.arg, selector = this.expression;

	        el.on('change.check-all', function() {
	            $(selector, vm.$el).prop('checked', $(this).prop('checked'));
	            vm.$set(keypath, self.checked());
	        });

	        $(vm.$el).on('change.check-all', selector, function() {
	            vm.$set(keypath, self.state());
	        });

	        this.unbindWatcher = vm.$watch(keypath, function(selected) {

	            $(selector, vm.$el).prop('checked', function() {
	                return selected.indexOf($(this).val()) !== -1;
	            });

	            self.state();
	        });

	    },

	    unbind: function() {

	        $(this.el).off('.check-all');
	        $(this.vm.$el).off('.check-all');

	        if (this.unbindWatcher) {
	            this.unbindWatcher();
	        }
	    },

	    state: function() {

	        var el = $(this.el), checked = this.checked();

	        if (checked.length === 0) {
	            el.prop('checked', false).prop('indeterminate', false);
	        } else if (checked.length == $(this.expression, this.vm.$el).length) {
	            el.prop('checked', true).prop('indeterminate', false);
	        } else {
	            el.prop('indeterminate', true);
	        }

	        return checked;
	    },

	    checked: function() {

	        var checked = [];

	        $(this.expression, this.vm.$el).each(function() {
	            if ($(this).prop('checked')) {
	                checked.push($(this).val());
	            }
	        });

	        return checked;
	    }

	});

	Vue.directive('checkbox', {

	    twoWay: true,

	    bind: function() {

	        var vm = this.vm, expression = this.expression, el = $(this.el);

	        el.on('change.checkbox', function() {

	            var model = vm.$get(expression), contains = model.indexOf(el.val());

	            if (el.prop('checked')) {
	                if (-1 === contains) {
	                    model.push(el.val());
	                }
	            } else if (-1 !== contains) {
	                model.splice(contains, 1);
	            }
	        });

	    },

	    update: function(value) {

	        if (undefined === value) {
	            this.set([]);
	            return;
	        }

	        $(this.el).prop('checked', -1 !== value.indexOf(this.el.value));
	    },

	    unbind: function() {
	        $(this.el).off('.checkbox');
	    }

	});


/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Vue Filters
	 */

	var $ = __webpack_require__(1);
	var _ = __webpack_require__(14);
	var Vue = __webpack_require__(3);

	Vue.filter('baseUrl', function(url) {
	    return _.startsWith(url, Vue.url.root) ? url.substr(Vue.url.root.length) : url;
	});

	Vue.filter('trans', function(id, parameters, domain, locale) {
	    return this.$trans(id, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
	});

	Vue.filter('transChoice', function(id, number, parameters, domain, locale) {
	    return this.$transChoice(id, evalExp.call(this, number) || 0, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
	});

	Vue.filter('date', function(date, format) {
	    return this.$date(date, format);
	});

	Vue.filter('first', function(collection) {
	    return Vue.filter('toArray')(collection)[0];
	});

	Vue.filter('length', function(collection) {
	    return Vue.filter('toArray')(collection).length;
	});

	Vue.filter('toArray', function(collection) {

	    if (_.isPlainObject(collection)) {
	        return Object.keys(collection).map(function(key) {
	            return collection[key];
	        });
	    }

	    return _.isArray(collection) ? collection : [];
	});

	Vue.filter('toObject', function(collection) {
	    return _.isArray(collection) ? collection.reduce(function(obj, value, key) {
	        obj[key] = value;
	        return obj;
	    }, {}) : collection;
	});

	Vue.filter('toOptions', function toOptions(collection) {
	    return Object.keys(collection).map(function (key) {

	        var op = collection[key];
	        if (typeof op === 'string') {
	            return { text: op, value: key };
	        } else {
	            return { label: key, options: toOptions(op) };
	        }

	    });
	});

	var evalExp = function(expression) {

	    try {

	        return undefined === expression ? expression : Vue.parsers.expression.parse(expression).get.call(this, this);

	    } catch (e) {
	        if (Vue.config.warnExpressionErrors) {
	            Vue.util.warn('Error when evaluating expression "' + expression + '":\n   ' + e);
	        }
	    }

	};


/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Vue Pagination component.
	 */

	var Vue = __webpack_require__(3);

	Vue.component('v-pagination', {

	    replace: true,

	    template: '<ul class="uk-pagination"></ul>',

	    data: function() {
	        return {
	            page: 1,
	            pages: 1
	        };
	    },

	    ready: function() {

	        var vm = this, pagination = UIkit.pagination(this.$el, { pages: this.pages });

	        pagination.on('select.uk.pagination', function(e, page) {
	            vm.$set('page', page);
	        });

	        this.$watch('page', function(page) {
	            pagination.selectPage(page);
	        }, true);

	        this.$watch('pages', function(pages) {
	            pagination.render(pages);
	        }, true);

	        pagination.selectPage(this.page);
	    }

	});


/***/ },
/* 12 */,
/* 13 */,
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = _;

/***/ }
/******/ ]);