<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Theme' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form uk-form-horizontal">

        <div class="uk-form-row">
            <label class="uk-form-label">{{ 'Logo Contrast' | trans }}</label>
            <div class="uk-form-controls uk-form-width-large">
                <input-image source="{{@ config.logo_contrast }}"></input-image>
                <p class="uk-form-help-block">{{ 'Select an alternative logo which looks great on images.' | trans }}</p>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Theme',
            icon: 'pk-icon-large-brush',
            priority: 15
        },

        data: function () {
            return window.$theme;
        },

        events: {

            save: function() {

                var config = _.omit(this.config, ['positions', 'menus', 'widget']);

                this.$http.post('admin/system/settings/config', {name: this.name, config: config}).error(function (data) {
                    this.$notify(data, 'danger');
                });

            }

        }

    };

    window.Site.components['site-theme'] = module.exports;

</script>
