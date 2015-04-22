var Directive = require('./src/valid');
var Validator = require('./src/validator');
var Validators = require('./src/validators');

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
