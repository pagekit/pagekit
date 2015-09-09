jQuery(function () {

    window.Extensions = _.merge(require('../components/package-manager.js'), {});

    (new Vue(window.Extensions)).$mount('#extensions');

});
