<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Meta' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form uk-form-horizontal">

        <div class="uk-form-row">
            <label for="form-meta-description" class="uk-form-label">{{ 'Description' | trans }}</label>
            <div class="uk-form-controls uk-form-width-large">
                <textarea id="form-meta-description" class="uk-form-width-large" rows="5" v-model="config.meta.description"></textarea>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-meta-image" class="uk-form-label">{{ 'Image' | trans }}</label>
            <div class="uk-form-controls uk-form-width-large">
                <input-image :source.sync="config.meta.image"></input-image>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-meta-facebook" class="uk-form-label">Facebook</label>
            <div class="uk-form-controls uk-form-width-large">
                <input id="form-meta-facebook" type="text" class="uk-form-width-large" placeholder="app_id" v-model="config.meta.facebook"></input>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-meta-twitter" class="uk-form-label">Twitter</label>
            <div class="uk-form-controls">
                <input id="form-meta-twitter" type="text" class="uk-form-width-large" placeholder="@username" v-model="config.meta.twitter"></input>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Meta',
            icon: 'pk-icon-large-meta',
            priority: 50
        },

        props: ['config']

    }

</script>
