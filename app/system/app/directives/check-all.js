module.exports = {

    isLiteral: true,

    bind: function () {

        var self = this, keypath = this.arg, selector = this.expression;

        this.$el = this.vm.$el;
        this.checked = false;
        this.number = this.el.getAttribute('number') !== null;

        $(this.el).on('change.check-all', function () {
            $(selector, self.$el).prop('checked', $(this).prop('checked'));
            self.selected(true);
        });

        $(this.$el).on('change.check-all', selector, function () {
            self.selected(true);
            self.state();
        });

        $(this.$el).on('click.check-all', '.check-item', function (e) {
            if (!$(e.target).is(':input, a') && !window.getSelection().toString()) {
                $(this).find(selector).trigger('click');
            }
        });

        this.unbindWatcher = this.vm.$watch(keypath, function (selected) {

            $(selector, this.$el).prop('checked', function () {
                return selected.indexOf(self.toNumber($(this).val())) !== -1;
            });

            self.selected();
            self.state();
        });

    },

    unbind: function () {

        $(this.el).off('.check-all');
        $(this.$el).off('.check-all');

        if (this.unbindWatcher) {
            this.unbindWatcher();
        }
    },

    state: function () {

        var el = $(this.el);

        if (this.checked === undefined) {
            el.prop('indeterminate', true);
        } else {
            el.prop('checked', this.checked).prop('indeterminate', false);
        }

    },

    selected: function (update) {

        var self = this, keypath = this.arg, selected = [], values = [], value;

        $(this.expression, this.$el).each(function () {

            value = self.toNumber($(this).val());
            values.push(value);

            if ($(this).prop('checked')) {
                selected.push(value);
            }
        });

        if (update) {

            update = this.vm.$get(keypath).filter(function (value) {
                return values.indexOf(value) === -1;
            });

            this.vm.$set(keypath, update.concat(selected));
        }

        if (selected.length === 0) {
            this.checked = false;
        } else if (selected.length == values.length) {
            this.checked = true;
        } else {
            this.checked = undefined;
        }

    },

    toNumber: function (value) {
        return this.number ? Number(value) : value;
    }

};
