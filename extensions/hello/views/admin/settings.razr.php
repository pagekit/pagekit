<form class="uk-form uk-form-horizontal" action="@url.route('@system/extensions/savesettings', ['name' => 'hello'])" method="post">

    <div class="pk-toolbar">
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@system/system/index')">@trans('Close')</a>
    </div>

    <div class="uk-form-row">
        <label for="form-hello-message" class="uk-form-label">@trans('Message')</label>
        <div class="uk-form-controls">
            <input id="form-hello-message" type="text" name="config[message]" value="@config['message']">
        </div>
    </div>

    <p>@trans('This settings page is just for demonstration purposes.')</p>

    @token()

</form>