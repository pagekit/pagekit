angular.module('tree')

    .controller('pageEditCtrl', ['$scope', '$resource', 'Application', function ($scope, $resource, App) {

        var vm = this;

        return $resource(App.url('/:id'), {}, {
            query: { method: 'GET', responseType: 'json' }
        });

    }]);
