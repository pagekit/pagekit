(function () {

    function install (Vue) {

        Vue.prototype.$date = System.date;
        Vue.prototype.$trans = System.trans;
        Vue.prototype.$transChoice = System.transChoice;

        Vue.filter('trans', function(id, parameters, domain, locale) {
            return this.$trans(id, parameters, domain, locale);
        });

        Vue.filter('transChoice', function(id, number, parameters, domain, locale) {
            return this.$transChoice(id, number, parameters, domain, locale);
        });

    }

    if (window.Vue) {
        Vue.use(install);
    }

})();