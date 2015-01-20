<div class="uk-form-row" ng-controller="postEditCtrl as vm" ng-cloak>
    <select ng-model="node.data.id" ng-options="id as title for (id, title) in posts">
        <option value=""><?php __('- Select Post -') ?></option>
    </select>
</div>
