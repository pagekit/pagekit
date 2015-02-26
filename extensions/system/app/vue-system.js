(function ($) {

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

        Vue.filter('first', function(collection) {
            return Vue.filter('toArray')(collection)[0];
        });

        Vue.filter('length', function(collection) {
            return Vue.filter('toArray')(collection).length;
        });

        Vue.filter('toArray', function(collection) {

            if ($.isPlainObject(collection)) {
                return Object.keys(collection)

                    .filter(function(key) {
                        return key.charAt(0) !== '$';
                    })

                    .map(function(key) {
                        return collection[key];
                    });
            }

            return $.isArray(collection) ? collection : [];
        });

        Vue.filter('toObject', function (collection) {
            return $.isArray(collection) ? collection.reduce(function (obj, value, key) {
                obj[key] = value;
                return obj;
            }, {}) : collection;
        });

    }

    if (window.Vue) {
        Vue.use(install);
    }

})(jQuery);
