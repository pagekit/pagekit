angular.module('tree')

    .controller('indexCtrl', ['$scope', '$http', '$filter', '$timeout', 'Application', 'Nodes', 'UIkit', function ($scope, $http, $filter, $timeout, App, Nodes, UIkit) {

        var vm = this;

        $scope.nodes = App.config.nodes;
        $scope.selections = {};

        UIkit.$doc.trigger('uk.domready');

        vm.editNode = function (id) {
            window.location = App.url('/' + id);
        };

        vm.deleteNodes = function () {
            var nodes = $scope.nodes;
            angular.forEach($scope.selections, function (node, id) {
                Nodes.delete({ id: id }, function () {
                    delete nodes[id];
                });
            });
            $scope.selections = {};
        };

        vm.toggleStatus = function (node) {
            Nodes.save({ id: node.id }, { node: angular.extend({}, node, { status: !node.status }) }, function (data) {
                angular.extend(node, data.toJSON());
            });
        };

        vm.getChildren = function (id) {
            return $filter('orderBy')($filter('filter')($filter('toArray')($scope.nodes), { parentId: id }, true), 'priority');
        };

        vm.getNodeUrl = function(node) {
            return node.path.substring(1);
        };

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

                $http.post(App.url('/reorder', { nodes: JSON.stringify(nodes) })).success(function (data) {
                    $scope.nodes = data;
                });
            });
        });
    }])

    .controller('editCtrl', ['$scope', 'Application', 'Nodes', function ($scope, App, Nodes) {

        var vm = this, node = $scope.node = App.config.node;

        vm.getPath = function () {
            return (node.path || '').replace(/^((.*)\/[^/]*)?$/, '$2/' + (node.slug || ''));
        };

        vm.save = function () {
            Nodes.save({ id: node.id }, { node: node }, function (data) {
                $scope.node = node = data.toJSON();
            });
        };

    }]);
