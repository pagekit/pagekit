<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Localization' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-sitelocale" class="uk-form-label">{{ 'Site Locale' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-sitelocale" class="uk-form-width-large" v-model="option.site.locale">
                <option v-for="lang in locales" :value="$key">{{ lang }}</option>
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-adminlocale" class="uk-form-label">{{ 'Admin Locale' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-adminlocale" class="uk-form-width-large" v-model="option.admin.locale">
                <option v-for="lang in locales" :value="$key">{{ lang }}</option>
            </select>
        </div>
    </div>

    <p>{{{ 'Is your language not available? Please help out by translating Pagekit into your own language on %link%.' | trans {link:link} }}}</p>

</template>

<script>

    module.exports = {

        section: {
            label: 'Localization',
            icon: 'pk-icon-large-pin',
            priority: 20
        },

        props: ['config', 'options'],

        data: function () {
            return {locales: window.$system.locales};
        },

        computed: {

            link: function () {
                return '<a href="https://www.transifex.com/pagekit/pagekit-cms/">Transifex</a>';
            },

            option: function () {
                return this.$root.$get('options.system');
            }

        }

    };

</script>
