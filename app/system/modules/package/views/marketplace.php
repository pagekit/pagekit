<?php $view->script('marketplace', 'system/package:app/marketplace.js', 'v-marketplace') ?>

<div id="marketplace">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Marketplace' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <form class="uk-form">
                <input type="text" name="q" placeholder="{{ 'Search' | trans }}" v-model="search">
            </form>

        </div>
    </div>

    <v-marketplace v-with="api: api, search: search, installed: packages"></v-marketplace>

</div>