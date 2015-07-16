<template>

    <div class="uk-form uk-form-horizontal">

        <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
            <div data-uk-margin>

                <h2 class="uk-margin-remove">{{ 'Theme Settings' | trans }}</h2>

            </div>
            <div data-uk-margin>

                <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-sidebar-a-width" class="uk-form-label">{{ 'Sidebar A Width' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-sidebar-a-width" class="uk-form-width-large" v-model="package.config.sidebars['sidebar-a']['width']">
                    <option v-repeat="widths" value="{{ $key }}">{{ '%percent% %' | trans {percent:$value} }}</option>
                </select>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-sidebar-b-width" class="uk-form-label">{{ 'Sidebar B Width' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-sidebar-b-width" class="uk-form-width-large" v-model="package.config.sidebars['sidebar-b']['width']">
                    <option v-repeat="widths" value="{{ $key }}">{{ '%percent% %' | trans {percent:$value} }}</option>
                </select>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        props: ['package'],

        settings: true,

        data: function() {
            return {
                widths: {12: '20', 15: '25', 18: '30', 20: '33', 24: '40', 30: '50'}
            };
        },

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

    window.Themes.component('settings-one', module.exports);

</script>
