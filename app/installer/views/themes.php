<?php $view->script('themes', 'installer:app/bundle/themes.js', ['vue', 'uikit-upload', 'editor']) ?>

<div id="themes" v-cloak>

    <div class="uk-margin-large uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Themes' | trans }}</h2>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="search">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <package-upload :api="api" :packages="packages" type="theme"></package-upload>

        </div>
    </div>

    <div class="uk-grid uk-grid-medium uk-grid-match uk-grid-width-medium-1-2 uk-grid-width-xlarge-1-3" data-uk-grid-margin>
        <div v-for="pkg in packages | filterBy search in 'title' | themeorder">
            <div class="uk-panel uk-panel-box uk-visible-hover uk-overlay-hover">

                <div class="uk-panel-teaser">
                    <div class="uk-overlay uk-display-block">
                        <div class="uk-cover-background uk-position-cover" :style="{'background-image': 'url('+image(pkg)+')'}"></div>
                        <canvas class="uk-responsive-width uk-display-block" width="1200" height="800"></canvas>
                        <div class="uk-overlay-panel uk-overlay-background pk-overlay-background uk-overlay-fade"></div>
                    </div>
                </div>

                <h2 class="uk-panel-title uk-margin-remove">{{ pkg.title }}</h2>

                <div class="uk-text-muted">{{ pkg.authors[0].name }}</div>

                <a class="uk-position-cover" @click="details(pkg)"></a>

                <div class="pk-panel-badge-bottom-right">
                    <button class="uk-button uk-button-primary uk-button-small" v-show="pkg.enabled && pkg.settings" @click="settings(pkg)">Customize</button>
                    <button class="uk-button uk-button-success uk-button-small" @click="update(pkg, updates)" v-show="updates && updates[pkg.name]">{{ 'Update' | trans }}</button>
                </div>

                <div class="uk-panel-badge pk-panel-badge uk-hidden" v-if="!pkg.enabled">
                    <ul class="uk-subnav pk-subnav-icon">
                        <li><a class="pk-icon-star pk-icon-hover" :title="'Enable' | trans" data-uk-tooltip="{delay: 500}" @click="enable(pkg)"></a></li>
                        <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click="uninstall(pkg, packages)" v-confirm="'Uninstall theme?'"></a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="packages | empty">{{ 'No theme found.' | trans }}</h3>

    <v-modal v-ref:details>
        <package-details :api="api" :package="package"></package-details>
    </v-modal>

    <v-modal v-ref:settings>
        <component :is="view" :package="package"></component>
    </v-modal>

</div>
