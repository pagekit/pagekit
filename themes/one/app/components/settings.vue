<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Theme Settings' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-form uk-form-horizontal">

        <div class="uk-form-row">
            <span class="uk-form-label">Sidebar</span>
            <div class="uk-form-controls uk-form-controls-text">
                <label><input type="checkbox" v-model="package.config['sidebar-first']"> Show the sidebar before the content.</label>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-hero-image" class="uk-form-label">Hero Image</label>
            <div class="uk-form-controls">
                <input-image source="{{@ package.config['hero-image'] }}"></input-image>
                <p class="uk-form-help-block">{{ 'Choose a background image for the hero position.' | trans }}</p>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        props: ['package'],

        settings: true,

        methods: {

            save: function () {
                this.$http.post('admin/system/settings/config', {name: this.package.name, config: this.package.config}, function () {
                    UIkit.notify(this.$trans('Settings saved.'), '');
                }).error(function (data) {
                    UIkit.notify(data, 'danger');
                }).always(function () {
                    this.$parent.close();
                });
            }

        },

        template: __vue_template__

    };

    window.Themes.component('settings-theme', module.exports);

</script>
