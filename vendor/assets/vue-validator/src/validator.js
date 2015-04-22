var _ = require('./util');

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
