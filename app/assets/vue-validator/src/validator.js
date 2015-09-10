var _ = require('./util');

/**
 * Validator for form input validation.
 */

module.exports = {

    validators: {},

    bind: function (dir) {

        var self = this, name = dir.form, vm = findVm(dir.el.form);

        if (!vm) {
            return;
        }

        if (!this.validators[name]) {

            this.validators[name] = {
                form: dir.el.form,
                vm: vm,
                handler: function (e) {
                    e.preventDefault();
                    _.trigger(e.target, self.validate(name, true) ? 'valid' : 'invalid');
                },
                dirs: []
            };

            vm.$add(name, {});
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
            if (form.form) {
                _.off(form.form, 'submit', form.handler);
            }
            form.vm.$delete(dir.form);
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

function findVm(elm) {

    do {

        if (elm.__vue__) {
            return elm.__vue__;
        }

        elm = elm.parentElement;

    } while (elm);

    return undefined;
}
