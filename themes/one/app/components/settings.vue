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


        <h2>{{ 'Sidebars' | trans }}</h2>

        <p>{{ 'Choose the grid layout for each position. Further, you can enable horizontal dividers and prevent the responsive grid behavior. Note: Both options are not taken into account for the stacked layout.' | trans }}</p>

        <table class="uk-table uk-table-middle tm-width">
            <thead>
            <tr>
                <th>{{ 'Position' | trans }}</th>
                <th>{{ 'Width' | trans }}</th>
                <th>{{ 'First' | trans }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-repeat="package.config.sidebars">
                <td>{{ $key }}</td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <select v-model="width">
                            <option value="{{ $key }}" v-repeat="{12: '20', 15: '25', 18: '30', 20: '33', 24: '40', 30: '50'}">{{ '%percent% %' | trans {percent:$value} }}</option>
                        </select>
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <input type="checkbox" v-model="first">
                    </p>
                </td>
            </tr>
            </tbody>
        </table>

        <h2>{{ 'Block Appearance' | trans }}</h2>

        <p>{{ 'Choose the appearance settings for each block position.' | trans }}</p>

        <table class="uk-table uk-table-middle tm-width">
            <thead>
            <tr>
                <th>{{ 'Position' | trans }}</th>
                <th>{{ 'Background' | trans }}</th>
                <th>{{ 'Image' | trans }}</th>
                <th>{{ 'White' | trans }}</th>
                <th>{{ 'Full Width' | trans }}</th>
                <th>{{ 'Full Height' | trans }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-repeat="package.config.blocks">
                <td>{{ $key }}</td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <select v-model="background">
                            <option value="default">{{ 'Default' | trans }}</option>
                            <option value="muted">{{ 'Muted' | trans }}</option>
                            <option value="primary">{{ 'Primary' | trans }}</option>
                            <option value="secondary">{{ 'Secondary' | trans }}</option>
                        </select>
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <input type="text" v-model="image">
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <input type="checkbox" v-model="contrast">
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <select v-model="padding">
                            <option value="">{{ 'Default' | trans }}</option>
                            <option value="large">{{ 'Large' | trans }}</option>
                            <option value="none">{{ 'None' | trans }}</option>
                        </select>
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <input type="checkbox" v-model="width">
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <input type="checkbox" v-model="height">
                    </p>
                </td>
            </tr>
            </tbody>
        </table>

        <h2>{{ 'Grid' | trans }}</h2>

        <p>{{ 'Choose the grid layout for each position. Further, you can enable horizontal dividers and prevent the responsive grid behavior. Note: Both options are not taken into account for the stacked layout.' | trans }}</p>

        <table class="uk-table uk-table-middle tm-width">
            <thead>
            <tr>
                <th>{{ 'Position' | trans }}</th>
                <th>{{ 'Layout' | trans }}</th>
                <th>{{ 'Responsive' | trans }}</th>
                <th>{{ 'Divider' | trans }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-repeat="package.config.grid">
                <td>{{ $key }}</td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <select v-model="layout">
                            <option value="parallel">{{ 'Parallel' | trans }}</option>
                            <option value="stacked">{{ 'Stacked' | trans }}</option>
                            <option value="doubled">{{ 'First doubled' | trans }}</option>
                            <option value="doubled-last">{{ 'Last doubled' | trans }}</option>
                        </select>
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <select v-model="responsive">
                            <option value="">{{ 'Disabled' | trans }}</option>
                            <option value="medium">{{ 'Stack on phones' | trans }}</option>
                            <option value="large">{{ 'Stack on ≤ tablets' | trans }}</option>
                        </select>
                    </p>
                </td>
                <td>
                    <p class="uk-form-controls-condensed">
                        <input type="checkbox" v-model="divider">
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
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
