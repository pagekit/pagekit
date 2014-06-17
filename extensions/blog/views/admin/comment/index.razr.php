@script('blog.comments-index', 'blog/js/comment/index.js', 'requirejs')

<form id="js-comments" class="uk-form" action="@url.route('@blog/comment')" method="post">

    <div class="pk-toolbar uk-clearfix">
        <div class="uk-float-left">

            <a class="uk-button pk-button-danger uk-hidden js-show-on-select" href="#" data-action="@url.route('@blog/comment/delete')">@trans('Delete')</a>

            <div class="uk-button-dropdown uk-hidden js-show-on-select" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">@trans('More') <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a href="#" data-action="@url.route('@blog/comment/status', ['status' => 0])">@trans('Approve')</a></li>
                        <li><a href="#" data-action="@url.route('@blog/comment/status', ['status' => 3])">@trans('Unapprove')</a></li>
                        <li><a href="#" data-action="@url.route('@blog/comment/status', ['status' => 2])">@trans('Mark as spam')</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="uk-float-right uk-hidden-small">

            <select name="filter[status]">
                <option value="">@trans('- Status -')</option>
                @foreach (statuses as id => status)
                <option value="@id"@(filter['status']|length && filter['status'] == id ? ' selected')>@status</option>
                @endforeach
            </select>

            <input type="text" name="filter[search]" placeholder="@trans('Search')" value="@filter['search']">

        </div>
    </div>

    <p class="uk-alert uk-alert-info @(comments ? 'uk-hidden' : '')">@trans('No comments found.')</p>

    <div class="js-table uk-overflow-container">
        @include('view://blog/admin/comment/table.razr.php', ['comments' => comments])
    </div>

    <ul class="uk-pagination @(total < 2 ? 'uk-hidden' : '')" data-uk-pagination="{ pages: @total }"></ul>

    @token()

    <input type="hidden" name="page" value="0">
    <input type="hidden" name="post" value="" />

</form>