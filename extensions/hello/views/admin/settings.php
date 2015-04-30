<?php $view->script('settings', 'hello:assets/js/settings.js', 'system') ?>

<div id="js-settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Edit Settings' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Default name' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" v-model="config.default">
        </div>
    </div>

</div>