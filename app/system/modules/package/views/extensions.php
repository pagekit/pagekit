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
                    <th class="pk-table-width-minimum uk-text-center">{{ 'Status' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">{{ 'Version' | trans }}</th>
                    <th class="pk-table-width-100">{{ 'Folder' | trans }}</th>
                    <th class="pk-table-width-minimum"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="uk-visible-hover-inline" v-repeat="pkg: packages | filterBy search in 'title'">
                    <td class="pk-table-width-minimum">
                        <img class="uk-img-preserve" width="50" height="50" alt="{{ pkg.title }}" v-attr="src: icon(pkg)">
                    </td>
                    <td class="uk-text-nowrap">
                        <a>{{ pkg.title }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a class="uk-icon-circle uk-text-success" title="{{ 'Enabled' | trans }}" v-show="pkg.enabled" v-on="click: disable(pkg)"></a>
                        <a class="uk-icon-circle uk-text-danger" title="{{ 'Disabled' | trans }}" v-show="!pkg.enabled" v-on="click: enable(pkg)"></a>
                    </td>
                    <td class="uk-text-center">{{ pkg.version }}</td>
                    <td class="pk-table-max-width-150 uk-text-truncate">/{{ pkg.name }}</td>
                    <td class="uk-text-right">
                        <ul class="uk-subnav pk-subnav-icon uk-flex-inline uk-invisible" style="flex-wrap: nowrap;">
                            <li v-show="!pkg.enabled"><a class="uk-icon-users" title="View Permissions" data-uk-tooltip="{delay: 500}" v-attr="href: $url('admin/system/user/permission#ext-:name',{name:pkg.name})"></a></li>
                            <li><a class="uk-icon-info-circle" title="View Details" data-uk-tooltip="{delay: 500}"></a></li>
                            <li v-show="!pkg.enabled"><a class="uk-icon-trash-o" title="Delete" data-uk-tooltip="{delay: 500}" v-on="click: uninstall(pkg)"></a></li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
