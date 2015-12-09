var _ = Vue.util;

module.exports = {

    priority: 500,

    bind: function () {

        var self = this, el = this.el, buttons = (_.getAttr(el, 'buttons') || '').split(',');

        this.options = {
            title: false,
            labels: {
                Ok: buttons[0] || this.vm.$trans('Ok'),
                Cancel: buttons[1] || this.vm.$trans('Cancel')
            }
        };

        this.dirs = this.vm._directives.filter(function (dir) {
            return dir.name == 'on' && dir.el === el;
        });

        this.dirs.forEach(function (dir) {

            _.off(dir.el, dir.arg, dir.handler);
            _.on(dir.el, dir.arg, function (e) {
                UIkit.modal.confirm(self.vm.$trans(self.options.text), function () {
                    dir.handler(e);
                }, self.options);
            });

        });
    },

    update: function (value) {

        // vue-confirm="'Title':'Text...?'"
        if (this.arg) {
            this.options.title = this.arg;
        }

        // vue-confirm="'Text...?'"
        if (typeof value === 'string') {
            this.options.text = value;
        }

        // vue-confirm="{title:'Title', text:'Text...?'}"
        if (typeof value === 'object') {
            this.options = _.extend(this.options, value);
        }
    },

    unbind: function () {
        this.dirs.forEach(function (dir) {
            try {
                _.off(dir.el, dir.arg, dir.handler);
            } catch (e) {
            }
        });
    }

};
