<?php $view->script('extensions', 'installer:app/bundle/extensions.js', ['vue', 'uikit-upload']) ?>

<div id="extensions" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Extensions' | trans }}</h2>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="search">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <package-upload api="{{ api }}" packages="{{ packages }}" type="extension"></package-upload>

        </div>
    </div>

    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th colspan="2">{{ 'Name' | trans }}</th>
                    <th class="pk-table-width-minimum"></th>
                    <th class="pk-table-width-minimum uk-text-center">{{ 'Status' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">{{ 'Version' | trans }}</th>
                    <th class="pk-table-width-100">{{ 'Folder' | trans }}</th>
                    <th class="pk-table-width-minimum"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="uk-visible-hover-inline" v-repeat="pkg: packages | filterBy search in 'title'">
                    <td class="pk-table-width-minimum">
                        <div class="uk-position-relative">
                            <div class="uk-cover-background uk-position-cover" v-style="background-image: 'url('+icon(pkg)+')'"></div>
                            <canvas class="uk-display-block" width="50" height="50"></canvas>
                        </div>
                    </td>
                    <td class="uk-text-nowrap">
                        <a v-on="click: settings(pkg)" v-show="pkg.enabled && pkg.settings">{{ pkg.title }}</a>
                        <span v-show="!pkg.enabled || !pkg.settings">{{ pkg.title }}</span>
                        <div class="uk-text-muted">{{ pkg.authors[0].name }}</div>
                    </td>
                    <td>
                        <a class="uk-button uk-button-success uk-button-small" v-on="click: update(updates[pkg.name])" v-show="updates && updates[pkg.name]">{{ 'Update' | trans }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a class="pk-icon-circle-success" title="{{ 'Enabled' | trans }}" v-show="pkg.enabled" v-on="click: disable(pkg)"></a>
                        <a class="pk-icon-circle-danger" title="{{ 'Disabled' | trans }}" v-show="!pkg.enabled" v-on="click: enable(pkg)"></a>
                    </td>
                    <td class="uk-text-center">{{ pkg.version }}</td>
                    <td class="pk-table-max-width-150 uk-text-truncate">/{{ pkg.name }}</td>
                    <td class="uk-text-right">
                        <div class="uk-invisible">
                            <ul class="uk-subnav pk-subnav-icon">
                                <li><a class="pk-icon-info pk-icon-hover" title="{{ 'View Details' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: details(pkg)"></a></li>
                                <li v-show="pkg.enabled && pkg.permissions"><a class="pk-icon-permission pk-icon-hover" title="{{ 'View Permissions' | trans }}" data-uk-tooltip="{delay: 500}" v-attr="href: $url.route('admin/user/permissions#:name',{name:pkg.module})"></a></li>
                                <li v-show="!pkg.enabled"><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: uninstall(pkg, packages)" v-confirm="'Uninstall extension?'"></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="packages | empty">{{ 'No extension found.' | trans }}</h3>

    <v-modal v-ref="details">
        <package-details api="{{ api }}" package="{{ package }}"></package-details>
    </v-modal>

    <v-modal v-ref="settings">
        <component is="{{ view }}" package="{{ package }}"></component>
    </v-modal>

</div>
