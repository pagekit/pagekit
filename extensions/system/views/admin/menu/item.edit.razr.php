@script('menu', 'system/js/menu/item.edit.js', 'requirejs')

<form class="js-item-edit uk-form uk-form-horizontal" action="@url.route('@system/item/save')" method="post">

    <div class="pk-toolbar">
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@system/menu/index', ['id' => item.menuId])">@(item.id ? trans('Close') : trans('Cancel'))</a>
    </div>

    <p class="uk-alert uk-alert-warning uk-hidden" data-msg="no-link">@trans('Please choose a link.')</p>

    <div class="uk-form-row">
        <label for="form-title" class="uk-form-label">@trans('Title')</label>
        <div class="uk-form-controls">
            <input id="form-title" class="uk-form-width-large" type="text" name="item[name]" value="@item.name" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-url" class="uk-form-label">@trans('Type')</label>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="item[url]" value="!divider"@(item.url == '!divider' ? ' checked')> @trans('Divider')</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="item[url]" value="!menu-header"@(item.url == '!menu-header' ? ' checked')> @trans('Menu Header')</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label>
                    <input type="radio" name="item[url]" value="@(item.url != '!divider' && item.url != '!menu-header' ? item.url~'" checked' : '"')>
                    <input class="js-item-url" type="hidden" name="" value="@(item.url != '!divider' && item.url != '!menu-header' ? item.url)" data-context="system/menu">
                </label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">@trans('Status')</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="item[status]" value="1"@(item.status ? ' checked')> @trans('Enabled')</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="radio" name="item[status]" value="0"@(!item.status ? ' checked')> @trans('Disabled')</label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-access" class="uk-form-label">@trans('Access')</label>
        <div class="uk-form-controls">
            <select id="form-access" class="uk-form-width-large" name="item[accessId]">
                @foreach (levels as level)
                <option value="@level.id"@(item.accessId == level.id ? ' selected')>@level.name</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-pages" class="uk-form-label">@trans('Activation Rules')</label>
        <div class="uk-form-controls">
            <textarea id="form-pages" class="uk-form-width-large" name="item[pages]" rows="5">@item.pages</textarea>
            <p class="uk-form-help-block">@trans('Enter custom activation rules as paths.')</p>
        </div>
    </div>


    <input type="hidden" name="id" value="@item.id">
    <input type="hidden" name="menu" value="@(menu.id ?: item.menuId)">

    @token()

</form>