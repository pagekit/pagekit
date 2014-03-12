@script('alias', 'system/js/aliases/edit.js', 'requirejs')

<form class="uk-form uk-form-horizontal" action="@url('@system/alias/save', ['id' => alias.id ?: 0])" method="post">

    <div class="uk-form-row">
        <label for="form-alias" class="uk-form-label">@trans('Alias')</label>
        <div class="uk-form-controls">
            <input id="form-alias" class="uk-form-width-large" type="text" name="alias" value="@alias.alias" required>
            <p class="uk-form-help-block">@trans('Use a relative path and don\'t add a trailing slash. For example: <code>about</code>')</p>
        </div>
    </div>
    <div class="uk-form-row">
        <label class="uk-form-label">@trans('Source')</label>
        <div class="uk-form-controls uk-form-controls-text">
            <input type="hidden" name="source" value="@alias.source">
        </div>
    </div>
    <p>
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url('@system/alias/index')">@(alias.id ? trans('Close') : trans('Cancel'))</a>
    </p>

    @token()

</form>