<div class="uk-margin uk-text-center uk-vertical-align">

    <!-- TODO: style is '...' -->
    <div class="uk-panel uk-panel-box uk-vertical-align-middle" style="width: 300px;">

        <div class="uk-alert">
            @trans('Please enter your username or email address. You will receive a link to create a new password via email.')
        </div>

        <form class="uk-form" action="@url.to('@system/auth/reset')" method="post">

            <h3>Logo Placeholder</h3>
            <div class="uk-form-row">
                <input class="uk-width-1-1" type="text" name="login" value="@last_login" placeholder="@trans('username or email')" required autofocus>
            </div>
            <div class="uk-form-row">
                <input class="uk-button uk-button-primary uk-width-1-1" type="submit" value="@trans('Get New Password')">
            </div>

        </form>

    </div>

</div>