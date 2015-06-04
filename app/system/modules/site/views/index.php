<?php $view->script('site', 'site:app/bundle/index.js', ['vue', 'uikit-nestable']) ?>

<div id="site" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>

        <menu-list class="pk-width-sidebar" active="{{ menu }}"></menu-list>
        <node-list class="uk-flex-item-1" menu="{{ menu }}"></node-list>

    </div>

</div>
