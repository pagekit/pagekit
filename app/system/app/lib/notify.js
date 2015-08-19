module.exports = function (Vue) {

    Vue.prototype.$notify = function () {

        var args = arguments,
            msgs = window.jQuery('.pk-system-messages'),
            UIkit = window.UIkit || {};

        if (args[0]) {
            args[0] = this.$trans(args[0]);
        }

        if (UIkit.notify) {
            UIkit.notify.apply(this, args);
        } else if (msgs) {
            msgs.empty().append('<div class="uk-alert uk-alert-' + (args[1] || 'info') + '"><p>' + args[0] + '</p></div>');
        }

    };

};
