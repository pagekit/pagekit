/**
 * Vue Filters
 */

Vue.filter('baseUrl', function(url) {
    return _.startsWith(url, config.url) ? url.substr(config.url.length) : url;
});

Vue.filter('trans', function(id, parameters, domain, locale) {
    return this.$trans(id, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
});

Vue.filter('transChoice', function(id, number, parameters, domain, locale) {
    return this.$transChoice(id, evalExp.call(this, number) || 0, evalExp.call(this, parameters), evalExp.call(this, domain), evalExp.call(this, locale));
});

Vue.filter('date', function(date, format) {
    return this.$date(date, format);
});

Vue.filter('first', function(collection) {
    return Vue.filter('toArray')(collection)[0];
});

Vue.filter('length', function(collection) {
    return Vue.filter('toArray')(collection).length;
});

Vue.filter('toArray', function(collection) {

    if (_.isPlainObject(collection)) {
        return Object.keys(collection).map(function(key) {
            return collection[key];
        });
    }

    return _.isArray(collection) ? collection : [];
});

Vue.filter('toObject', function(collection) {
    return _.isArray(collection) ? collection.reduce(function(obj, value, key) {
        obj[key] = value;
        return obj;
    }, {}) : collection;
});

Vue.filter('toOptions', function toOptions(collection) {
    return Object.keys(collection).map(function (key) {

        var op = collection[key];
        if (typeof op === 'string') {
            return { text: op, value: key };
        } else {
            return { label: key, options: toOptions(op) };
        }

    });
});

var evalExp = function(expression) {

    try {

        return undefined === expression ? expression : Vue.parsers.expression.parse(expression).get.call(this, this);

    } catch (e) {
        if (Vue.config.warnExpressionErrors) {
            Vue.util.warn('Error when evaluating expression "' + expression + '":\n   ' + e);
        }
    }

};
