/**
 * Install plugin.
 */

function install(Vue) {

    var v = Vue.prototype,
        _ = require('./util')(Vue);

    v.$date = require('./date')(_);
    v.$number = require('./number')(_);
    v.$currency = require('./currency')(_);
    v.$locale = require('../dist/locales/en.json');

    Vue.filter('date', v.$date);
    Vue.filter('number', v.$number);
    Vue.filter('currency', v.$currency);
}

if (window.Vue) {
    Vue.use(install);
}

module.exports = install;
