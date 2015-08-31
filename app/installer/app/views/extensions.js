jQuery(function () {

    var Component = _.merge(require('../components/package-manager.js'), {});

    (new Vue(Component)).$mount('#extensions');

});
