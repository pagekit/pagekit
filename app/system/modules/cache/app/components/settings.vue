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
                <button id="clearcache" class="uk-button uk-button-primary">{{ 'Clear Cache' | trans }}</button>
            </p>
        </div>
    </div>

    <!-- TODO: fix form in form + vueify-->
    <div id="modal-clearcache" class="uk-modal">
        <div class="uk-modal-dialog">

            <h4>{{ 'Select caches to clear:' | trans }}</h4>

            <form class="uk-form" action="{{ $url('admin/system/cache/clear') }}" method="post">

                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" name="caches[cache]" value="1" checked> {{ 'System Cache' | trans }}</label>
                        </p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" name="caches[temp]" value="1"> {{ 'Temporary Files' | trans }}</label>
                        </p>
                    </div>
                </div>
                <p>
                    <button class="uk-button uk-button-primary" type="submit">{{ 'Clear' | trans }}</button>
                    <button class="uk-button uk-modal-close" type="submit">{{ 'Cancel' | trans }}</button>
                </p>

            </form>

        </div>
    </div>


</template>

<script>

    var Settings = require('settings');

    module.exports = {

        data: function() {
            return { caches: window.$caches }
        },

        label: 'Cache',
        priority: 30,

        template: __vue_template__,

        ready: function() {

//            jQuery(function($) {
//
//                var modal;
//
//                $('#clearcache').on('click', function(e) {
//                    e.preventDefault();
//
//                    if (!modal) {
//                        modal = UIkit.modal('#modal-clearcache');
//                        modal.element.find('form').on('submit', function(e) {
//                            e.preventDefault();
//
//                            $.post($(this).attr('action'), $(this).serialize(),function(data) {
//                                UIkit.notify(data.message);
//                            }).fail(function() {
//                                UIkit.notify('Clearing cache failed.', 'danger');
//                            }).always(function() {
//                                modal.hide();
//                            });
//                        });
//                    }
//
//                    modal.show();
//                });
//
//            });

        }

    };

    Settings.register('system/cache', module.exports);

</script>
