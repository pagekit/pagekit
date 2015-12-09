module.exports = {

    el: '#marketplace',

    data: _.extend(window.$data, {
        search: ''
    }),

    components: {
        'marketplace': require('../components/marketplace.vue')
    }

};

Vue.ready(module.exports);
