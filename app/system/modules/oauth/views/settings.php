<div v-component="v-oauth" inline-template>

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
                <li id="{{ $value }}_link" v-repeat="oauth | configured">
                    <a href="#" v-on="click: addProvider($value)">{{ $value }}</a>
                </li>
            </ul>
        </div>
    </div>

    <p>{{ $trans('Redirect URL: %url%', {url: redirect_url}) }}</p>

    <div id="oauth-service-list" class="uk-form-row">

        <input type="hidden" name="option[system/oauth]" value="">

        <div id="{{ $key }}-container" v-repeat="provider: option['oauth'].provider">

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

</div>
