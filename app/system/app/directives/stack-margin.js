module.exports = {

    bind: function () {

        this.cls = this.el.classList.contains('uk-grid') ? 'uk-grid-margin':'uk-margin-small-top';
    },

    update: function (data) {

        var $el = $(this.el), cls = this.cls;

        Vue.nextTick(function () {
            UIkit.Utils.stackMargin($el.children(), {cls:cls});
        });
    }

};
