angular.module('tree')

    .controller('indexCtrl', ['$scope', '$http', '$filter', '$timeout', 'Application', 'Nodes', 'UIkit', function ($scope, $http, $filter, $timeout, App, Nodes, UIkit) {

        var vm = this;

        $scope.nodes = App.data.nodes;
        $scope.selections = {};

        vm.editNode = function (id) {
            window.location = App.url('/' + id);
        };

        vm.deleteNodes = function () {
            $http.delete(App.url('/bulk', { nodes: JSON.stringify($scope.selections) })).success(function (data) {
                $scope.nodes = data;
            });
            $scope.selections = {};
        };

        vm.makeHomepage = function () {

            var nodes = $filter('filter')($filter('toArray')($scope.nodes), function(node) {
                    if (node.data['homepage']) {
                        delete node.data['homepage'];
                        return true;
                    }
                }),
                node  = $filter('first')($scope.selections);

            node.data = angular.extend($filter('toObject')(node.data), { homepage: true });
            nodes.push(node);

            vm.bulkSave(nodes);
            $scope.selections = {};
        };

        vm.toggleStatus = function (node) {
            Nodes.save({ id: node.id }, { node: angular.extend({}, node, { status: !node.status }) }, function (data) {
                angular.extend(node, data);
            });
        };

        vm.getChildren = function (id) {
            return $filter('orderBy')($filter('filter')($filter('toArray')($scope.nodes), { parentId: id }, true), 'priority');
        };

        vm.getNodePath = function(node) {
            return node.data['homepage'] ? '/' : node.path;
        };

        vm.getNodeUrl = function(node) {
            return App.config.url + vm.getNodePath(node);
        };

        vm.bulkSave = function(nodes) {
            $http.post(App.url('/bulk', { nodes: JSON.stringify(nodes) })).success(function (data) {
                $scope.nodes = data;
            });
        };

        $scope.$watch('nodes', function() {
            angular.forEach(($scope.types = angular.copy(App.data.types)), function(type, index) {
                if (type.type == 'mount') {
                    angular.forEach($scope.nodes, function(node) {
                        if (node.type === type.id) {
                            delete $scope.types[index];
                        }
                    });
                }
            });
        });

        UIkit.$doc.on('uk.nestable.change', function () {

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
    }])

    .controller('editCtrl', ['$scope', 'Application', 'Nodes', function ($scope, App, Nodes) {

        var vm = this, node = $scope.node = App.data.node;

        $scope.type  = App.data.type;
        $scope.roles = App.data.roles;

        vm.getPath = function () {
            return (node.path || '').replace(/^((.*)\/[^/]*)?$/, '$2/' + (node.slug || ''));
        };

        vm.save = function () {
            Nodes.save({ id: node.id }, { node: node }, function (data) {
                $scope.node = node = data.toJSON();
            });
        };

    }])

    .controller('aliasEditCtrl', ['$scope', 'Application', 'Nodes', function ($scope, App, Nodes) {

        var vm = this;

    }]);
