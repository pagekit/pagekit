<div class="uk-form-row">
    <label for="form-login-redirect" class="uk-form-label">@trans('Login Redirect Page')</label>
    <div class="uk-form-controls">
        <input id="form-login-redirect" class="uk-form-width-large" type="text" name="widget[settings][redirect.login]" value="@widget.get('redirect.login')">
        <p class="uk-form-help-block">@trans('Leave blank, to stay on current page after successful log in.')</p>
    </div>
</div>
<div class="uk-form-row">
    <label for="form-logout-redirect" class="uk-form-label">@trans('Logout Redirect Page')</label>
    <div class="uk-form-controls">
        <input id="form-logout-redirect" class="uk-form-width-large" type="text" name="widget[settings][redirect.logout]" value="@widget.get('redirect.logout')">
        <p class="uk-form-help-block">@trans('Leave blank, to redirect to frontpage after logout.')</p>
    </div>
</div>