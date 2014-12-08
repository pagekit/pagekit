angular.module('tree')

    .controller('indexCtrl', ['$scope', '$http', '$filter', '$timeout', 'Application', 'Pages', 'UIkit', function ($scope, $http, $filter, $timeout, App, Pages, UIkit) {

        var vm = this;

        $scope.pages = App.config.pages;
        $scope.selections = {};

        UIkit.$doc.trigger('uk.domready');

        vm.editPage = function (id) {
            window.location = App.url('/' + id);
        };

        vm.deletePages = function () {
            var pages = $scope.pages;
            angular.forEach($scope.selections, function (page, id) {
                Pages.delete({ id: id }, function () {
                    delete pages[id];
                });
            });
            $scope.selections = {};
        };

        vm.toggleStatus = function (page) {
            Pages.save({ id: page.id }, { page: angular.extend({}, page, { status: !page.status }) }, function (data) {
                angular.extend(page, data.toJSON());
            });
        };

        vm.getChildren = function (id) {
            return $filter('orderBy')($filter('filter')($filter('toArray')($scope.pages), { parentId: id }, true), 'priority');
        };

        UIkit.$doc.on('uk.nestable.change', function () {

            $timeout(function () {
                var pages = [];

                angular.forEach(angular.element('ul.uk-nestable:first li'), function (element, priority) {

                    element = angular.element(element);

                    var page = angular.copy(element.scope().page), parent = element.parent().parent().scope().page;

                    page.priority = priority;
                    page.parentId = parent && parent.id || 0;
                    page.path = (parent && parent.path || '') + '/' + page.slug;

                    pages.push(page);
                });

                $http.post(App.url('/reorder', { pages: JSON.stringify(pages) })).success(function (data) {
                    $scope.pages = data;
                });
            });
        });
    }])

    .controller('editCtrl', ['$scope', 'Application', 'Pages', function ($scope, App, Pages) {

        var vm = this, page = $scope.page = App.config.page;

        vm.getPath = function () {
            return (page.path || '').replace(/^((.*)\/[^/]*)?$/, '$2/' + (page.slug || ''));
        };

        vm.save = function () {
            Pages.save({ id: page.id }, { page: page }, function (data) {
                $scope.page = page = data.toJSON();
            });
        };

    }]);
