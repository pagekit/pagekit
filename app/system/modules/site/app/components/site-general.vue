<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'General' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form uk-form-horizontal">

        <div class="uk-form-row">
            <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-title" class="uk-form-width-large" name="title" type="text" v-model="config.title" v-validate:required>
                <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Site title cannot be blank.' | trans }}</p>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-logo" class="uk-form-label">{{ 'Logo' | trans }}</label>
            <div class="uk-form-controls uk-form-width-large">
                <input-image :source.sync="config.view.logo"></input-image>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-favicon" class="uk-form-label">{{ 'Favicon' | trans }}</label>
            <div class="uk-form-controls uk-form-width-large">
                <input-image :source.sync="config.icons.favicon"></input-image>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-appicon" class="uk-form-label">{{ 'Appicon' | trans }}</label>
            <div class="uk-form-controls uk-form-width-large">
                <input-image :source.sync="config.icons.appicon"></input-image>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'General',
            icon: 'pk-icon-large-settings',
            priority: 10
        },

        props: ['config']

    }

</script>
