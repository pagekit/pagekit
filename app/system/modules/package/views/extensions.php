<?php $view->script('extensions', 'system/package:app/bundle/extensions.js', 'v-upload') ?>

<div id="extensions">

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

            <v-upload type="extension"></v-upload>

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
                            <div class="uk-cover-background uk-position-cover" style="background-image: url({{icon(pkg)}});"></div>
                            <canvas class="uk-display-block" width="50" height="50"></canvas>
                        </div>
                    </td>
                    <td class="uk-text-nowrap">
                        <a>{{ pkg.title }}</a>
                    </td>
                    <td>
                        <a class="uk-button uk-button-success">{{ 'Update' | trans }}</a>
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
                                <li><a class="pk-icon-info pk-icon-hover" title="{{ 'View Details' | trans }}" data-uk-tooltip="{delay: 500}"></a></li>
                                <li v-show="pkg.enabled && pkg.permissions"><a class="pk-icon-users pk-icon-hover" title="{{ 'View Permissions' | trans }}" data-uk-tooltip="{delay: 500}" v-attr="href: $url('admin/user/permissions#:name',{name:pkg.name})"></a></li>
                                <li v-show="!pkg.enabled"><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: uninstall(pkg)" v-confirm="'Uninstall extension?'"></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="uk-modal" id="modal-package">

        <div class="uk-modal-dialog">

            <h3>{{ upload.package.title }}</h3>

            <div class="uk-grid">

                <div class="uk-width-medium-2-3">
                    {{ upload.package.description }}
                </div>

                <div class="uk-width-medium-1-3">

                    <div class="uk-margin uk-text-center">
                        <img src="{{ upload.package.extra.image }}" alt="{{ upload.package.title }}" />
                    </div>

                    <div class="uk-margin">

                    </div>

                </div>

            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-primary" v-on="click: install(upload)">{{ 'Install Package' | trans }}</button>
                <button class="uk-button uk-modal-close">{{ 'Cancel' | trans }}</button>
            </div>

        </div>

    </div>

</div>
