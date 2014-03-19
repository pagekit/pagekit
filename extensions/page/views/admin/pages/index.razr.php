@script('pages-index', 'page/js/pages.js', 'requirejs')

<form id="js-pages" class="uk-form" action="@url.route('@page/page/index')" method="post">

    <div class="pk-options uk-clearfix">
        <div class="uk-float-left">

            <a class="uk-button uk-button-primary" href="@url.route('@page/page/add')">@trans('Add Page')</a>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">@trans('Actions') <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a href="#" data-action="@url.route('@page/page/status', ['status' => 1])">@trans('Enable')</a></li>
                        <li><a href="#" data-action="@url.route('@page/page/status', ['status' => 0])">@trans('Disable')</a></li>
                        <li class="uk-nav-divider"></li>
                        <li><a href="#" data-action="@url.route('@page/page/copy')">@trans('Copy')</a></li>
                        <li><a href="#" data-action="@url.route('@page/page/delete')">@trans('Delete')</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="uk-float-right">

            <input type="text" name="filter[search]" placeholder="@trans('Search')" value="@filter['search']">

            <select name="filter[status]">
                <option value="">@trans('- Status -')</option>
                @foreach (statuses as id => status)
                <option value="@id"@(filter['status']|length && filter['status'] == id ? ' selected')>@status</option>
                @endforeach
            </select>

        </div>
    </div>

    @if (pages)
    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></th>
                    <th class="pk-table-min-width-100">@trans('Title')</th>
                    <th class="pk-table-width-100 uk-text-center">@trans('Status')</th>
                    <th class="pk-table-width-200 pk-table-min-width-200">@trans('URL')</th>
                    <th class="pk-table-width-100">@trans('Access')</th>
                </tr>
            </thead>
            <tbody>
                @foreach (pages as page)
                <tr>
                    <td>
                        <input type="checkbox" name="ids[]" value="@page.id">
                    </td>
                    <td>
                        <a href="@url.route('@page/page/edit', ['id' => page.id])">@page.title</a>
                    </td>
                    <td class="uk-text-center">
                        <a href="#" data-action="@url.route('@page/page/status', ['ids[]' => page.id, 'status' => page.status ? '0' : '1'])" title="@page.statusText">
                            <i class="uk-icon-circle uk-text-@(page.status ? 'success' : 'danger')" title="@page.statusText"></i>
                        </a>
                    </td>
                    <td class="pk-table-text-break">
                        <a href="@url.route('@page/id', ['id' => page.id])" target="_blank">@url.route('@page/id', ['id' => page.id])</a>
                    </td>
                    <td>
                        @(levels[page.accessId].name ?: trans('No access level'))
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    @else
    <p class="uk-alert uk-alert-info">@trans('No pages found.')</p>
    @endif

    @token()

</form>
