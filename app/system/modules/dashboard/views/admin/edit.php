<?php $view->script('widget-edit', 'system/dashboard:app/edit.js', ['system', 'vue-validator']) ?>

<form id="js-widget-edit" name="form" class="uk-form uk-form-horizontal" v-on="valid: save" v-cloak>

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
        <label for="form-feed-title" class="uk-form-label"><?= __('Title') ?></label>
        <div class="uk-form-controls">
            <input id="form-feed-title" class="uk-form-width-large" type="text" name="widget[title]" v-model="widget.title" v-valid="required">
            <p class="uk-form-help-block uk-text-danger" v-show="form['widget[title]'].invalid">{{ 'Title cannot be blank.' | trans }}</p>
        </div>
    </div>

    <?= $type->renderForm($widget) ?>

</form>
