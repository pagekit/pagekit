module.exports = function (Vue) {

    Vue.filter('baseUrl', function (url) {
        return _.startsWith(url, Vue.url.options.root) ? url.substr(Vue.url.options.root.length) : url;
    });

    Vue.filter('trans', function (id, parameters, domain, locale) {
        return this.$trans(id, parameters, domain, locale);
    });

    Vue.filter('transChoice', function (id, number, parameters, domain, locale) {
        return this.$transChoice(id, number, parameters, domain, locale);
    });

    Vue.filter('trim', {

        write: function (value) {
            return value.trim();
        }

    });

    Vue.filter('toOptions', function toOptions(collection) {
        return collection ? Object.keys(collection).map(function (key) {

            var op = collection[key];

            if (typeof op === 'string') {
                return {text: op, value: key};
            } else {
                return {label: key, options: toOptions(op)};
            }

        }) : [];
    });

};
