<form class="uk-form" action="@url('@system/auth/authenticate')" method="post">

    <h3>Logo Placeholder</h3>
    <div class="uk-form-row">
        <input class="uk-width-1-1" type="text" name="credentials[username]" value="@last_username" placeholder="@trans('username')" autofocus>
    </div>
    <div class="uk-form-row">
        <input class="uk-width-1-1" type="password" name="credentials[password]" value="" placeholder="@trans('password')">
    </div>
    <div class="uk-form-row">
        <input class="uk-button uk-button-primary uk-width-1-1" type="submit" value="@trans('Login')">
    </div>
    <p class="uk-text-left">
        <label><input type="checkbox" name="@remember_me_param"> @trans('Remember Me')</label><br>
        @if (app.config.mail.enabled)
        <a href="@url('@system/auth/reset')" title="Recover your password">@trans('Forgot Password?')</a>
        @endif
    </p>

    <input type="hidden" name="redirect" value="@( widget.get('redirect.logout') ?: url.current(true) )">
    @token()
</form>