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

    Vue.filter('date', function (date, format) {
        return this.$date(date, format);
    });

    Vue.filter('relativeDate', function (value, reference) {
        try {
            return this.$relativeDate(value, reference);
        } catch (e) {
            return 'NaN';
        }
    });

    Vue.filter('trim', {

        write: function (value) {
            return value.trim();
        }

    });

    Vue.filter('stripTags', function stripTags(value, allowed) {

        var comments = /<!--[\s\S]*?-->/gi, tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;

        allowed = ((allowed || '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

        return value.replace(comments, '').replace(tags, function(tag, name) {
            return allowed.indexOf('<' + name.toLowerCase() + '>') > -1 ? tag : '';
        });

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
