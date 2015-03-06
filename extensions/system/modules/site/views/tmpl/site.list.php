<?php $app['sections']->start('toolbar', 'show') ?>
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
<?php $app['sections']->end() ?>

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
        <div>
            {{ menu.label }}
            <a ng-click="vm.editMenu(menu)" ng-if="!menu.fixed"><i class="uk-icon-cog"></i></a>
            <a ng-click="vm.deleteMenu(menu)" ng-if="!menu.fixed"><i class="uk-icon-times-circle"></i></a>
        </div>
    </div>

    <div nestable ng-model="tree[menu.id]" group="{{ menu.id }}">
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
<p>
    <a ng-click="vm.createMenu()"><i class="uk-icon-th-list"></i> <?php echo __('Create Menu') ?></a>
</p>

<div id="modal-menu" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-slide">

        <form ng-submit="vm.saveMenu()" name="form" novalidate>

            <p>
                <input class="uk-width-1-1 uk-form-large" type="text" ng-model="menu.label" placeholder="<?php echo __('Enter Menu Name') ?>" required autofocus>
            <p>
            </p>
                <input class="uk-width-1-1 uk-form-large" type="text" ng-model="menu.newId" placeholder="<?php echo __('Enter Menu Slug') ?>" required>
            </p>

            <button class="uk-button uk-button-primary" ng-click="vm.saveMenu()" ng-disabled="form.$invalid"><?php echo __('Save') ?></button>
            <button class="uk-button uk-modal-close"><?php echo __('Cancel') ?></button>

        </form>
    </div>
</div>
