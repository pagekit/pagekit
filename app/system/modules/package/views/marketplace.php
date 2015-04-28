<?php $view->script('marketplace', 'system/package:app/marketplace.js', 'v-marketplace') ?>

<div id="marketplace">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Marketplace' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <form class="uk-form">

                <input type="text" placeholder="{{ 'Search' | trans }}" v-model="search" debounce="300">

                <select v-model="type">
                    <option value="extension">{{ 'Extensions' | trans }}</option>
                    <option value="theme">{{ 'Themes' | trans }}</option>
                </select>

            </form>

        </div>
    </div>

    <v-marketplace v-with="api: api, search: search, type: type, installed: packages"></v-marketplace>

</div>