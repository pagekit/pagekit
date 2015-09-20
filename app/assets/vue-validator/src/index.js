/**
 * Install plugin.
 */

module.exports = function (Vue) {

    var _ = require('./util')(Vue);
    var v = require('./validator')(_);

    Vue.validator = v;
    Vue.filter('valid', v.filter);
    Vue.directive('valid', v.directive);

    Vue.prototype.$validator = v;

};

if (window.Vue) {
    Vue.use(module.exports);
}
