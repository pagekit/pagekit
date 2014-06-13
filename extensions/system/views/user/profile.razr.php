<form class="uk-article uk-form uk-form-stacked" action="@url.route('@system/profile/save')" method="post">

    <h1 class="uk-article-title">@trans('Your Profile')</h1>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label">@trans('Name')</label>
        <div class="uk-form-controls">
            <input id="form-name" class="uk-form-width-large" type="text" name="user[name]" value="@user.name" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label">@trans('Email')</label>
        <div class="uk-form-controls">
            <input id="form-name" class="uk-form-width-large" type="text" name="user[email]" value="@user.email" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-password" class="uk-form-label">@trans('Password')</label>
        <div class="uk-form-controls">
            <input id="form-password" class="uk-form-width-large" type="password" name="user[password]" value="" autocomplete="off">
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-password2" class="uk-form-label">@trans('Confirm Password')</label>
        <div class="uk-form-controls">
            <input id="form-password2" class="uk-form-width-large" type="password" name="user[password2]" value="" autocomplete="off">
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
    </div>

    @token()

</form>