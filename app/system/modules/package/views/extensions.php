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

            <a class="uk-button uk-button-primary">{{ 'Upload' | trans }}</a>

        </div>
    </div>

    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th colspan="2">{{ 'Name' | trans }}</th>
                    <th class="pk-table-width-100">{{ 'Version' | trans }}</th>
                    <th class="pk-table-width-minimum uk-text-center">{{ 'Status' | trans }}</th>
                    <th class="pk-table-width-minimum"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="pkg: packages | filterBy search in 'title'">
                    <td class="pk-table-width-minimum">
                        <img class="uk-img-preserve" width="50" height="50" alt="{{ pkg.title }}" v-attr="src: icon(pkg)">
                    </td>
                    <td class="uk-text-nowrap">
                        <h2 class="uk-h3 uk-margin-remove">{{ pkg.title }}</h2>
                        <ul class="uk-subnav uk-subnav-line uk-margin-remove" v-show="pkg.enabled">
                            <li><a>{{ 'Details' | trans }}</a></li>
                            <li><a>{{ 'Settings' | trans }}</a></li>
                            <li><a v-attr="href: $url('admin/system/user/permission#ext-:name',{name:pkg.name})">{{ 'Permissions' | trans }}</a></li>
                        </ul>
                    </td>
                    <td>
                        <ul class="uk-list uk-margin-remove">
                            <li class="uk-text-truncate">{{ pkg.version }}</li>
                            <li class="uk-text-truncate">/{{ pkg.name }}</li>
                        </ul>
                    </td>
                    <td class="uk-text-center">
                        <a class="uk-button uk-button-success" v-show="pkg.enabled" v-on="click: disable(pkg)">{{ 'Enabled' | trans }}</a>
                        <a class="uk-button" v-show="!pkg.enabled" v-on="click: enable(pkg)">{{ 'Disabled' | trans }}</a>
                    </td>
                    <td>
                        <a class="uk-button pk-button-danger" v-show="!pkg.enabled" v-on="click: uninstall(pkg)">{{ 'Delete' | trans }}</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
