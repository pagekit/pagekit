angular.module('site')

    .controller('indexCtrl', ['$scope', '$filter', '$timeout', 'Application', 'Node', 'UIkit', function ($scope, $filter, $timeout, App, Node, UIkit) {

        var vm = this;

        $scope.nodes = Node.query(function() {
            setTypes();
        });
        $scope.selections = {};

        vm.deleteNodes = function () {
            Node.delete({ id: 'bulk', ids: JSON.stringify(Object.keys($scope.selections)) }, function (data) {
                $scope.nodes = data;
            });
            $scope.selections = {};
        };

        vm.makeFrontpage = function () {

            var nodes = $filter('filter')($filter('toArray')($scope.nodes), function(node) {
                    if (node.data['frontpage']) {
                        delete node.data['frontpage'];
                        return true;
                    }
                }),
                node  = $filter('first')($scope.selections);

            node.data = angular.extend($filter('toObject')(node.data), { frontpage: true });
            nodes.push(node);

            vm.bulkSave(nodes);
            $scope.selections = {};
        };

        vm.toggleStatus = function (node) {
            Node.save({ id: node.id }, { node: angular.extend({}, node, { status: !node.status }) }, function (data) {
                angular.extend(node, data);
            });
        };

        vm.getChildren = function (id) {
            return $filter('orderBy')($filter('filter')($filter('toArray')($scope.nodes), { parentId: id }, true), 'priority');
        };

        vm.getNodePath = function(node) {
            return node.data['frontpage'] ? '/' : node.path;
        };

        vm.getNodeUrl = function(node) {
            return App.config.url + vm.getNodePath(node);
        };

        vm.bulkSave = function(nodes) {
            Node.save({ id: 'bulk' }, { nodes: JSON.stringify(nodes) }, function (data) {
                $scope.nodes = data;
            });
        };

        // -TODO- listen to "change", currently "change" gets triggered by checkboxes too
        UIkit.$doc.on('stop.uk.nestable', function () {
            $timeout(function () {
                var nodes = [];

                angular.forEach(angular.element('ul.uk-nestable:first li'), function (element, priority) {

                    element = angular.element(element);

                    var node = angular.copy(element.scope().node), parent = element.parent().parent().scope().node;

                    node.priority = priority;
                    node.parentId = parent && parent.id || 0;
                    node.path = (parent && parent.path || '') + '/' + node.slug;

                    nodes.push(node);
                });

                vm.bulkSave(nodes);
            });
        });

        $scope.$watch('nodes', function() {
            setTypes();
        });

        function setTypes() {
            angular.forEach(($scope.types = angular.copy(App.data.types)), function(type, index) {
                if (type.type == 'mount') {
                    angular.forEach($scope.nodes, function(node) {
                        if (node.type === type.id) {
                            delete $scope.types[index];
                        }
                    });
                }
            });
        }

    }])

    .controller('editCtrl', ['$scope', '$routeParams', 'Application', 'Node', function ($scope, $routeParams, App, Node) {

        var vm = this;

        $scope.node = $routeParams['id'] ? Node.query({ id: $routeParams['id'] }) : new Node({ type: $routeParams['type']});

        $scope.roles = App.data.roles;

        vm.getPath = function () {
            return ($scope.node.path || '').replace(/^((.*)\/[^/]*)?$/, '$2/' + ($scope.node.slug || ''));
        };

        vm.getType = function() {
            return App.data.types[$scope.node.type] || {};
        };

        vm.save = function () {
            Node.save({ id: $scope.node.id }, { node: $scope.node }, function (data) {
                $scope.node = data.toJSON();
            });
        };

    }])

    .controller('aliasEditCtrl', ['$scope', 'Application', 'Node', function ($scope, App, Node) {

        var vm = this;

    }]);
