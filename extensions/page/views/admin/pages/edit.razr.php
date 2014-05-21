@script('pages-edit', 'page/js/edit.js', 'requirejs')

<form id="js-page" class="uk-form uk-form-stacked" action="@url.route('@page/page/save')" method="post">

    <div class="pk-toolbar">
        <button class="uk-button uk-button-primary" type="submit">@trans('Save') <i class="uk-icon-spinner uk-icon-spin js-spinner uk-margin-small-left uk-hidden"></i></button>
        <a class="uk-button js-cancel" href="@url.route('@page/page/index')" data-label="@trans('Close')">@(page.id ? trans('Close') : trans('Cancel'))</a>
    </div>

    <div class="uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match>
        <div class="uk-width-medium-3-4">

            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="page[title]" value="@page.title" placeholder="@trans('Enter Title')" required>
                <input type="hidden" name="id" value="@(page.id ?: 0)">
            </div>
            <div class="uk-form-row">
                @editor('page[content]', page.content, ['id' => 'page-content', 'data-markdown' => page.get('markdown', '0')])
            </div>

        </div>
        <div class="uk-width-medium-1-4 pk-sidebar-right">

            <div class="uk-panel uk-panel-divider">
                <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Status')</label>
                    <div class="uk-form-controls">
                        <select class="uk-width-1-1" name="page[status]">
                            <option value="1"@(page.status ? ' selected')>@statuses[1]</option>
                            <option value="0"@(!page.status ? ' selected')>@statuses[0]</option>
                        </select>
                    </div>
                </div>
               <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Access')</label>
                    <div class="uk-form-controls">
                        <select class="uk-width-1-1" name="page[access_id]">
                            @foreach (levels as level)
                            <option value="@level.id"@(page.accessId == level.id ? ' selected')>@level.name</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Slug')</label>
                    <div class="uk-form-controls">
                        <input class="uk-width-1-1" type="text" name="page[slug]" value="@page.slug" data-url="@url.route('@page/page/getslug')">
                    </div>
                </div>
                <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Options')</label>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" name="page[data][title]" value="1"@(page.get('title', 1) ? ' checked')> @trans('Show Title')</label>
                    </div>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" name="page[data][markdown]" value="1"@(page.get('markdown', 0) ? ' checked')> @trans('Enable Markdown')</label>
                    </div>
                </div>

            </div>

        </div>
    </div>

    @token()

</form>
