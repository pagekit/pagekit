/**
 * Install plugin.
 */

function install(Vue) {

    var v = Vue.prototype,
        _ = require('./util')(Vue);

    require('./plural')(_);

    if (!v.$locale) {
        v.$locale = require('../dist/locales/en.json');
    }

    v.$date = require('./date')(_);
    v.$number = require('./number')(_);
    v.$currency = require('./currency')(_);
    v.$relativeDate = require('./relative')(_);

    Vue.filter('date', v.$date);
    Vue.filter('number', v.$number);
    Vue.filter('currency', v.$currency);
    Vue.filter('relativeDate', v.$relativeDate);
}

if (window.Vue) {
    Vue.use(install);
}

module.exports = install;
