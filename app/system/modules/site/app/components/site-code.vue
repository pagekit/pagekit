<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Code' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <p>{{ 'Insert code in the header and footer of your theme. This is useful for tracking codes and meta tags.' | trans }}</p>

    <div class="uk-form uk-form-stacked">
        <div class="uk-form-row">
            <label for="form-codeheader" class="uk-form-label">{{ 'Header' | trans }}</label>
            <div class="uk-form-controls">
                <v-editor type="code" :value.sync="config.code.header"></v-editor>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-codeheader" class="uk-form-label">{{ 'Footer' | trans }}</label>
            <div class="uk-form-controls">
                <v-editor type="code" :value.sync="config.code.footer"></v-editor>
            </div>
        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Code',
            icon: 'pk-icon-large-code',
            priority: 20
        },

        props: ['config']

    }

</script>
