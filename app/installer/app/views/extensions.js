window.Extensions = _.merge(require('../components/package-manager.js'), {});

jQuery(function () {

    (new Vue(window.Extensions)).$mount('#extensions');

});