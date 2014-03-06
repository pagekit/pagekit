<div class="uk-margin uk-text-center uk-vertical-align">

    <div class="uk-panel uk-panel-box uk-vertical-align-middle" style="width: 300px;">

        <form class="uk-form" action="@url.to('@system/auth/authenticate')" method="post">

            <h3>Logo Placeholder</h3>
            <div class="uk-form-row">
                <input class="uk-width-1-1" type="text" name="credentials[username]" value="@last_username" placeholder="@trans('username')" autofocus>
            </div>
            <div class="uk-form-row">
                <input class="uk-width-1-1" type="password" name="credentials[password]" value="" placeholder="@trans('password')">
            </div>
            <div class="uk-form-row">
                <input class="uk-button uk-button-primary uk-width-1-1" type="submit" value="@trans('Submit')">
            </div>
            <p class="uk-text-left">
                <label><input type="checkbox" name="@remember_me_param"> @trans('Remember Me')</label><br>
                <a href="@url.to('@system/auth/reset')" title="@trans('Recover your password')">@trans('Forgot Password?')</a>
            </p>

            <input type="hidden" name="redirect" value="@redirect">
            @token()
        </form>

    </div>

</div>