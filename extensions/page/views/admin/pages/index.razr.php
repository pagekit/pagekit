@script('pages-index', 'page/js/pages.js', 'requirejs')

<form id="js-pages" class="uk-form" action="@url.route('@page/page/index')" method="post">

    <div class="pk-options uk-clearfix">
        <div class="uk-float-left">

            <a class="uk-button uk-button-primary" href="@url.route('@page/page/add')">@trans('Add Page')</a>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">@trans('Actions') <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a href="#" data-action="@url.route('@page/page/status', ['status' => 1])">@trans('Publish')</a></li>
                        <li><a href="#" data-action="@url.route('@page/page/status', ['status' => 0])">@trans('Unpublish')</a></li>
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

    <p class="uk-alert uk-alert-info @(pages ? 'uk-hidden' : '')">@trans('No pages found.')</p>

    <div class="js-table uk-overflow-container">
        @include('view://page/admin/pages/table.razr.php', ['pages' => pages])
    </div>

    <ul class="uk-pagination @(!pages ? 'uk-hidden' : '')" data-uk-pagination="{ pages: @total }"></ul>

    @token()

    <input type="hidden" name="page" value="0">

</form>
