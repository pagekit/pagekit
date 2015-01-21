angular.module('Application')

    .directive('checkList', function () {
        return {
            restrict  : 'A',
            scope     : {
                list : '=checkList',
                value: '@'
            },
            controller: function ($scope, $element) {

                $element.on('change', function () {
                    $scope.$apply(function () {
                        var checked = $element.prop('checked'), index = $scope.list.indexOf($scope.value);

                        if (checked && index == -1) {
                            $scope.list.push($scope.value);
                        } else if (!checked && index != -1) {
                            $scope.list.splice(index, 1);
                        }
                    });
                });

                $scope.$watch('list', function () {
                    $element.prop('checked', -1 !== $scope.list.indexOf($scope.value));
                }, true);
            }
        };
    })

    .directive('checkAll', ['$filter', function ($filter) {
        return {
            restrict: 'A',
            scope   : {
                all     : '=checkAll',
                selected: '=checkSelected'
            },
            controller: function ($scope, $element) {

                $element.on('change', function () {
                    $scope.$apply(function () {
                        $scope.selected = $element.is(':checked') ? Object.keys($scope.all).filter($filter('number')) : [];
                    });
                });

                $scope.$watch('selected', function () {
                    var selected = $filter('length')($scope.selected),
                        all = $filter('length')($scope.all);

                    $element.prop('indeterminate', selected && selected !== all);
                    $element.prop('checked', all && selected === all);

                }, true);
            }
        };
    }]);
