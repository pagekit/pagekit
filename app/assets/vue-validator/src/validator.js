/**
 * Validator for form input validation.
 */

module.exports = function (_) {

    var Validator = {

        directives: [],

        bind: function (dir) {
            this.directives.push(dir);
        },

        unbind: function (dir) {
            this.directives.splice(this.directives.indexOf(dir), 1);
        },

        validate: function (form, submit) {

            var vm = _.vm(form), name = _.attr(form, 'name'), results = {valid: true, invalid: false};

            if (!vm || !name) {
                return;
            }

            this.directives.forEach(function (dir) {

                var valid = dir.validate(), el = dir.el, name = dir.name;

                if (el.form !== form) {
                    return;
                }

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

                results[name][dir.type] = !valid;

                if (submit && results.valid && !valid) {
                    el.focus();
                }

                if (results[name].valid && !valid) {
                    results[name].valid = results.valid = false;
                    results[name].invalid = results.invalid = true;
                }
            });

            if (vm.$get(name)) {
                vm.$set(name, results);
            } else {
                vm.$add(name, results);
            }

            if (submit && results.invalid) {
                _.trigger(form, 'invalid');
            }

            return results.valid;
        }

    };

    Validator.filter = function (fn) {

        return function (e) {
            e.preventDefault();

            if (Validator.validate(e.target, true)) {
                fn(e);
            }

        }.bind(this);
    };

    Validator.types = require('./validators');
    Validator.directive = require('./valid')(_);

    return _.validator = Validator;
};
