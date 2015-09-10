var Directive = require('./valid');
var Validator = require('./validator');
var Validators = require('./validators');

/**
 * Install plugin.
 */

module.exports = function (Vue) {

    Vue.validators = Validators;
    Vue.directive('valid', Directive);

    Vue.prototype.$validator = Validator;

};

if (window.Vue) {
    Vue.use(module.exports);
}
