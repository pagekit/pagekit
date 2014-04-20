<form class="uk-form uk-form-horizontal" action="@url.route('@system/extensions/savesettings', ['name' => 'hello'])" method="post">

	<div class="pk-options">
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@system/system/index')">@trans('Close')</a>
    </div>

    <p>
        @trans('This settings page is just for demonstration purposes.')
    </p>

    @token()

</form>