@script('pages-edit', 'page/js/edit.js', 'requirejs')

<form id="js-page" class="uk-form uk-form-horizontal" action="@url.route('@page/page/save')" method="post">

    <div class="uk-form-row">
        <input class="uk-width-1-1 uk-form-large" type="text" name="page[title]" value="@page.title" placeholder="@trans('Enter Title')" required>
        <input type="hidden" name="id" value="@(page.id ?: 0)">
    </div>

    <div class="uk-form-row">
        <ul class="uk-subnav uk-">
            <li>
                @trans('Slug'):
                <a class="js-slug" href="#" data-uk-toggle="{target:'.js-slug'}">@(page.slug ?: '...')</a>
                <input class="uk-form-width-medium uk-form-small uk-hidden js-slug" type="text" name="page[slug]" value="@page.slug">
            </li>
            <li>
                @trans('Status'):
                <a class="uk-text-danger js-status uk-hidden" data-status="0">@statuses[0]</a>
                <a class="uk-text-success js-status uk-hidden" data-status="1">@statuses[1]</a>
                <input type="hidden" name="page[status]" value="@page.status">
            </li>
            <li>
                @trans('Access'):
                <a class="js-access" href="#" data-uk-toggle="{target:'.js-access'}">...</a>
                <select class="uk-form-width-small uk-form-small uk-hidden js-access" name="page[access_id]">
                    @foreach (levels as level)
                    <option value="@level.id"@(page.accessId == level.id ? ' selected' : '')>@level.name</option>
                    @endforeach
                </select>
            </li>
        </ul>
    </div>

    <div class="uk-form-row">
        @editor('page[content]', page.content, ['id' => 'page-content'])
    </div>

    <p>
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@page/page/index')">@( page.id ? trans('Close') : trans('Cancel') )</a>
    </p>

    @token()

</form>