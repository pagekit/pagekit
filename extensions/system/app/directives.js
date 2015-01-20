angular.module('site')
    .directive('checkAll', ['$filter', function($filter) {
        return {
            restrict: 'A',
            scope: {
                checkboxes: '=',
                all       : '='
            },
            controller: function($scope, $element) {

                $element.bind('change', function() {
                    $scope.$apply(function() {
                        $scope.checkboxes = $element.is(':checked') ? angular.copy($scope.all) : (angular.isObject($scope.all) ? {} : []);
                    });
                });

                $scope.$watch('checkboxes', function() {

                    var selected = $filter('truthy')($filter('toArray')($scope.checkboxes)),
                        all      = $filter('toArray')($scope.all);

                    $element.prop('indeterminate', selected.length && selected.length !== all.length);
                    $element.prop('checked', all.length && selected.length === all.length);

                }, true);
            }
        };
    }]);
