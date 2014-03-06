<table class="uk-table uk-table-hover">
    <thead>
        <tr>
            <th colspan="2">@trans('Name')</th>
            <th colspan="2">@trans('Changelog')</th>
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
            </td>
            <td>{{ version.changelog }}</td>
            <td class="pk-table-max-width-200">
                <ul class="uk-list pk-extensions-list pk-extensions-margin">
                    <li class="uk-text-truncate"><strong>@trans('Version'):</strong> {{ version.version }}</li>
                    <li class="uk-text-truncate"><strong>@trans('Date'):</strong> {{ version.released }}</li>
                </ul>
            </td>
            <td>
                <button class="uk-button uk-button-primary pk-extensions-margin" data-install="{{ name }}">@trans('Update')</button>
            </td>
        </tr>
        {{ /packages }}
    </tbody>
</table>