module.exports = {

    bind: function () {
        this.vm.$add(this.arg);
    },

    unbind: function () {
        this.vm.$delete(this.arg);
    },

    update: function (value) {
        this.vm.$set(this.arg, value);
    }

};
