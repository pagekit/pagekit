<h2></h2>

<form class="uk-form uk-form-stacked">

    <?php $app['sections']->start('toolbar', 'show') ?>
        <button ng-click="vm.save()" class="uk-button uk-button-primary"><?php echo __('Save') ?></button>
        <a class="uk-button js-cancel" ng-href="#/">{{ node.id ? "<?php echo __('Close') ?>" : "<?php echo __('Cancel') ?>" }}</a>
    <?php $app['sections']->end() ?>

    <div class="uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match>

        <div class="uk-width-medium-3-4 uk-form-horizontal">

            <div class="uk-form-row">
                <label for="form-title" class="uk-form-label"><?php echo __('Title') ?></label>
                <div class="uk-form-controls">
                    <input id="form-title" class="uk-form-width-large" type="text" ng-model="node.title" required>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-slug" class="uk-form-label"><?php echo __('Slug') ?></label>
                <div class="uk-form-controls">
                    <span>{{ vm.getPath() }}</span><br>
                    <input id="form-slug" class="uk-form-width-large" type="text" ng-model="node.slug">
                </div>
            </div>

            <div class="uk-form-row">
                <span class="uk-form-label"><?php echo __('Restrict Access') ?></span>
                <div ng-repeat="role in roles" class="uk-form-controls">
                    <label><input type="checkbox" ng-model="node.roles[role.id]" ng-value="{{ role.id }}"> {{ role.name }}</label>
                </div>
            </div>

            <div class="uk-form-row" ng-include="vm.getType()['tmpl.edit']"></div>

        </div>

    </div>

</form>
