<?php $view->script('site', 'site:app/bundle/site.js', ['system', 'vue-validator', 'uikit-nestable']) ?>

<div id="site" v-cloak>

    <div class="uk-grid">

        <div class="uk-panel uk-panel-box uk-width-1-4" v-component="menu-list"></div>
        <div class="uk-panel uk-panel-box uk-width-3-4" v-component="node-edit"></div>

    </div>

</div>
