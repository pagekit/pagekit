/**
 * Validate directive.
 */

module.exports = function (_) {

    return {

        bind: function () {

            var name = _.attr(this.el, 'name');

            if (!name) {
                return;
            }

            this.name = _.camelize(name);
            this.type = this.arg || this.expression;
            this.value = this.el.value;

            this.el._dirty = false;
            this.el._touched = false;

            _.on(this.el, 'blur', this.listener.bind(this));
            _.on(this.el, 'input', this.listener.bind(this));

            _.validator.add(this);
        },

        unbind: function () {

            _.off(this.el, 'blur', this.listener);
            _.off(this.el, 'input', this.listener);

            _.validator.remove(this);
        },

        update: function (value) {
            this.args = value;
        },

        listener: function (e) {

            if (e.relatedTarget && (e.relatedTarget.tagName === 'A' || e.relatedTarget.tagName === 'BUTTON')) {
                return;
            }

            if (e.type == 'blur') {
                this.el._touched = true;
            }

            if (this.el.value != this.value) {
                this.el._dirty = true;
            }

            _.validator.validate(this.el);
        },

        validate: function () {

            var validator = this.validator();

            if (validator) {
                return validator.call(this.vm, this.el.value, this.args);
            }
        },

        validator: function () {

            var vm = this.vm, validators;

            do {

                validators = vm.$options.validators || {};

                if (validators[this.type]) {
                    return validators[this.type];
                }

                vm = vm.$parent;

            } while (vm);

            return _.validator.types[this.type];
        }

    };

};
