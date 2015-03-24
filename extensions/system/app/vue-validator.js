(function (window) {

    var install = function (Vue) {

        var _ = Vue.util.extend({}, Vue.util);


        /**
         * The Validator for form input validation
         */

        Vue.prototype.$validator = {

            elements: [],

            validators: {},

            bind: function (dir) {

                var self = this, el = dir.el, form = dir.form;

                if (!this.validators[form]) {
                    this.validators[form] = [];
                    _.on(el.form, 'submit', function (e) {
                        e.preventDefault();
                        _.trigger(e.target, self.validate(form, true) ? 'valid' : 'invalid');
                    });
                }

                if (this.elements.indexOf(el) == -1) {
                    this.elements.push(el);
                    _.on(el, 'blur', dir.listener);
                    _.on(el, 'input', dir.listener);
                }

                dir.vm.$set(form + '.' + dir.name, {});
                this.validators[form].push(dir);
            },

            unbind: function (dir) {

                var form = dir.form, validators = this.validators[form];

                validators.splice(validators.indexOf(dir), 1);

                if (!validators.length) {
                    delete this.validators[form];
                }
            },

            validate: function (form, submit) {

                var results = {}, focus, keys, vm;

                if (!this.validators[form]) return results;

                this.validators[form].forEach(function (dir) {

                    vm = dir.vm;

                    var name = dir.name, valid = dir.validate();

                    if (submit) {
                        dir.touched = true;
                    }

                    if (!dir.touched) {
                        results[name] = {};
                        return;
                    }

                    if (!results[name]) {
                        results[name] = {
                            valid: true,
                            invalid: false,
                            touched: dir.touched,
                            dirty: dir.dirty
                        };
                    }

                    if (submit && !focus && !valid) {
                        dir.el.focus();
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

                if (vm) vm.$set(form, results);

                return results.valid;
            }

        };

        Vue.directive('valid', {

            bind: function () {

                var self = this, name = _.attr(this.el, 'name'), form = _.attr(this.el.form, 'name');

                if (!name || !form) {
                    return;
                }

                this.name    = _.camelize(name);
                this.form    = _.camelize(form);
                this.type    = this.arg || this.expression;
                this.args    = this.arg ? this.expression : '';
                this.value   = this.el.value;
                this.dirty   = false;
                this.touched = false;

                this.listener = function (e) {

                    if (!self.el) return;

                    if (e.type == 'blur') {
                        self.touched = true;
                    }

                    if (self.el.value != self.value) {
                        self.dirty = true;
                    }

                    self.vm.$validator.validate(self.form);
                };

                this.vm.$validator.bind(this);
            },

            unbind: function () {
                if (this.form) {
                    this.vm.$validator.unbind(this);
                }
            },

            validate: function () {

                var validator = Vue.validators[this.type];

                return validator ? validator(this.el.value, this.args) : undefined;
            }

        });

        Vue.validators = {
            required: function (value) {
                if (typeof value == 'boolean') return value;
                return !((value === null) || (value.length === 0));
            },
            numeric: function (value) {
                return (/^\s*(\-|\+)?(\d+|(\d*(\.\d*)))\s*$/).test(value);
            },
            integer: function (value) {
                return (/^(-?[1-9]\d*|0)$/).test(value);
            },
            digits: function (value) {
                return (/^[\d() \.\:\-\+#]+$/).test(value);
            },
            alpha: function (value) {
                return (/^[a-zA-Z]+$/).test(value);
            },
            alphaNum: function (value) {
                return !(/\W/).test(value);
            },
            email: function (value) {
                return (/^[a-z0-9!#$%&'*+\/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/i).test(value);
            },
            url: function (value) {
                return (/^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/).test(value);
            },
            minLength: function (value, arg) {
                return value && value.length && value.length >= +arg;
            },
            maxLength: function (value, arg) {
                return value && value.length && value.length <= +arg;
            },
            length: function (value) {
                return value && value.length == +arg;
            },
            min: function (value, arg) {
                return value >= +arg;
            },
            max: function (value, arg) {
                return value <= +arg;
            },
            pattern: function (value, arg) {
                var match = arg.match(new RegExp('^/(.*?)/([gimy]*)$'));
                var regex = new RegExp(match[1], match[2]);
                return regex.test(value);
            }
        };

        /**
         * The Utility functions
         */

        _.attr = function (el, attr) {
            return el ? el.getAttribute(attr) : null;
        };

        _.trigger = function(el, event) {
            var e = document.createEvent('HTMLEvents');
            e.initEvent(event, true, false);
            el.dispatchEvent(e);
        };

    };

    if (typeof exports == 'object') {
        module.exports = install;
    } else if (typeof define == 'function' && define.amd) {
        define([], function (){ return install; });
    } else if (window.Vue) {
        Vue.use(install);
    }

})(this);
