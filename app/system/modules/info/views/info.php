<?php $view->script('system-info', 'app/system/modules/info/app/info.js', 'system') ?>

<div id="info" class="uk-grid" data-uk-grid-margin>
    <div class="uk-width-medium-1-4">

        <div class="uk-panel">
            <ul class="uk-nav uk-nav-side" data-uk-tab="{ connect: '#tab-content' }">
                <li class="uk-active"><a>{{ 'System' | trans }}</a></li>
                <li><a>{{ 'PHP' | trans }}</a></li>
                <li><a>{{ 'Database' | trans }}</a></li>
                <li><a>{{ 'Permissions' | trans }}</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-width-medium-3-4">

        <ul id="tab-content" class="uk-switcher uk-margin">
            <li>
                <h2>{{ 'System' | trans }}</h2>
                <div class="uk-overflow-container">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th class="pk-table-width-150">{{ 'Setting' | trans }}</th>
                                <th>{{ 'Value' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'Pagekit Version' | trans }}</td>
                                <td>{{ info.version }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'Web Server' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.server }}</td>
                            </tr>
                            <tr>
                                <td class="uk-text-nowrap">{{ 'User Agent' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.useragent }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li>
                <h2>{{ 'PHP' | trans }}</h2>
                <div class="uk-overflow-container">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th class="pk-table-width-150">{{ 'Setting' | trans }}</th>
                                <th>{{ 'Value' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ 'Version' | trans }}</td>
                                <td>{{ info.phpversion }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Handler' | trans }}</td>
                                <td>{{ info.sapi_name }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Built On' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.php }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Extensions' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.extensions }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li>
                <h2>{{ 'Database' | trans }}</h2>
                <div class="uk-overflow-container">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th class="pk-table-width-150">{{ 'Setting' | trans }}</th>
                                <th>{{ 'Value' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ 'Driver' | trans }}</td>
                                <td>{{ info.dbdriver }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Version' | trans }}</td>
                                <td>{{ info.dbversion }}</td>
                            </tr>
                            <tr>
                                <td>{{ 'Client' | trans }}</td>
                                <td class="pk-table-text-break">{{ info.dbclient }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li>
                <h2>{{ 'Permisssion' | trans }}</h2>
                <div class="uk-overflow-container">
                    <table class="uk-table uk-table-hover">
                        <thead>
                            <tr>
                                <th>{{ 'Path' | trans }}</th>
                                <th class="pk-table-width-100">{{ 'Status' | trans }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-repeat="info.directories">
                                <td>{{ $key }}</td>
                                <td class="uk-text-success" v-show="$value">{{ 'Writable' | trans }}</span></td>
                                <td class="uk-text-danger" v-show="!$value">{{ 'Unwritable' | trans }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
        </ul>

    </div>
</div>
