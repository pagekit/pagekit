<template>

    <div class="uk-form uk-form-horizontal">

        <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
            <div data-uk-margin>

                <h2 class="uk-margin-remove">{{ 'Hello Settings' | trans }}</h2>

            </div>
            <div data-uk-margin>

                <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-default" class="uk-form-label">{{ 'Default Name' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-default" class="uk-form-width-large" type="text" v-model="package.config.default">
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
                this.$http.post('admin/system/settings/config', { name: this.package.name, config: this.package.config }, function () {
                    UIkit.notify(this.$trans('Settings saved.'), '');
                }).error(function (data) {
                    UIkit.notify(data, 'danger');
                }).always(function() {
                    this.$parent.close();
                });
            }

        },

        template: __vue_template__

    };

    window.Extensions.component('settings-hello', module.exports);

</script>
