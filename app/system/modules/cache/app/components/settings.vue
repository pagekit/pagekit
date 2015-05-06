<template>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Cache' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Cache' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed" v-repeat="cache: caches">
                <label><input type="radio" value="{{ $key }}" v-model="config.caches.cache.storage" v-attr="disabled: !cache.supported"> {{ cache.name }}</label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{ 'Developer' | trans }}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" value="1" v-model="config.nocache"> {{ 'Disable cache' | trans }}</label>
            </p>
            <p>
                <button class="uk-button uk-button-primary" v-on="click: open">{{ 'Clear Cache' | trans }}</button>
            </p>
        </div>
    </div>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog">

            <h4>{{ 'Select caches to clear:' | trans }}</h4>

            <div class="uk-form">

                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" v-model="clear.cache"> {{ 'System Cache' | trans }}</label>
                        </p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" v-model="clear.temp"> {{ 'Temporary Files' | trans }}</label>
                        </p>
                    </div>
                </div>
                <p>
                    <button class="uk-button uk-button-primary" type="submit" v-on="click: clear">{{ 'Clear' | trans }}</button>
                    <button class="uk-button uk-modal-close" type="submit" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                </p>

            </div>

        </div>
    </div>

</template>

<script>

    var Settings = require('settings');

    module.exports = {

        data: function() {
            return { caches: window.$caches };
        },

        name: 'system/cache',
        label: 'Cache',
        priority: 30,

        template: __vue_template__,

        methods: {

            open: function(e) {
                e.preventDefault();

                this.$set('clear', { cache: true });

                this.modal = UIkit.modal(this.$$.modal);
                this.modal.show();
            },

            clear: function(e) {
                e.preventDefault();

                this.$http.post('admin/system/cache/clear', { caches: this.clear });
                this.cancel(e);
            },

            cancel: function(e) {
                e.preventDefault();

                this.modal.hide();
            }

        }

    };

    Settings.register(module.exports);

</script>
