<?php $view->script('marketplace', 'installer:app/bundle/marketplace.js', ['vue', 'marked']) ?>

<div id="marketplace" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap">
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove">{{ title | trans }}</h2>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="search" debounce="300">
                </div>
            </div>

        </div>
    </div>

    <marketplace :api="api" :search="search" :page="page" :type="type" :installed="installed"></marketplace>

</div>
