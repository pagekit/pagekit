var _ = require('./util');

/**
 * Valid directive.
 */

module.exports = {

    isLiteral: true,

    bind: function () {
        Vue.nextTick(this.init.bind(this));
    },

    unbind: function () {

        this.vm.$validator.unbind(this);

        _.off(this.el, 'input', this.listener);
        _.off(this.el, 'blur', this.listener);

    },

    init: function() {

        var self = this, el = this.el, name = _.attr(el, 'name'), form = _.attr(el.form, 'name');

        if (!name || !form) {
            return;
        }

        this.name      = _.camelize(name);
        this.form      = _.camelize(form);
        this.type      = this.arg || this.expression;
        this.args      = this.arg ? this.expression : '';
        this.value     = el.value;
        this.validator = getValidators(this.vm)[this.type];

        if (!this.validator) {
            return;
        }

        el._dirty   = false;
        el._touched = false;

        this.listener = function (e) {

            if (e.relatedTarget && (e.relatedTarget.tagName === 'A' || e.relatedTarget.tagName === 'BUTTON')) {
                return;
            }

            if (e.type == 'blur') {
                el._touched = true;
            }

            if (el.value != self.value) {
                el._dirty = true;
            }

            self.vm.$validator.validate(self.form);
        };

        if (!el._bound) {
            _.on(el, 'input', this.listener);
            _.on(el, 'blur', this.listener);
            el._bound = true;
        }

        this.vm.$validator.bind(this);
    },

    validate: function () {
        return this.validator.call(this.vm, this.el.value, this.args);
    }

};

function getValidators(vm) {

    var validators = {};

    do {

        validators = defaults(validators, vm.$options.validators || {});

        vm = vm.$parent;

    } while (vm);

    return defaults(validators, Vue.validators);
}

function defaults(target, source) {

    return _.extend(_.extend({}, source), target)
}
