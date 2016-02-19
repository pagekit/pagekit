module.exports = {

    params: ['group'],

    update: function (subSelector) {

        var self = this, keypath = this.arg, group = this.params.group ? this.params.group + ' ' : '', selector = group + subSelector;

        this.selector = selector;
        this.$el = this.vm.$el;
        this.checked = false;
        this.number = this.el.getAttribute('number') !== null;

        $(this.el).on('change.check-all', function () {
            $(selector, self.$el).prop('checked', $(this).prop('checked'));
            self.selected(true);
        });

        this.handler = [
            function () {
                self.selected(true);
                self.state();
            },
            function (e) {
                if (!$(e.target).is(':input, a') && !window.getSelection().toString()) {
                    $(this).find(subSelector).trigger('click');
                }
            }
        ];

        $(this.$el).on('change.check-all', selector, this.handler[0]);
        $(this.$el).on('click.check-all', group + '.check-item', this.handler[1]);

        this.unbindWatcher = this.vm.$watch(keypath, function (selected) {

            $(subSelector, this.$el).prop('checked', function () {
                return selected.indexOf(self.toNumber($(this).val())) !== -1;
            });

            self.selected();
            self.state();
        });
    },

    unbind: function () {

        var self = this;

        $(this.el).off('.check-all');

        if (this.handler) {
            this.handler.forEach(function (handler) {
                $(self.$el).off('.check-all', handler);
            });
        }

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

        $(this.selector, this.$el).each(function () {

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
