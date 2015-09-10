module.exports = {

    data: _.extend(window.$data, {
        search: ''
    }),

    components: {
        'marketplace': require('../components/marketplace.vue')
    }

};

$(function () {

    new Vue(module.exports).$mount('#marketplace');

});
