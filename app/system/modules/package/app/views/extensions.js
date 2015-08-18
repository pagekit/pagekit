jQuery(function () {

    var Component = _.merge(require('../components/package-manager.js'),
        {
            filters: {
                folder: function (pkg) {
                    if (pkg.url) {
                        return pkg.url.match(/[^\/]+$/gi);
                    }
                }
            }
        });

    (new Vue(Component)).$mount('#extensions');

});
