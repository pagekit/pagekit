<?php $view->script('settings', 'system/user:app/bundle/settings.js', ['vue', 'input-link']) ?>

<div id="settings" class="uk-form uk-form-horizontal" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Settings' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Registration' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="config.registration" value="admin"> {{ 'Disabled' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="config.registration" value="guest"> {{ 'Enabled' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" v-model="config.registration" value="approval"> {{ 'Enabled, but approval is required.' | trans }}</label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-user-verification" class="uk-form-label">{{ 'Verification' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <label><input id="form-user-verification" type="checkbox" v-model="config.require_verification"> {{ 'Require e-mail verification when a guest creates an account.' | trans }}</label>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-redirect" class="uk-form-label">{{ 'Login Redirect' | trans }}</label>
        <div class="uk-form-controls">
           <input-link id="form-redirect" class="uk-form-width-large" :link.sync="config.login_redirect"></input-link>
        </div>
    </div>

</div>
