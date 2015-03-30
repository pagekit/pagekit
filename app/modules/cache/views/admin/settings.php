<h2 class="pk-form-heading">{{ 'Cache' | trans }}</h2>

<div class="uk-form-row">
    <span class="uk-form-label">{{ 'Cache' | trans }}</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p v-repeat="cache: caches" class="uk-form-controls-condensed">
            <label><input type="radio" v-model="config['cache'].caches.cache.storage" value="{{ $key }}" v-attr="disabled: !cache.supported"> {{ cache.name }}</label>
        </p>
    </div>
</div>
<div class="uk-form-row">
    <span class="uk-form-label">{{ 'Developer' | trans }}</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <label><input type="checkbox" v-model="config['cache'].nocache" value="1"> {{ 'Disable cache' | trans }}</label>
        </p>
    </div>
</div>
