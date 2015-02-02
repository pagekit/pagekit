<div class="uk-form-row" ng-controller="pageEditCtrl as vm" ng-cloak>
    <div class="uk-form-row">
        <label for="form-page-title" class="uk-form-label"><?php echo __('Page Title') ?></label>

        <div class="uk-form-controls">
            <input id="form-page-title" class="uk-form-width-large" type="text" ng-model="node.data.page.title">
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-url" class="uk-form-label"><?php echo __('Content') ?></label>

        <div class="uk-form-controls">
            <textarea ng-model="node.data.page.content"></textarea>
        </div>
    </div>
</div>
