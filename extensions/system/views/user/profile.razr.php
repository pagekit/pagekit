<article class="uk-article">

    <h1 class="uk-article-title">@trans('Your Profile')</h1>

    <form class="js-settings uk-form uk-form-horizontal" action="@url.route('@system/profile/save')" method="post">

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

        <p><button class="uk-button uk-button-primary" type="submit">@trans('Save')</button></p>

        @token()

    </form>

</article>