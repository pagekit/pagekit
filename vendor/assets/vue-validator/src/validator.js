var _ = require('./util');

/**
 * Validator for form input validation.
 */

module.exports = {

    validators: {},

    bind: function (dir) {

        var self = this, name = dir.form;

        if (!this.validators[name]) {

            this.validators[name] = {
                form: dir.el.form,
                vm: dir.vm.$root,
                handler: function (e) {
                    e.preventDefault();
                    _.trigger(e.target, self.validate(name, true) ? 'valid' : 'invalid');
                },
                dirs: []
            };

            dir.vm.$root.$set(name, {});
            _.on(dir.el.form, 'submit', this.validators[name].handler);
        }

        this.validators[name].dirs.push(dir);
    },

    unbind: function (dir) {

        var form = this.validators[dir.form];

        if (!form) {
            return;
        }

        form.dirs.splice(form.dirs.indexOf(dir), 1);

        if (!form.dirs.length) {
            _.off(dir.el.form, 'submit', form.handler);
            delete this.validators[dir.form];
        }
    },

    validate: function (form, submit) {

        var validator = this.validators[form], results = { valid: true, invalid: false };

        if (!validator) {
            return true;
        }

        validator.dirs.forEach(function(dir) {

            var valid = dir.validate(), el = dir.el, name = dir.name;

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

        validator.vm.$set(form, results);

        return results.valid;
    }

};
