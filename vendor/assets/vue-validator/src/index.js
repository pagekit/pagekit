var Directive = require('./valid');
var Validator = require('./validator');
var Validators = require('./validators');

/**
 * Install plugin.
 */

function install (Vue) {

    Vue.validators = Validators;
    Vue.directive('valid', Directive);

    Vue.prototype.$validator = Validator;

}

if (window.Vue) {
    Vue.use(install);
}

module.exports = install;
