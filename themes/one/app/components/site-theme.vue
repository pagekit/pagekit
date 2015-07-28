<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Theme' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form uk-form-horizontal">

        <div class="uk-form-row">
            <span class="uk-form-label">Sidebar</span>
            <div class="uk-form-controls uk-form-controls-text">
                <label><input type="checkbox" v-model="config['sidebar-first']"> Show the sidebar before the content.</label>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-hero-image" class="uk-form-label">Hero Image</label>
            <div class="uk-form-controls">
                <input-image source="{{@ config['hero-image'] }}"></input-image>
                <p class="uk-form-help-block">{{ 'Choose a background image for the hero position.' | trans }}</p>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Theme',
            icon: 'pk-icon-large-code',
            priority: 30
        },

        data: function () {
            return window.$theme;
        },

        methods: {

            save: function(e) {
                e.preventDefault();

                var config = _.omit(this.config, ['positions', 'menus', 'widget']);

                this.$http.post('admin/system/settings/config', {name: this.name, config: config}, function () {
                    UIkit.notify(this.$trans('Settings saved.'), '');
                }).error(function (data) {
                    UIkit.notify(data, 'danger');
                });

            }

        }

    };

    window.Site.components['site-theme'] = module.exports;

</script>
