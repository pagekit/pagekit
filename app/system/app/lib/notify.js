module.exports = function (Vue) {

    var Notify = function() {

        if (arguments[0]) {
            arguments[0] = this.$trans(arguments[0]);
        }

        UIkit.notify.apply(this, arguments);

    };

    Vue.prototype.$notify = Notify;

    return Notify;

};
