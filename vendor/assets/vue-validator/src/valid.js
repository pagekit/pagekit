var _ = require('./util');

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

        this.name    = _.camelize(name);
        this.form    = _.camelize(form);
        this.type    = this.arg || this.expression;
        this.args    = this.arg ? this.expression : '';
        this.value   = el.value;
        this.model   = el.form.__vue__;

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
