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

	var Directive = __webpack_require__(4);
	var Validator = __webpack_require__(5);
	var Validators = __webpack_require__(6);

	/**
	 * Install plugin.
	 */

	function install (Vue) {

	    Vue.validators = Validators;
	    Vue.directive('valid', Directive);

	    Vue.prototype.$validator = Validator;

	}

	if (window.Vue) {
	    Vue.use(install);
	}

	module.exports = install;


/***/ },
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(8);

	/**
	 * Valid directive.
	 */

	module.exports = {

	    bind: function () {
	        this.vm.$on('hook:ready', function() { this.init(); }.bind(this));
	    },

	    unbind: function () {
	        if (this.form) {
	            this.vm.$validator.unbind(this);
	        }
	    },

	    validate: function () {

	        var validator = Vue.validators[this.type];

	        return validator ? validator.call(this.vm, this.el.value, this.args) : undefined;
	    },

	    init: function() {

	        var self = this, el = this.el, name = _.attr(el, 'name'), form = _.attr(el.form, 'name');

	        if (!name || !form) {
	            return;
	        }

	        this.name  = _.camelize(name);
	        this.form  = _.camelize(form);
	        this.type  = this.arg || this.expression;
	        this.args  = this.arg ? this.expression : '';
	        this.value = el.value;
	        this.model = findVM(el.form);

	        el._dirty   = false;
	        el._touched = false;

	        this.listener = function (e) {

	            if (!el || e.relatedTarget && (e.relatedTarget.tagName === 'A' || e.relatedTarget.tagName === 'BUTTON')) return;

	            if (e.type == 'blur') {
	                el._touched = true;
	            }

	            if (el.value != self.value) {
	                el._dirty = true;
	            }

	            self.vm.$validator.validate(self.form);
	        };

	        this.vm.$validator.bind(this);
	    }

	};

	function findVM(el) {
	    do {

	        if (el.__vue__) {
	            return el.__vue__;
	        }

	        el = el.parentNode;

	    } while (el);
	}


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(8);

	/**
	 * Validator for form input validation.
	 */

	module.exports = {

	    elements: [],

	    validators: {},

	    bind: function (dir) {

	        var self = this, el = dir.el, form = dir.form;

	        if (!this.validators[form]) {

	            this.validators[form] = [];
	            this.validators[form].handler = function (e) {
	                e.preventDefault();
	                _.trigger(e.target, self.validate(form, true) ? 'valid' : 'invalid');
	            };

	            this.validators[form].model = dir.model;

	            _.on(el.form, 'submit', this.validators[form].handler);

	            dir.model.$set(form, {});
	        }

	        if (this.elements.indexOf(el) == -1) {

	            this.elements.push(el);

	            _.on(el, 'blur', dir.listener);
	            _.on(el, 'input', dir.listener);
	        }

	        dir.model[form].$add(dir.name, {});
	        this.validators[form].push(dir);
	    },

	    unbind: function (dir) {

	        var form = dir.form, validators = this.validators[form];

	        validators.splice(validators.indexOf(dir), 1);

	        if (!validators.length) {
	            _.off(dir.el.form, 'submit', validators.handler);
	            delete this.validators[form];
	        }
	    },

	    validate: function (form, submit) {

	        var results = {}, focus, keys;

	        if (!this.validators[form]) return results;

	        this.validators[form].forEach(function (dir) {

	            var el = dir.el, name = dir.name, valid = dir.validate();

	            if (submit) {
	                el._touched = true;
	            }

	            if (!el._touched) {
	                results[name] = {};
	                return;
	            }

	            if (!results[name]) {
	                results[name] = {
	                    valid: true,
	                    invalid: false,
	                    touched: el._touched,
	                    dirty: el._dirty
	                };
	            }

	            if (submit && !focus && !valid) {
	                el.focus();
	                focus = true;
	            }

	            results[name][dir.type] = !valid;

	            if (results[name].valid && !valid) {
	                results[name].valid = results.valid = false;
	                results[name].invalid = results.invalid = true;
	            }

	        });

	        keys = Object.keys(results);

	        if (keys.length && keys.indexOf('valid') == -1) {
	            results.valid = true;
	            results.invalid = false;
	        }

	        this.validators[form].model.$set(form, results);

	        return results.valid;
	    }

	};


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Validate functions.
	 */

	function required (value) {
	    if (typeof value == 'boolean') return value;
	    return !((value === null) || (value.length === 0));
	}

	function numeric (value) {
	    return (/^\s*(\-|\+)?(\d+|(\d*(\.\d*)))\s*$/).test(value);
	}

	function integer (value) {
	    return (/^(-?[1-9]\d*|0)$/).test(value);
	}

	function digits (value) {
	    return (/^[\d() \.\:\-\+#]+$/).test(value);
	}

	function alpha (value) {
	    return (/^[a-zA-Z]+$/).test(value);
	}

	function alphaNum (value) {
	    return (/\w/).test(value);
	}

	function email (value) {
	    return (/^[a-z0-9!#$%&'*+\/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/i).test(value);
	}

	function url (value) {
	    return (/^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/).test(value);
	}

	function minLength (value, arg) {
	    return value && value.length && value.length >= +arg;
	}

	function maxLength (value, arg) {
	    return value && value.length && value.length <= +arg;
	}

	function length (value) {
	    return value && value.length == +arg;
	}

	function min (value, arg) {
	    return value >= +arg;
	}

	function max (value, arg) {
	    return value <= +arg;
	}

	function pattern (value, arg) {
	    var match = arg.match(new RegExp('^/(.*?)/([gimy]*)$'));
	    var regex = new RegExp(match[1], match[2]);
	    return regex.test(value);
	}

	module.exports = {
	    required: required,
	    numeric: numeric,
	    integer: integer,
	    digits: digits,
	    alpha: alpha,
	    alphaNum: alphaNum,
	    email: email,
	    url: url,
	    minLength: minLength,
	    maxLength: maxLength,
	    min: min,
	    max: max,
	    pattern: pattern
	};


/***/ },
/* 7 */,
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var _ = Vue.util.extend({}, Vue.util);

	/**
	 * Utility functions.
	 */

	_.attr = function (el, attr) {
	    return el ? el.getAttribute(attr) : null;
	};

	_.trigger = function(el, event) {
	    var e = document.createEvent('HTMLEvents');
	    e.initEvent(event, true, false);
	    el.dispatchEvent(e);
	};

	module.exports = _;

/***/ }
/******/ ]);