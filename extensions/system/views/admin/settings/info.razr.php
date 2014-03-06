<ul class="uk-tab" data-uk-tab="{connect:'#tab-content'}">
    <li class="uk-active"><a href="#">@trans('System')</a></li>
    <li><a href="#">@trans('PHP')</a></li>
    <li><a href="#">@trans('Database')</a></li>
    <li><a href="#">@trans('Permissions')</a></li>
</ul>

<ul id="tab-content" class="uk-switcher uk-margin">
    <li>

        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>@trans('Setting')</th>
                    <th>@trans('Value')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@trans('Pagekit Version')</td>
                    <td>@info['version']</td>
                </tr>
                <tr>
                    <td>@trans('Web Server')</td>
                    <td>@info['server']</td>
                </tr>
                <tr>
                    <td>@trans('User Agent')</td>
                    <td>@info['useragent']</td>
                </tr>
            </tbody>
        </table>

    </li>
    <li>

        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>@trans('Setting')</th>
                    <th>@trans('Value')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@trans('Version')</td>
                    <td>@info['phpversion']</td>
                </tr>
                <tr>
                    <td>@trans('WebServer to PHP Interface')</td>
                    <td>@info['sapi_name']</td>
                </tr>
                <tr>
                    <td>@trans('Built On')</td>
                    <td>@info['php']</td>
                </tr>
                <tr>
                    <td>@trans('Extensions')</td>
                    <td>@info['extensions']</td>
                </tr>
            </tbody>
        </table>

    </li>
    <li>

        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>@trans('Setting')</th>
                    <th>@trans('Value')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@trans('Driver')</td>
                    <td>@info['dbdriver']</td>
                </tr>
                <tr>
                    <td>@trans('Version')</td>
                    <td>@info['dbversion']</td>
                </tr>
                <tr>
                    <td>@trans('Client')</td>
                    <td>@info['dbclient']</td>
                </tr>
            </tbody>
        </table>

    </li>
    <li>

        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>@trans('Path')</th>
                    <th>@trans('Status')</th>
                </tr>
            </thead>
            <tbody>
                @foreach (info['directories'] as directory => writable)
                <tr>
                    <td>@directory</td>
                    <td class="@( writable ? 'uk-text-success' : 'uk-text-danger' )">@( writable ? trans('Writable') : trans('Unwritable') )</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </li>
</ul>