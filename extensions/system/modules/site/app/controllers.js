angular.module('site')

    .controller('indexCtrl', ['$scope', '$filter', '$timeout', 'Application', 'Node', 'Menu', 'UIkit', function ($scope, $filter, $timeout, App, Node, Menu, UIkit) {

        var vm = this;

        $scope.nodes = Node.query();
        $scope.menus = Menu.query();
        $scope.types = angular.copy(App.data.types);
        $scope.selected = [];

        vm.deleteNodes = function () {
            Node.delete({ id: 'bulk', ids: JSON.stringify($scope.selected) });
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
                tree[menu.id] = [];
            });

            angular.forEach($filter('orderBy')($filter('toArray')($scope.nodes), 'priority'), function (node) {

                var menu = tree[node.menu] || tree[''];

                parent = !node.parentId && menu || (nodes[node.parentId] = nodes[node.parentId] || { node: $scope.nodes[node.parentId], children: [] }).children;

                parent.push(nodes[node.id] = nodes[node.id] || { node: node, children: [] });
            });

            $scope.tree = tree;
        }

        $scope.$watch('nodes', function () {
            updateTree();
        }, true);

        $scope.$watch('menus', function () {
            updateTree();
        }, true);

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
        vm.createMenu = function () {
            vm.editMenu(new Menu);
        };

        vm.editMenu = function (menu) {
            $scope.menu = menu;
            menu.newId = menu.id;
            modal = UIkit.modal('#modal-menu').show();
        };

        vm.saveMenu = function () {
            var menu = $scope.menu, newId = menu.newId, oldId = menu.id;

            if (!newId) return;

            if (oldId) {
                $scope.menu.$update({ id: oldId }, function() {

                    if (menu.id == oldId) return;

                    $filter('filter')($filter('toArray')($scope.nodes), { menu: oldId }).forEach(function(node) {
                        node.menu = menu.id;
                    });
                });
            } else {
                menu.$save({ id: newId });
                $scope.menus.splice(-1, 0, menu);
            }
            modal.hide();
        };

        vm.deleteMenu = function (menu) {
            menu.$delete({ id: menu.id });
            $scope.menus.splice($scope.menus.lastIndexOf(menu), 1);
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
