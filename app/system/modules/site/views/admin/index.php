<?php $view->script('site', 'site:app/bundle/site.js', ['vue', 'uikit-nestable']) ?>

<div id="site" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>

        <div class="pk-width-sidebar" v-component="menu-list"></div>

        <div class="uk-flex-item-1" v-component="node-edit"></div>

    </div>

</div>
