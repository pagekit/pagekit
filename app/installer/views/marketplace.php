<?php $view->script('marketplace', 'installer:app/bundle/marketplace.js', 'vue') ?>

<div id="marketplace" v-cloak data-uk-observe>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove">{{ title | trans }}</h2>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="search" debounce="300">
                </div>
            </div>

        </div>
    </div>

    <marketplace :api="api" :search="search" :type="type" :installed="installed"></marketplace>

</div>
