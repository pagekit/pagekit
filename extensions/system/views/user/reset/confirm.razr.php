<form class="uk-article uk-form" action="@url.route('@system/resetpassword/confirm', ['user' => username, 'key' => activation])" method="post">

    <h1 class="uk-article-title">@trans('Password confirmation')</h1>

    <p>@trans('Enter your new password below.')</p>

    <div class="uk-form-row">
        <label for="form-password" class="uk-form-label">@trans('New password')</label>
        <div class="uk-form-controls">
            <input id="form-password" type="password" name="password1" value="" required>
        </div>
    </div>
    <div class="uk-form-row">
        <label for="form-confirm" class="uk-form-label">@trans('Confirm password')</label>
        <div class="uk-form-controls">
            <input id="form-confirm" type="password" name="password2" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit">@trans('Submit')</button>
    </div>

    @token()

</form>