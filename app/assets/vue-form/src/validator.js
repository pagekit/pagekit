/**
 * Validator for input validation.
 */

module.exports = function (_) {

    return _.validator = {

        dirs: [],

        types: require('./validators'),

        add: function (dir) {
            this.dirs.push(dir);
        },

        remove: function (dir) {
            _.pull(this.dirs, dir);
        },

        instance: function (el) {

            do {

                if (el._validator) {
                    return el._validator;
                }

                el = el.parentElement;

            } while (el);

        },

        validate: function (el, submit) {

            var validator = this.instance(el), results = {valid: true};

            if (!validator) {
                return;
            }

            this.dirs.forEach(function (dir) {

                var valid = dir.validate(), el = dir.el, name = dir.name;

                if (this.instance(el) !== validator) {
                    return;
                }

                if (!el._touched && submit) {
                    el._touched = true;
                }

                if (!el._touched && !valid) {
                    valid = true;
                    results.valid = false;
                }

                if (!results[name]) {
                    results[name] = {
                        valid: true,
                        invalid: false,
                        dirty: el._dirty,
                        touched: el._touched
                    };
                }

                results[name][dir.type] = !valid;

                if (submit && results.valid && !valid) {
                    el.focus();
                }

                if (results[name].valid && !valid) {
                    results[name].valid = false;
                    results[name].invalid = true;
                    results.valid = false;
                }

            }, this);

            results.invalid = !results.valid;

            validator.results(results);

            if (submit && results.invalid) {
                _.trigger(validator.el, 'invalid');
            }

            return results.valid;
        },

        filter: function (fn) {
            return function (e) {
                e.preventDefault();

                if (_.validator.validate(e.target, true)) {
                    fn(e);
                }

            }.bind(this);
        },

        directive: {

            bind: function () {

                var self = this, name = this.arg || this.expression;

                this.name = _.camelize(name);
                this.el._validator = this;

                this.vm.$set(this.name);
                this.vm.$on('hook:compiled', function () {
                    _.validator.validate(self.el);
                });
            },

            unbind: function () {
                this.vm.$delete(this.name);
            },

            results: function (results) {
                this.vm.$set(this.name, _.extend({
                    validate: this.validate.bind(this)
                }, results));
            },

            validate: function () {
                return _.validator.validate(this.el, true);
            }

        }

    };

};
