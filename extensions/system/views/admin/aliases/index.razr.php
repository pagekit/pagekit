@script('aliases', 'system/js/aliases/index.js', 'requirejs')

<form id="js-aliases" class="uk-form" action="@url('@system/alias/index')" method="post">

    <div class="pk-options uk-clearfix">
        <div class="uk-float-left">

            <a class="uk-button uk-button-primary" href="@url('@system/alias/add')">@trans('Add Alias')</a>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">@trans('Actions') <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a href="#" data-action="@url('@system/alias/delete')">@trans('Delete')</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="uk-float-right">

            <input type="text" name="filter[search]" placeholder="@trans('Search')" value="@filter['search']">

        </div>
    </div>

    @if (aliases)
    <table class="uk-table uk-table-hover uk-table-middle">
        <thead>
            <tr>
                <th class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></th>
                <th>@trans('Alias')</th>
                <th>@trans('Source')</th>
            </tr>
        </thead>
        <tbody>
            @foreach (aliases as alias)
            <tr>
                <td>
                    <input type="checkbox" name="ids[]" value="@alias.Id">
                </td>
                <td>
                    <a href="@url('@system/alias/edit', ['id' => alias.id])">@alias.alias</a>
                </td>
                <td>@alias.source</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="uk-alert uk-alert-info">@trans('No URL aliases found.')</p>
    @endif

</form>