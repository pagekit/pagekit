<!-- -TODO- temporary fix to hide the empty <li>-->
<li class="uk-nestable-list-item" ng-if="!(vm.getChildren(parent) | length)"></li>

<li class="uk-nestable-list-item" ng-repeat="node in vm.getChildren(parent)" ng-init="children = vm.getChildren(node.id)" ng-class="{ 'uk-parent': children.length }">
    <div class="uk-nestable-item pk-table-fake">

        <div class="pk-table-width-minimum"><div class="uk-nestable-handle"></div></div>
        <div class="pk-table-width-minimum pk-padding-horizontal-remove"><div data-nestable-action="toggle"></div></div>
        <div class="pk-table-width-minimum"><input check-list="selected" value="{{ node.id }}" type="checkbox"></div>
        <div class="pk-table-min-width-100"><a ng-href="#/edit/{{node.id}}">{{ node.title }}</a></div>
        <div class="pk-table-width-100 uk-text-center"><i ng-if="node.data.frontpage" class="uk-icon-home"></i></div>
        <div class="pk-table-width-100 uk-text-center">
            <a ng-class="{ 'uk-text-success': node.status, 'uk-text-danger': !node.status }" class="uk-icon-circle" ng-click="vm.toggleStatus(node)" title="{{ node.status ? 'Enabled' : 'Disabled' }}"></a>
        </div>
        <div class="pk-table-width-150 pk-table-max-width-150 uk-text-truncate">
            <a ng-if="node.status" ng-href="{{ vm.getNodeUrl(node) }}" target="_blank">{{ vm.getNodePath(node) }}</a>
            <span ng-if="!node.status">{{ vm.getNodePath(node) }}</span>
        </div>

    </div>

    <ul ng-if="children.length" class="uk-nestable-list" ng-include="'site.item'" ng-init="parent = node.id"></ul>
</li>
