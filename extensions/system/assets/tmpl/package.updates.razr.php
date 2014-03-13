<table class="uk-table uk-table-hover">
    <thead>
        <tr>
            <th colspan="2">@trans('Name')</th>
            <th>@trans('Version')</th>
            <th class="pk-table-width-minimum"></th>
        </tr>
    </thead>
    <tbody>
        {{ #packages }}
        <tr>
            <td class="pk-table-width-minimum">
                <img class="uk-img-preserve" src="{{ extra.image }}" width="50" height="50" alt="{{ title }}">
            </td>
            <td>
                <h2 class="pk-extensions-heading">{{ title }}</h2>
                <ul class="uk-subnav uk-subnav-line uk-margin-remove uk-text-nowrap">
                    <li><a href="" data-uk-toggle="{target:'#toggle'}">@trans('Show Changelog')</a></li>
                    <li><span>{{ version.released }}</span></li>
                </ul>
                <div id="toggle" class="uk-hidden">{{ version.changelog }}</div>
            </td>
            <td class="pk-table-max-width-200">{{ version.version }}</td>
            <td>
                <button class="uk-button uk-button-primary pk-extensions-margin" data-install="{{ name }}">@trans('Update')</button>
            </td>
        </tr>
        {{ /packages }}
    </tbody>
</table>