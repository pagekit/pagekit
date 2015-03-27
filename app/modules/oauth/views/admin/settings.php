<div v-component="v-oauth"></div>

<script id="template-oauth" type="x-template">
    <h2 class="pk-form-heading">{{ 'OAuth' | trans }}</h2>
    <div class="uk-button-dropdown" data-uk-dropdown>
        <div class="uk-button">{{ 'Add Service' | trans }} <i class="uk-icon-caret-down"></i></div>
        <div class="uk-dropdown uk-dropdown-scrollable">
            <ul class="uk-nav uk-nav-dropdown" id="oauth-service-dropdown">
                <li v-repeat="oauth | configured" id="{{ $value }}_link">
                    <a href="#" v-on="click: addProvider($value)">{{ $value }}</a>
                </li>
            </ul>
        </div>
    </div>

    <p>{{ $trans('Redirect URL: %url%', {url: redirect_url}) }}</p>

    <div id="oauth-service-list" class="uk-form-row">
        <input type="hidden" name="option[system/oauth]" value="">

        <div v-repeat="provider: option['system/oauth'].provider" id="{{ $key }}-container">
            <h2 class="pk-form-heading">{{ $key }}</h2>
            <a class="uk-close uk-close-alt uk-float-right" v-on="click: removeProvider($key)" href="#"></a>

            <div class="uk-form-row">
                <label for="client_id_{{ $key }}" class="uk-form-label">{{ 'Client ID' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="client_id_{{ $key }}" v-model="provider.client_id" class="uk-form-width-large"
                           type="text">
                </div>
            </div>
            <div class="uk-form-row">
                <label for="client_secret_{{ $key }}" class="uk-form-label">{{ 'Client Secret' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="client_secret_{{ $key }}" v-model="provider.client_secret" class="uk-form-width-large"
                           type="text">
                </div>
            </div>
            </br>
        </div>
    </div>
</script>
