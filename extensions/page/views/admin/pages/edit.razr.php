@script('pages-edit', 'page/js/edit.js', 'requirejs')

<form id="js-page" class="uk-form uk-form-horizontal uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match action="@url.route('@page/page/save')" method="post">
    <div class="uk-width-medium-3-4">

        <div class="pk-options uk-clearfix">
            <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
            <a class="uk-button" href="@url.route('@page/page/index')">@( page.id ? trans('Close') : trans('Cancel') )</a>
        </div>

        <div class="uk-form-row">
            <input class="uk-width-1-1 uk-form-large" type="text" name="page[title]" value="@page.title" placeholder="@trans('Enter Title')" required>
            <input type="hidden" name="id" value="@(page.id ?: 0)">
            <div class="uk-text-muted uk-margin-small-top">
                <span class="uk-visible-hover-inline js-slug">@(page.slug ?: '') <a class="uk-invisible uk-icon-pencil"></a></span>
                <input class="uk-form-width-medium uk-form-small uk-hidden" type="text" name="page[slug]" value="@page.slug">
            </div>
        </div>
        <div class="uk-form-row">
            @editor('page[content]', page.content, ['id' => 'page-content', 'data-markdown' => page.get('markdown', '0') ])
        </div>

    </div>
    <div class="uk-width-medium-1-4 pk-sidebar-right">

        <div class="uk-panel uk-panel-divider">
            <h3 class="uk-panel-title">Options</h3>
            <ul class="uk-list pk-list-table uk-margin-remove">
                <li>
                    <div>@trans('Status')</div>
                    <div>
                        <button class="uk-button uk-button-mini uk-button-danger js-status uk-hidden" type="button" data-status="0">@statuses[0]</button>
                        <button class="uk-button uk-button-mini uk-button-success js-status uk-hidden" type="button" data-status="1">@statuses[1]</button>
                        <input type="hidden" name="page[status]" value="@page.status">
                    </div>
                </li>
                <li>
                    <div>@trans('Access')</div>
                    <div>
                        <div class="uk-form-select" data-uk-form-select="{target:'button:first'}">
                            <button class="uk-button uk-button-mini" type="button">...</button>
                            <select name="page[access_id]">
                                @foreach (levels as level)
                                <option value="@level.id"@(page.accessId == level.id ? ' selected' : '')>@level.name</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </li>
                <li>
                    <div>@trans('Markdown')</div>
                    <div>
                        <button class="uk-button uk-button-mini js-markdown  @(page.get('markdown') ? 'uk-hidden':'')" type="button" data-value="0">@trans('No')</button>
                        <button class="uk-button uk-button-mini js-markdown  @(!page.get('markdown') ? 'uk-hidden':'')" type="button" data-value="1">@trans('Yes')</button>
                        <input type="hidden" name="page[data][markdown]" value="@page.get('markdown', '0')">
                    </div>
                </li>
                <li>
                    <div>@trans('Title')</div>
                    <div>
                        <button class="uk-button uk-button-mini js-title @(page.get('title') ? 'uk-hidden':'')" type="button" data-value="0">@trans('Hide')</button>
                        <button class="uk-button uk-button-mini js-title @(!page.get('title') ? 'uk-hidden':'')" type="button" data-value="1">@trans('Show')</button>
                        <input type="hidden" name="page[data][title]" value="@page.get('title', 1)">
                    </div>
                </li>
            </ul>
        </div>

    </div>

    @token()

</form>