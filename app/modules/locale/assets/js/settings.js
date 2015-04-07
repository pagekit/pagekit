Vue.component('v-locale', {

    inherit: true,
    replace: true,

    ready: function() {

        var changed = false;

        this.$watch('adminLocale', function() {
            changed = true;
        }, true);

        this.$on('save', function() {
            if (changed) {
                window.location.reload();
            }
        }, true);

    },

    computed: {

        adminLocale: function() {
            return this.option['locale'].locale_admin;
        }

    }

});
