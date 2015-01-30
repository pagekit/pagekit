angular.module('Application')

    .directive('nestable', ['$compile', '$timeout', 'UIkit', function ($compile, $timeout, UIkit) {
        return {
            restrict: 'A',
            scope   : {
                model: '=ngModel',
                group: '@'
            },
            compile : function (element) {

                var tpl = element.html();

                element.empty();

                return function ($scope, $element, $attrs) {

                    $scope.$watch('model', function (model) {

                        if (!model) return;

                        var root = getHtml(model, angular.element('<ul class="uk-nestable"></ul>'), tpl);
                        $element.empty().append(root);
                        $compile(root)($scope.$parent);
                        var nestable = UIkit.nestable(root);

                        root.on('change.uk.nestable', function () {
                            $timeout(function () {
                                model = nestable.serialize();
                                $scope.$emit('change.nestable', nestable.list());
                            });
                        });
                    }, true);
                };
            }
        };

        function getHtml(model, list, tpl) {
            model.forEach(function (item) {
                var listItem = angular.element('<li nestable-item class="uk-nestable-list-item" data-id="{{ node.id }}"></li>').append(tpl).data('node', item.node);

                var children = item['children'];
                if (angular.isArray(children) && children.length) {
                    getHtml(children, angular.element('<ul class="uk-nestable-list"></ul>'), tpl).appendTo(listItem);
                }
                listItem.appendTo(list);
            });

            return list;
        }
    }])

    .directive('nestableItem', function () {
        return {
            restrict: 'A',
            scope   : true,
            link    : function ($scope, $element) {
                $scope['node'] = $element.data('node');
            }
        };
    })


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
            restrict  : 'A',
            scope     : {
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
    }])

    .directive('autofocus', ['$timeout', function($timeout) {

        var elements = [];

        return {
            restrict: 'A',
            link: function($scope, element) {
                elements.push(element);
                $timeout(function() {
                    elements[0][0].focus();
                });
            }
        };
    }]);
