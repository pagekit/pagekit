<form class="uk-article uk-form uk-form-stacked" action="<?= $view->url('@user/authenticate') ?>" method="post">

    <h1 class="uk-article-title"><?= __('Login') ?></h1>

    <div class="uk-form-row">
        <label for="form-username" class="uk-form-label"><?= __('Username') ?></label>
        <div class="uk-form-controls">
            <input id="form-username" type="text" name="credentials[username]" value="<?= $last_username ?>" required autofocus>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-password" class="uk-form-label"><?= __('Password') ?> <a href="<?= $view->url('@user/resetpassword') ?>"><?= __('(Forgot Password?)') ?></a></label>
        <div class="uk-form-controls">
            <input id="form-password" type="password" name="credentials[password]" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Submit') ?></button>
    </div>

    <p>
        <label><input type="checkbox" name="<?= $remember_me_param ?>"> <?= __('Remember Me') ?></label><br>
    </p>

    <input type="hidden" name="redirect" value="<?= $redirect ?>">
    <?php $view->token()->get() ?>

</form>
