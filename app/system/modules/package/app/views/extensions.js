jQuery(function () {

    var PackageManager = require('../components/package-manager.js');

    (new Vue(PackageManager)).$mount('#extensions');

});
