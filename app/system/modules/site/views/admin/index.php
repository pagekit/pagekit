<?php $view->script('site', 'site:app/bundle/site.js', ['system', 'vue-validator', 'uikit-nestable']) ?>

<div id="site" v-cloak>

    <div class="uk-grid">

        <div class="uk-panel uk-panel-box uk-width-1-4" v-component="menu-list"></div>
        <div class="uk-panel uk-panel-box uk-width-3-4" v-component="node-edit"></div>

    </div>

</div>

<!--TODO move partial to template file?-->
<script id="settings-fields" type="text/template">

    <div class="uk-form-row">
        <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
        <div class="uk-form-controls">
            <input id="form-title" class="uk-form-width-large" type="text" name="node[title]" v-model="node.title" v-valid="alphaNum">
            <span class="uk-form-help-block uk-text-danger" v-show="form['node[title]'].invalid">{{ 'Invalid name.' | trans }}</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
        <div class="uk-form-controls">
            <span>{{ path }}</span><br>
            <input id="form-slug" class="uk-form-width-large" type="text" name="node[slug]" v-model="node.slug" v-valid="alphaNum">
            <span class="uk-form-help-block uk-text-danger" v-show="form['node[slug]'].invalid">{{ 'Invalid slug.' | trans }}</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-status" class="uk-form-width-large" v-model="node.status">
                <option value="0">{{ 'Disabled' | trans }}</option>
                <option value="1">{{ 'Enabled' | trans }}</option>
            </select>
        </div>
    </div>

    <div class="uk-form-row" v-if="type.url">
        <span class="uk-form-label">{{ 'Options' | trans }}</span>
        <div class="uk-form-controls">
            <label><input type="checkbox" name="frontpage" v-model="isFrontpage"> {{ 'Frontpage' | trans }}</label>
        </div>
    </div>

</script>
