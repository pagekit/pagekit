angular.module('site')

    .controller('indexCtrl', ['$scope', '$filter', '$timeout', 'Application', 'Node', 'UIkit', function ($scope, $filter, $timeout, App, Node, UIkit) {

        var vm = this;

        $scope.nodes = Node.query();
        $scope.selected = [];
        $scope.types = App.data.types;

        vm.deleteNodes = function () {
            Node.delete({ id: 'bulk', ids: JSON.stringify($scope.selected) }, function (data) {
                $scope.nodes = data;
            });
            $scope.selected = [];
        };

        vm.makeFrontpage = function () {
            bulkSave($filter('filter')($filter('toArray')($scope.nodes), function(node) {
                if (node.data['frontpage']) {
                    delete node.data['frontpage'];
                    return true;
                }

                if (node.id === $scope.selected[0]) {
                    node.data = angular.extend($filter('toObject')(node.data), { frontpage: true });
                    return true;
                }
            }));
            $scope.selected = [];
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

        vm.isMounted = function(type) {
            return type.type === 'mount' && $filter('filter')($filter('toArray')($scope.nodes), { type: type.id }, true).length;
        };

        function bulkSave(nodes) {
            Node.save({ id: 'bulk' }, { nodes: JSON.stringify(nodes) }, function (data) {
                $scope.nodes = data;
            });
        }

        // -TODO- listen to "change", currently "change" gets triggered by checkboxes too
        UIkit.$doc.on('stop.uk.nestable', 'ul.uk-nestable:first', function () {
            var list = angular.element(this);
            $timeout(function () {
                var nodes = [];

                list.find('li').each(function(priority, element) {

                    var elem = angular.element(element), node = elem.scope().node, parent = elem.parent().parent().scope().node;

                    node.priority = priority;
                    node.parentId = parent && parent.id || 0;
                    node.path = (parent && parent.path || '') + '/' + node.slug;

                    nodes.push(node);
                });

                bulkSave(nodes);
            });
        });
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
