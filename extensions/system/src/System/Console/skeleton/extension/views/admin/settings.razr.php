<form class="uk-form uk-form-horizontal" action="@url('@system/extensions/savesettings', ['name' => '%NAME%'])" method="post">

    <!-- your settings here -->

    <p>
        <button class="uk-button uk-button-primary" type="submit">Save</button>
        <a class="uk-button" href="@url('@system/extensions')">@trans('Close')</a>
    </p>

</form>