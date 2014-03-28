<form id="js-page" class="uk-form uk-form-horizontal" action="@url.route('@page/page/save', ['id' => page.id ?: 0])" method="post">

    <ul class="uk-tab" data-uk-tab="{ connect: '#tab-content' }">
        <li class="uk-active"><a href="#">@trans('Content')</a></li>
        <li><a href="#">@trans('Settings')</a></li>
    </ul>

    <ul id="tab-content" class="uk-switcher uk-margin">
        <li>

            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="page[title]" value="@page.Title" placeholder="@trans('Enter Title')" required>
            </div>

            <div class="uk-form-row">
                @trans('Slug'):
                <a class="js-slug" href="#" data-uk-toggle="{target:'.js-slug'}">@page.slug</a>
                <input class="uk-form-width-medium uk-form-small uk-hidden js-slug" type="text" name="page[slug]" value="@page.slug">
            </div>

            <div class="uk-form-row">
                <ul class="uk-subnav uk-">
                    <li>@trans('Status'): @( page.status ? '<span class="uk-text-success">Published</span>' : '<span class="uk-text-danger">Unpublished</span>' )</li>
                    <li>
                        @trans('Access'):
                        <a class="js-access" href="#" data-uk-toggle="{target:'.js-access'}">Public</a>
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

        </li>
        <li>

            <div class="uk-form-row">
                <label for="form-slug" class="uk-form-label">@trans('Slug')</label>
                <div class="uk-form-controls">
                    <input id="form-slug" class="uk-form-width-large" type="text" name="page[slug]" value="@page.slug">
                </div>
            </div>
            <div class="uk-form-row">
                <span class="uk-form-label">@trans('Status')</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" name="page[status]" value="1"@( page.status ? ' checked' : '' )> @trans('Enabled')</label>
                    </p>
                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" name="page[status]" value="0"@( !page.status ? ' checked' : '' )> @trans('Disabled')</label>
                    </p>
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form-access" class="uk-form-label">@trans('Access')</label>
                <div class="uk-form-controls">
                    <select id="form-access" class="uk-form-width-large" name="page[access_id]">
                        @foreach (levels as level)
                        <option value="@level.id"@(page.accessId == level.id ? ' selected' : '')>@level.name</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </li>
    </ul>

    <p>
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@page/page/index')">@( page.id ? trans('Close') : trans('Cancel') )</a>
    </p>

    @token()

</form>