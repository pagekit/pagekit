@script('menu', 'system/js/menu/item.edit.js', 'requirejs')

<form class="js-item-edit uk-form uk-form-horizontal" action="@url.route('@system/item/save')" data-item-id="@item.id" method="post">

    <p class="uk-alert uk-alert-warning uk-hidden" data-msg="no-link">@trans('Please choose a link.')</p>

    <div class="uk-form-row">
        <label for="form-title" class="uk-form-label">@trans('Title')</label>
        <div class="uk-form-controls">
            <input id="form-title" class="uk-form-width-large" type="text" name="item[name]" value="@item.name" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-url" class="uk-form-label">@trans('URL')</label>
        <div class="uk-form-controls uk-form-controls-text">
            <input type="hidden" name="item[url]" value="@item.url" data-context="system/menu">
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

    <input type="hidden" name="id" value="@item.id">
    <input type="hidden" name="menu" value="@(menu.id ?: item.menuId)">

    @token()

    <p>
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@system/menu/index', ['id' => item.menuId])">@(item.id ? trans('Close') : trans('Cancel'))</a>
    </p>

</form>