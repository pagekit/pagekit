module.exports = {

    twoWay: true,

    bind: function () {

        var vm = this.vm, expression = this.expression, el = $(this.el);

        el.on('change.checkbox', function () {

            var model = vm.$get(expression), contains = model.indexOf(el.val());

            if (el.prop('checked')) {
                if (-1 === contains) {
                    model.push(el.val());
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

        $(this.el).prop('checked', -1 !== value.indexOf(this.el.value));
    },

    unbind: function () {
        $(this.el).off('.checkbox');
    }

};
