<form class="uk-form uk-form-horizontal" action="@url.route('@hello/hello/savesettings')" method="post">

    <p>
        @trans('This settings page is just for demonstration purposes.')
    </p>

    <p>
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@system/system/index')">@trans('Close')</a>
    </p>

    @token()

</form>