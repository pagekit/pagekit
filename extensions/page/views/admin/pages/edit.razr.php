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
                <input class="uk-form-width-medium uk-form-small uk-hidden js-slug" type="text" name="page[slug]" value="@page.slug" data-url="@url.route('@page/page/getslug', ['_csrf' => app.csrf.generate])">
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
            <li>
                @trans('Markdown'):
                <a class="uk-text-danger js-markdown  @(page.get('markdown') ? 'uk-hidden':'')" data-value="0">@trans('No')</a>
                <a class="uk-text-success js-markdown  @(!page.get('markdown') ? 'uk-hidden':'')" data-value="1">@trans('Yes')</a>
                <input type="hidden" name="page[data][markdown]" value="@page.get('markdown', '0')">
            </li>
            <li>
                @trans('Publish on'):
                <a class="js-publish" href="#" data-uk-toggle="{target:'.js-publish'}">@page.date|date @ @page.date|date('H:i')</a>
                <input class="uk-form-width-small uk-form-small uk-hidden js-publish" type="text" data-uk-datepicker="{ format: 'YYYY-MM-DD' }" name="" value="@page.date|date('Y-m-d')">
                <input class="uk-form-width-small uk-form-small uk-hidden js-publish" type="text" data-uk-timepicker="{ showSeconds: true }" name="" value="@page.date|date('H:i:s')">
                <input type="hidden" name="page[date]" value="@page.date|date('Y-m-d H:i:s')">
            </li>
        </ul>
    </div>

    <div class="uk-form-row">
        @editor('page[content]', page.content, ['id' => 'page-content', 'markdown' => page.get('markdown', '0') ])
    </div>

    <p>
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@page/page/index')">@( page.id ? trans('Close') : trans('Cancel') )</a>
    </p>

    @token()

</form>