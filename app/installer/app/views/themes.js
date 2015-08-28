jQuery(function () {

    var Component = _.merge(require('../components/package-manager.js'),
        {
            filters: {
                themeorder: function (packages) {

                    var index = packages.indexOf(_.find(packages, {enabled: true}));

                    if (index !== -1) {
                        packages.splice(0, 0, packages.splice(index, 1)[0]);
                    }

                    return packages;
                }
            }
        });

    (new Vue(Component)).$mount('#themes');

});
