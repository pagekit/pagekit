<?php $view->script('marketplace', 'system/package:app/bundle/marketplace.js', 'system') ?>

<div id="marketplace">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove">{{ title | trans }}</h2>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="search" debounce="300">
                </div>
            </div>

        </div>
        <div data-uk-margin>

        </div>
    </div>

    <v-marketplace v-with="api: api, search: search, type: type, installed: packages"></v-marketplace>

</div>