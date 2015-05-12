<?php $view->script('dashboard-widget-edit', 'system/dashboard:app/bundle/admin/edit.js', ['system', 'vue-validator', 'uikit-autocomplete']) ?>

<form id="widget-edit" name="form" class="uk-form uk-form-horizontal" v-on="valid: save" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ widget.id ? $trans('Edit Widget') : $trans('Add Widget') }} ({{ type.name }})</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
            <a class="uk-button" v-attr="href: $url('admin/dashboard/settings')">{{ widget.id ? 'Close' : 'Cancel' | trans }}</a>

        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-feed-title" class="uk-form-label">{{ 'Title' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-feed-title" class="uk-form-width-large" type="text" name="widget[title]" v-model="widget.title" v-valid="required">
            <p class="uk-form-help-block uk-text-danger" v-show="form['widget[title]'].invalid">{{ 'Title cannot be blank.' | trans }}</p>
        </div>
    </div>

    <div class="uk-form-row" v-repeat="section: sections | active | orderBy 'priority'">
        <div v-component="{{ section.name }}" v-with="widget: widget"></div>
    </div>

</form>
