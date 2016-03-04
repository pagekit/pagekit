module.exports = {

    el: '#marketplace',

    data: function () {
        return _.extend({
            search: this.$session.get('marketplace.search', '')
        }, window.$data);
    },

    watch: {

        search: function (search) {
            this.$session.set('marketplace.search', search);
        }

    },

    components: {
        'marketplace': require('../components/marketplace.vue')
    }

};

Vue.ready(module.exports);
