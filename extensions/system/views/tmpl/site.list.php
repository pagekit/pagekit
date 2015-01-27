<?php $this['sections']->start('toolbar', 'show') ?>
<div class="uk-float-left">

    <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
        <button class="uk-button uk-button-primary" type="button"><?php echo __('Add') ?></button>
        <div class="uk-dropdown uk-dropdown-small">
            <ul class="uk-nav uk-nav-dropdown">
                <li ng-repeat="type in types" ng-if="!vm.isMounted(type)"><a ng-href="#/create/{{ type.id }}">{{ type.label }}</a></li>
            </ul>
        </div>
    </div>

    <a class="uk-button pk-button-danger" ng-click="vm.deleteNodes()" ng-show="selected.length"><?php echo __('Delete') ?></a>
    <a class="uk-button" ng-click="vm.makeFrontpage()" ng-show="selected.length === 1"><?php echo __('Make Frontpage') ?></a>

</div>
<?php $this['sections']->end() ?>

<div class="uk-overflow-container">

    <div class="pk-table-fake pk-table-fake-header pk-table-fake-header-indent-nested">
        <div class="pk-table-width-minimum"><input type="checkbox" check-all="nodes" check-selected="selected"></div>
        <div class="pk-table-min-width-100">Title</div>
        <div class="pk-table-width-100 uk-text-center"></div>
        <div class="pk-table-width-100 uk-text-center">Status</div>
        <div class="pk-table-width-150">URL</div>
    </div>

</div>

<div ng-repeat="menu in menus">
    <div class="pk-table-fake pk-table-fake-header pk-table-fake-subheading">
        <div>{{ menu || 'Not Linked' }}</div>
    </div>

    <div nestable ng-model="tree[menu]" group="{{ menu }}">
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
    </div>
</div>
