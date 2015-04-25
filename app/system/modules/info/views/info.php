<?php $view->script('system-info', 'app/system/modules/info/app/info.js', 'system') ?>

<div id="info">

    <ul class="uk-tab" data-uk-tab="{ connect:'#tab-content' }">
        <li class="uk-active"><a>{{ 'System' | trans }}</a></li>
        <li><a>{{ 'PHP' | trans }}</a></li>
        <li><a>{{ 'Database' | trans }}</a></li>
        <li><a>{{ 'Permissions' | trans }}</a></li>
    </ul>

    <ul id="tab-content" class="uk-switcher uk-margin">
        <li>
            <div class="uk-overflow-container">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <tr>
                            <th>{{ 'Setting' | trans }}</th>
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
            <div class="uk-overflow-container">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <tr>
                            <th>{{ 'Setting' | trans }}</th>
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
            <div class="uk-overflow-container">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <tr>
                            <th>{{ 'Setting' | trans }}</th>
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
            <div class="uk-overflow-container">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <tr>
                            <th>{{ 'Path' | trans }}</th>
                            <th class="uk-text-center pk-table-width-100">{{ 'Status' | trans }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-repeat="info.directories">
                            <td>{{ $key }}</td>
                            <td v-show="$value" class="uk-text-center uk-text-success">{{ 'Writable' | trans }}</span></td>
                            <td v-show="!$value" class="uk-text-center uk-text-danger">{{ 'Unwritable' | trans }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </li>
    </ul>

</div>
