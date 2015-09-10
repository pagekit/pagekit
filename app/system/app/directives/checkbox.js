module.exports = {

    twoWay: true,

    bind: function () {

        var self = this, vm = this.vm, expression = this.expression, el = $(this.el);

        this.number = this.el.getAttribute('number') !== null;

        el.on('change.checkbox', function () {

            var model = vm.$get(expression), contains = model.indexOf(self.toNumber(el.val()));

            if (el.prop('checked')) {
                if (-1 === contains) {
                    model.push(self.toNumber(el.val()));
                }
            } else if (-1 !== contains) {
                model.splice(contains, 1);
            }
        });

    },

    update: function (value) {

        if (undefined === value) {
            this.set([]);
            return;
        }

        $(this.el).prop('checked', -1 !== value.indexOf(this.toNumber(this.el.value)));
    },

    unbind: function () {
        $(this.el).off('.checkbox');
    },

    toNumber: function (value) {
        return this.number ? Number(value) : value;
    }

};
