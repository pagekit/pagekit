<?php $view->script('themes', 'system/package:app/bundle/themes.js', 'v-upload') ?>

<div id="themes">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

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
            <div class="uk-panel uk-panel-box uk-visible-hover">

                <div class="uk-panel-teaser">
                    <img width="800" height="600" alt="{{ pkg.title }}" v-attr="src: icon(pkg)">
                </div>

                <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                    <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

                        <h2 class="uk-h3 uk-margin-remove">{{ pkg.title }}</h2>

                        <div class="uk-margin-left">
                            <ul class="uk-subnav pk-subnav-icon uk-hidden">
                                <li v-show="pkg.enabled"><a class="uk-icon-cog" title="Settings" data-uk-tooltip="{delay: 500}"></a></li>
                                <li v-show="!pkg.enabled"><a class="uk-icon-check-circle-o" title="Enable" data-uk-tooltip="{delay: 500}" v-on="click: enable(pkg)"></a></li>
                                <li v-show="!pkg.enabled"><a class="pk-icon-delete pk-icon-hover" title="Delete" data-uk-tooltip="{delay: 500}" v-on="click: uninstall(pkg)" v-confirm="'Uninstall theme?'"></a></li>
                            </ul>
                        </div>

                    </div>
                    <div data-uk-margin>

                        <span class="uk-badge" v-show="pkg.enabled">{{ 'Active' | trans }}</span>

                    </div>
                </div>

                <p class="uk-text-muted">
                    {{ pkg.version }}
                    <br>/{{ pkg.name }}
                </p>

            </div>
        </div>
    </div>

</div>
