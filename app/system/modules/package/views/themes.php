<?php $view->script('themes', 'system/package:app/bundle/themes.js', 'v-upload') ?>

<div id="themes">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap"  data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Themes' | trans }}</h2>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="search">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-button-primary">{{ 'Upload' | trans }}</a>

        </div>
    </div>

   <div class="uk-grid uk-grid-width-medium-1-2 uk-grid-width-xlarge-1-3" data-uk-grid-margin data-uk-grid-match="{target:'.uk-panel'}">
        <div v-repeat="pkg: packages | filterBy search in 'title'">
            <div class="uk-panel uk-panel-box">
                <div class="uk-panel-teaser">
                    <img width="800" height="600" alt="{{ pkg.title }}" v-attr="src: icon(pkg)">
                </div>
                <div class="pk-themes-position">
                    <h2 class="uk-panel-title uk-margin-remove">
                        {{ pkg.title }} <span class="uk-badge" v-show="pkg.enabled">{{ 'Active' | trans }}</span>
                    </h2>
                    <ul class="uk-subnav uk-subnav-line uk-margin-remove uk-text-nowrap">
                        <li><span>{{ pkg.version }}</span></li>
                        <li><a>{{ 'Details' | trans }}</a></li>
                    </ul>
                    <div class="pk-themes-action" v-show="pkg.enabled">
                        <a class="uk-button">{{ 'Settings' | trans }}</a>
                    </div>
                    <div class="pk-themes-action" v-show="!pkg.enabled">
                        <a class="uk-button uk-button-primary" v-on="click: enable(pkg)">{{ 'Enable' | trans }}</a>
                        <a class="uk-button pk-button-danger " v-on="click: uninstall(pkg)">{{ 'Delete' | trans }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
