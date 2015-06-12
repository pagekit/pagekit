module.exports = {

    isLiteral: true,

    bind: function () {

        var self = this, vm = this.vm, el = $(this.el), keypath = this.arg, selector = this.expression;

        el.on('change.check-all', function () {
            $(selector, vm.$el).prop('checked', $(this).prop('checked'));
            vm.$set(keypath, self.checked());
        });

        $(vm.$el).on('change.check-all', selector, function () {
            vm.$set(keypath, self.state());
        });

        $(vm.$el).on('click.check-all', '.check-item', function (e) {

            if (!$(e.target).is(':input, a') && !window.getSelection().toString()) {
                $(this).find(selector).trigger('click');
            }
        });

        this.unbindWatcher = vm.$watch(keypath, function (selected) {

            $(selector, vm.$el).prop('checked', function () {
                return selected.indexOf($(this).val()) !== -1;
            });

            self.state();
        });

    },

    unbind: function () {

        $(this.el).off('.check-all');
        $(this.vm.$el).off('.check-all');

        if (this.unbindWatcher) {
            this.unbindWatcher();
        }
    },

    state: function () {

        var el = $(this.el), checked = this.checked();

        if (checked.length === 0) {
            el.prop('checked', false).prop('indeterminate', false);
        } else if (checked.length == $(this.expression, this.vm.$el).length) {
            el.prop('checked', true).prop('indeterminate', false);
        } else {
            el.prop('indeterminate', true);
        }

        return checked;
    },

    checked: function () {

        var checked = [];

        $(this.expression, this.vm.$el).each(function () {
            if ($(this).prop('checked')) {
                checked.push($(this).val());
            }
        });

        return checked;
    }

};
