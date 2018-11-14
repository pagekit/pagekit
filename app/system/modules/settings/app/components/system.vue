<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'System' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-storage" class="uk-form-label">{{ 'Storage' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-storage" class="uk-form-width-large" type="text" placeholder="/storage" v-model="$root.config['system/finder'].storage">
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-fileextensions" class="uk-form-label">{{ 'File Extensions' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-fileextensions" class="uk-form-width-large" type="text" v-model="$root.options['system/finder']['extensions']">
            <p class="uk-form-help-block">{{ 'Allowed file extensions for the storage upload.' | trans }}</p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-user-recaptcha-enable" class="uk-form-label">{{ 'Google reCAPTCHA' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input id="form-user-recaptcha-enable" type="checkbox" v-model="$root.options['system/captcha'].recaptcha_enable"> {{ 'Enable for user registration and comments' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed" v-if="$root.options['system/captcha'].recaptcha_enable">
                <input id="form-user-recaptcha-sitekey" class="uk-form-width-large" placeholder="{{ 'Site key' | trans }}" v-model="$root.options['system/captcha'].recaptcha_sitekey">
            </p>
            <p class="uk-form-controls-condensed" v-if="$root.options['system/captcha'].recaptcha_enable">
                <input id="form-user-recaptcha-secret" class="uk-form-width-large" placeholder="{{ 'Secret key' | trans }}" v-model="$root.options['system/captcha'].recaptcha_secret">
            </p>
            <p class="uk-form-help-block">{{ 'Only key pairs for Google reRECAPTCHA V2 Invisible are supported.' | trans }}</p>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Developer' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" value="1" v-model="$root.config.application.debug"> {{ 'Enable debug mode' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" value="1" v-model="$root.config.debug.enabled" :disabled="!sqlite"> {{ 'Enable debug toolbar' | trans }}</label>
            </p>
            <p class="uk-form-help-block" v-if="!sqlite">{{ 'Please enable the SQLite database extension.' | trans }}</p>
            <p class="uk-form-help-block" v-if="$root.config.application.debug || $root.config.debug.enabled">{{ 'Please note that enabling debug mode or toolbar has serious security implications.' | trans }}</p>
        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'System',
            icon: 'pk-icon-large-settings',
            priority: 10
        },

        props: ['config', 'options'],

        data: function () {
            return window.$system;
        }

    };

</script>
