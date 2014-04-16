<form class="uk-form uk-form-horizontal" action="@url.route('@system/themes/savesettings', ['name' => '%NAME%'])" method="post">

    <!-- your settings here -->

    <p>
        <button class="uk-button uk-button-primary" type="submit">Save</button>
        <a class="uk-button" href="@url.route('@system/themes/index')">@trans('Close')</a>
    </p>

</form>