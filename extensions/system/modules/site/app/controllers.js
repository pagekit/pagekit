angular.module('site')

    .controller('indexCtrl', ['$scope', '$filter', '$timeout', 'Application', 'Node', 'UIkit', function ($scope, $filter, $timeout, App, Node, UIkit) {

        var vm = this;

        $scope.nodes = Node.query();
        $scope.selected = [];
        $scope.types = angular.copy(App.data.types);
        $scope.menus = angular.copy(App.data.menus);
        $scope.menus.push('');

        vm.deleteNodes = function () {
            Node.delete({ id: 'bulk', ids: JSON.stringify($scope.selected) }, function (data) {
                $scope.nodes = data;
            });
            $scope.selected = [];
        };

        vm.makeFrontpage = function () {
            bulkSave($filter('toArray')($scope.nodes).filter(function (node) {
                if (node.data['frontpage']) {
                    delete node.data['frontpage'];
                    return true;
                }

                if (node.id == $scope.selected[0]) {
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

        vm.getNodePath = function (node) {
            return node.data['frontpage'] ? '/' : node.path;
        };

        vm.getNodeUrl = function (node) {
            return App.config.url + vm.getNodePath(node);
        };

        vm.isMounted = function (type) {
            return type.type === 'mount' && $filter('filter')($filter('toArray')($scope.nodes), { type: type.id }, true).length;
        };

        function bulkSave(nodes) {
            Node.save({ id: 'bulk' }, { nodes: JSON.stringify(nodes) });
        }

        function updateTree() {
            var tree = {}, nodes = {};

            angular.forEach($scope.menus, function (menu) {
                tree[menu] = [];
            });

            angular.forEach($filter('orderBy')($filter('toArray')($scope.nodes), 'priority'), function (node) {

                var menu = tree[node.menu];

                if (!menu) {
                    $scope.menus.splice(-1, 0, node.menu);
                    return;
                }

                parent = !node.parentId && menu || (nodes[node.parentId] = nodes[node.parentId] || { node: $scope.nodes[node.parentId], children: [] }).children;

                parent.push(nodes[node.id] = nodes[node.id] || { node: node, children: [] });
            });

            $scope.tree = tree;
        }

        $scope.$watch('nodes', function () {
            updateTree();
        }, true);

        $scope.$watchCollection('menus', function () {
            updateTree();
        });

        var debounce;
        $scope.$on('change.nestable', function (event, items) {

            items.forEach(function (item) {

                var node = item.node, parent = $scope.nodes[item.parent_id];

                node.priority = item.order;
                node.parentId = item.parent_id || 0;
                node.path = (parent && parent.path || '') + '/' + node.slug;
                node.menu = event.targetScope.group;
            });

            $timeout.cancel(debounce);
            debounce = $timeout(function () {
                bulkSave($scope.nodes);
            }, 100);
        });

        var modal;
        vm.openModal = function() {
            $scope.menu = '';
            modal = UIkit.modal('#modal-menu').show();
        };

        vm.addMenu = function() {
            $scope.menus.splice(-1, 0, $scope.menu);
            modal.hide();
        }
    }])

    .controller('editCtrl', ['$scope', '$routeParams', 'Application', 'Node', function ($scope, $routeParams, App, Node) {

        var vm = this;

        $scope.node = $routeParams['id'] ? Node.get({ id: $routeParams['id'] }) : new Node({ type: $routeParams['type'] });

        $scope.roles = App.data.roles;

        vm.getPath = function () {
            return ($scope.node.path || '').replace(/^((.*)\/[^/]*)?$/, '$2/' + ($scope.node.slug || ''));
        };

        vm.getType = function () {
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
