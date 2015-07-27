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
        <label for="form-apikey" class="uk-form-label">{{ 'API Key' | trans }}</label>
        <div class="uk-form-controls">
            <textarea id="form-apikey" class="uk-form-width-large" placeholder="{{ 'Enter your API key' | trans }}" rows="6" v-model="$root.options['system/package'].api.key"></textarea>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-channel" class="uk-form-label">{{ 'Release Channel' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-channel" class="uk-form-width-large" v-model="$root.options['system/package'].release_channel">
                <option value="stable">{{ 'Stable' | trans }}</option>
                <option value="nightly">{{ 'Nightly' | trans }}</option>
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-uploadfolder" class="uk-form-label">{{ 'Storage' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-uploadfolder" class="uk-form-width-large" type="text" placeholder="/storage" v-model="$root.config['system/finder'].storage">
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Developer' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" value="1" v-model="$root.config.application.debug"> {{ 'Enable debug mode' | trans }}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" value="1" v-model="$root.config.debug.enabled" v-attr="disabled: !sqlite"> {{ 'Enable debug toolbar' | trans }}</label>
            </p>
            <p class="uk-form-help-block" v-if="!sqlite">{{ 'Please enable the SQLite database extension.' | trans }}</p>
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

        data: function() {
            return window.$system;
        }

    };

</script>
