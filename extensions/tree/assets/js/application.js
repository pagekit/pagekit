(function(angular, global) {

    angular.module('Application', [])

        .factory('Application', ['$window', 'name', function($window, name) {

            return angular.extend({

                url: function(cmd, params) {

                    var query = [], url = this.config.route + cmd;

                    angular.forEach(params || {}, function(val, key) {
                        query.push(key + '=' + val);
                    });

                    if (query.length) {
                        url += (url.indexOf('?') != -1 ? '&' : '?') + query.join('&');
                    }

                    return url;
                },

                baseUrl: function() {
                    return this.config.url;
                },

                templateUrl: function(name) {
                    var url = this.config['url.template'];

                    return url += (url.indexOf('?') != -1 ? '&' : '?') + 'name=' +name;
                }

            }, $window[name]);

        }])

        .filter('first', ['$filter', function($filter) {
            return function(collection) {
                return $filter('toArray')(collection)[0];
            };
        }])

        .filter('length', ['$filter', function($filter) {
            return function(collection) {
                return $filter('toArray')(collection).length;
            };
        }])

        .filter('toArray', function() {
            return function(collection) {

                if (angular.isObject(collection)) {
                    return Object.keys(collection)

                        .filter(function(key) {
                            return key.charAt(0) !== '$';
                        })

                        .map(function(key) {
                            return collection[key];
                        });
                }

                return angular.isArray(collection) ? collection : [];
            };
        })

        .config(['$provide', function($provide) {

            $provide.decorator('$templateCache', ['$delegate', 'Application', function($delegate, App) {

                angular.forEach(App.templates || [], function(tpl, name) {
                    $delegate.put(name, tpl);
                });

                return $delegate;
            }]);

            $provide.decorator('$templateRequest', ['$delegate', 'Application', function($delegate, App) {

                return function(tpl, ignoreRequestError) {

                    if (!App.templates || !App.templates[tpl]) {
                        tpl = App.templateUrl(tpl);
                    }

                    return $delegate(tpl, ignoreRequestError);
                };
            }]);

        }]);

    angular.element(global.document).ready(function() {

        var apps = angular.element(this).find('[data-app]');

        angular.forEach(apps, function(app) {

            var name = angular.element(app).data('app');

            if (global[name]) {
                angular.bootstrap(app, [name]);
            }
        });

    });

})(angular, window);
