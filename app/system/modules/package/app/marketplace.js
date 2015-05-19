jQuery(function ($) {

    new Vue({

        el: '#marketplace',

        data: $.extend(window.$data, {
            search: ''
        }),

        components: {
            'v-marketplace': require('./components/marketplace.vue')
        }

    });

});
