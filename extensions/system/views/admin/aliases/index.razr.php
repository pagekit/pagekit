@script('aliases', 'system/js/aliases/index.js', 'requirejs')

<form id="js-aliases" class="uk-form" action="@url.route('@system/alias/index')" method="post">

    <div class="pk-options uk-clearfix">
        <div class="uk-float-left">

            <a class="uk-button uk-button-primary" href="@url.route('@system/alias/add')">@trans('Add Alias')</a>
            <a class="uk-button pk-button-danger uk-hidden js-show-on-select" href="#" data-action="@url.route('@system/alias/delete')">@trans('Delete')</a>

        </div>
        <div class="uk-float-right">

            <input type="text" name="filter[search]" placeholder="@trans('Search')" value="@filter['search']">

        </div>
    </div>

    @if (aliases)
    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle js-table">
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
                        <input class="js-select" type="checkbox" name="ids[]" value="@alias.Id">
                    </td>
                    <td>
                        <a href="@url.route('@system/alias/edit', ['id' => alias.id])">@alias.alias</a>
                    </td>
                    <td>@app['system.info'].resolveUrl(alias.source)|urldecode</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="uk-alert uk-alert-info">@trans('No URL aliases found.')</p>
    @endif

    @token()

</form>