@script('user', 'system/js/user/registration.js', 'requirejs')

<form class="uk-article uk-form uk-form-stacked" action="@url.route('@system/registration/register')" method="post">

    <h1 class="uk-article-title">@trans('User Registration')</h1>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label">@trans('Name')</label>
        <div class="uk-form-controls">
            <input id="form-name" class="uk-form-width-large" type="text" name="user[name]" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-username" class="uk-form-label">@trans('Username')</label>
        <div class="uk-form-controls">
            <input id="form-username" class="uk-form-width-large" type="text" name="user[username]" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-email" class="uk-form-label">@trans('Email')</label>
        <div class="uk-form-controls">
            <input id="form-email" class="uk-form-width-large" type="email" name="user[email]" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-password" class="uk-form-label">@trans('Password')</label>
        <div class="uk-form-controls">
            <input id="form-password" class="uk-form-width-large" type="password" name="user[password]" value="">
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit">@trans('Submit')</button>
    </div>

    @token()

</form>