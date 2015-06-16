<?php $view->script('site-index', 'site:app/bundle/index.js', ['vue', 'uikit-nestable']) ?>

<div id="site" class="uk-form" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>

        <menus class="pk-width-sidebar" menu="{{@ menu }}"></menus>
        <nodes class="uk-flex-item-1" menu="{{ menu }}"></nodes>

    </div>

</div>
