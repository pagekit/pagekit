window.Themes = _.merge(
    require('../components/package-manager.js'),
    {
        name: 'themes',

        el: '#themes',

        filters: {
            themeorder: function (packages) {

                var index = packages.indexOf(_.find(packages, {enabled: true}));

                if (index !== -1) {
                    packages.splice(0, 0, packages.splice(index, 1)[0]);
                }

                return packages;
            }
        }

    }
);

Vue.ready(window.Themes);
