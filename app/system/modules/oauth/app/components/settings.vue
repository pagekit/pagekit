<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'OAuth' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-button-dropdown" data-uk-dropdown>
        <div class="uk-button">{{ 'Add Service' | trans }} <i class="uk-icon-caret-down"></i></div>
        <div class="uk-dropdown uk-dropdown-scrollable">
            <ul class="uk-nav uk-nav-dropdown" id="oauth-service-dropdown">
                <li id="{{ $value }}_link" v-repeat="providers | configured">
                    <a href="#" v-on="click: addProvider($value)">{{ $value }}</a>
                </li>
            </ul>
        </div>
    </div>

    <p>{{ $trans('Redirect URL: %url%', {url: redirect_url}) }}</p>

    <div id="oauth-service-list" class="uk-form-row">

        <div id="{{ $key }}-container" v-repeat="provider: options.provider">

            <h2>{{ $key }}</h2>
            <a class="uk-close uk-close-alt uk-float-right" href="#" v-on="click: removeProvider($key)"></a>

            <div class="uk-form-row">
                <label for="client_id_{{ $key }}" class="uk-form-label">{{ 'Client ID' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="client_id_{{ $key }}" class="uk-form-width-large" type="text" v-model="provider.client_id">
                </div>
            </div>

            <div class="uk-form-row">
                <label for="client_secret_{{ $key }}" class="uk-form-label">{{ 'Client Secret' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="client_secret_{{ $key }}" class="uk-form-width-large" type="text" v-model="provider.client_secret">
                </div>
            </div>

        </div>

    </div>

</template>

<script>

    var Settings = require('settings');

    module.exports = {

        section: {
            name: 'system/oauth',
            label: 'OAuth',
            icon: 'uk-icon-cog',
            priority: 50
        },

        data: function() {
            return window.$oauth
        },

        template: __vue_template__,

        ready: function () {

            if (Vue.util.isArray(this.options.provider)) {
                this.options.$delete('provider');
                this.options.$add('provider', {});
            }

            this.providers.sort();
        },

        methods: {

            addProvider: function (provider) {
                this.options.provider.$add(provider, {'client_id': '', 'client_secret': ''});
            },

            removeProvider: function (provider) {
                this.options.provider.$delete(provider);
            }

        },

        filters: {

            configured: function (services) {

                var results = [], self = this;

                services.forEach(function (service) {
                    if (!self.options.provider.hasOwnProperty(service)) {
                        results.push(service);
                    }
                });

                return results;
            }

        }

    };

    Settings.component('system/oauth', module.exports);

</script>
