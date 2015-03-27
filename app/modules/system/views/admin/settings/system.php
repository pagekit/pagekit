<h2 class="pk-form-heading">{{ 'System' | trans }}</h2>
<div class="uk-form-row">
    <label for="form-apikey" class="uk-form-label">{{ 'API Key' | trans }}</label>
    <div class="uk-form-controls">
        <textarea id="form-apikey" class="uk-form-width-large" placeholder="{{ 'Enter your API key' | trans }}" rows="6" v-model="option.system.api.key"></textarea>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-channel" class="uk-form-label">{{ 'Release Channel' | trans }}</label>
    <div class="uk-form-controls">
        <select id="form-channel" class="uk-form-width-large" v-model="option.system.release_channel">
            <option value="stable">{{ 'Stable' | trans }}</option>
            <option value="nightly">{{ 'Nightly' | trans }}</option>
        </select>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-uploadfolder" class="uk-form-label">{{ 'Storage' | trans }}</label>
    <div class="uk-form-controls">
        <input id="form-uploadfolder" class="uk-form-width-large" type="text" v-model="config.system.storage" placeholder="/storage">
    </div>
</div>
<div class="uk-form-row">
    <span class="uk-form-label">{{ 'Developer' | trans }}</span>
    <div class="uk-form-controls uk-form-controls-text">
        <p class="uk-form-controls-condensed">
            <label><input type="checkbox" value="1" v-model="config.framework.debug"> {{ 'Enable debug mode' | trans }}</label>
        </p>
        <p class="uk-form-controls-condensed">
            <label><input type="checkbox" value="1" v-model="config['system/profiler'].enabled" v-attr="disabled: !sqlite"> {{ 'Enable debug toolbar' | trans }}</label>
        </p>
        <p v-if="!sqlite" class="uk-form-help-block">{{ 'Please enable the SQLite database extension.' | trans }}</p>
    </div>
</div>
