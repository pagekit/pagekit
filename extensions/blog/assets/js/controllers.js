angular.module('tree')

    .controller('postEditCtrl', ['$scope', '$resource', 'Application', function ($scope, $resource, App) {

        var vm = this;

        $scope.posts = App['blog-config']['posts'];

        $scope.$watch('node.data.id', function(id) {
            $scope.node.data = $scope.node.data || {};
            $scope.node.data.url = '@blog/id?id='+id;
        });

    }]);
