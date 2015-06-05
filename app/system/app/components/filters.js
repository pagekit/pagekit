/**
 * Vue Filters
 */

var $ = require('jquery');
var _ = require('lodash');
var Vue = require('vue');

Vue.filter('baseUrl', function(url) {
    return _.startsWith(url, Vue.url.options.root) ? url.substr(Vue.url.options.root.length) : url;
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

Vue.filter('trim', {

    write: function (val) {
        return val.trim();
    }

});

var evalExp = function(expression) {

    try {

        return undefined === expression ? expression : Vue.parsers.expression.parse(expression).get.call(this, this);

    } catch (e) {

        if (Vue.util.warn && Vue.config.warnExpressionErrors) {
            Vue.util.warn('Error when evaluating expression "' + expression + '":\n   ' + e);
        }
    }

};
