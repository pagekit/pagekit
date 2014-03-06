<div class="uk-margin uk-text-center uk-vertical-align">

    <!-- TODO: style is '...' -->
    <div class="uk-panel uk-panel-box uk-vertical-align-middle" style="width: 300px;">

        <div class="uk-alert">
            @trans('Enter your new password below.')
        </div>

        <form class="uk-form" action="@url.to('@system/auth/resetconfirm', ['user' => username, 'key' => activation])" method="post">

            <h3>Logo Placeholder</h3>
            <div class="uk-form-row">
                <input class="uk-width-1-1" type="password" name="password1" value="" placeholder="@trans('New password')" required>
            </div>
            <div class="uk-form-row">
                <input class="uk-width-1-1" type="password" name="password2" value="" placeholder="@trans('Confirm new password')" required>
            </div>
            <div class="uk-form-row">
                <input class="uk-button uk-button-primary uk-width-1-1" type="submit" value="@trans('Reset Password')">
            </div>

        </form>

    </div>

</div>