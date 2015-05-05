<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Site' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-title" class="uk-form-width-large" type="text" v-model="option.site.title">
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-description" class="uk-form-label">{{ 'Description' | trans }}</label>
        <div class="uk-form-controls">
            <textarea id="form-description" class="uk-form-width-large" rows="5" v-model="option.site.description"></textarea>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Maintenance' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" value="1" v-model="option.maintenance.enabled"> {{ 'Put the site offline and show the offline message.' | trans }}</label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-offlinemessage" class="uk-form-label">{{ 'Offline Message' | trans }}</label>
        <div class="uk-form-controls">
            <textarea id="form-offlinemessage" class="uk-form-width-large" placeholder="{{ &quot;We'll be back soon.&quot; | trans }}" rows="5" v-model="option.maintenance.msg"></textarea>
        </div>
    </div>

</template>

<script>

    module.exports = {

        name: 'settings-site',
        label: 'Site',
        priority: 0,

        computed: {

            option: function() {
                return this.$root.$get('options.system');
            }

        }

    };

</script>
