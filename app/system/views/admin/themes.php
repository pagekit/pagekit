<?php $view->script('extensions-upload', 'app/system/app/upload.js', ['vue-system', 'uikit-upload']) ?>
<?php $view->script('extensions-index', 'app/system/app/themes.js', 'marketplace') ?>

<div id="themes" class="uk-grid" data-uk-grid-margin>

    <div class="uk-width-medium-1-4 pk-sidebar-left">

        <div class="uk-panel uk-panel-divider pk-panel-marginless">
            <ul class="uk-nav uk-nav-side" data-uk-switcher="{connect:'#tab-content', toggle:' > *:not(.uk-nav-header)'}">
                <li class="uk-active"><a href="#">{{ 'Installed' | trans }}</a></li>
                <li>
                    <a href="#">{{ 'Updates' | trans }}
                        <i class="uk-icon-spinner uk-icon-spin" v-show="status == 'loading'"></i>
                        <span class="uk-badge" v-show="updates.length">{{ updates.length }}</span>
                    </a>
                </li>
                <li><a href="#">{{ 'Install' | trans }}</a></li>
                <li class="uk-nav-header">{{ 'Marketplace' | trans }}</li>
                <li><a href="#">{{ 'All' | trans }}</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-width-medium-3-4">

        <ul id="tab-content" class="uk-switcher uk-margin">
            <li>

                <div class="uk-grid uk-grid-width-large-1-2" data-uk-grid-margin data-uk-grid-match="{target:'.uk-panel'}">
                    <div v-repeat="pkg: packages">
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

            </li>
            <li>

                <div class="uk-alert uk-alert-info uk-margin-remove" v-show="!updates">
                    {{ 'No theme updates found.' | trans }}
                </div>

                <div class="uk-alert uk-alert-warning uk-margin-remove" v-show="status == 'error'">
                    {{ 'An error occurred in retrieving update information. Please try again later.' | trans }}
                </div>

            </li>
            <li class="js-upload" data-action="@url('@system/package/upload', ['type' => 'theme'])">

                <h2 class="pk-form-heading">{{ 'Install an theme' | trans }}</h2>

                <v-upload v-with="action: 'theme'"></v-upload>

            </li>
            <li>

                <form class="uk-form">
                    <input type="text" name="q" placeholder="{{ 'Search' | trans }}" v-model="search">
                </form>

                <hr>

                <v-marketplace v-with="api: api, search: search, type: 'theme', installed: packages"></v-marketplace>

            </li>
        </ul>

    </div>

</div>
