<form class="uk-article uk-form uk-form-stacked" action="<?= $view->url('@user/authenticate') ?>" method="post">

    <h1 class="uk-article-title"><?= __('Login') ?></h1>

    <div class="uk-form-row">
        <label for="form-username" class="uk-form-label"><?= __('Username') ?></label>
        <div class="uk-form-controls">
            <input id="form-username" class="uk-form-width-medium" type="text" name="credentials[username]" value="<?= $last_username ?>" required autofocus>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-password" class="uk-form-label"><?= __('Password') ?></label>
        <div class="uk-form-controls">
            <input id="form-password" class="uk-form-width-medium" type="password" name="credentials[password]" value="" required>
        </div>
    </div>

    <p class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Login') ?></button>
    </p>

    <ul class="uk-list">
        <li><label><input type="checkbox" name="<?= $remember_me_param ?>"> <?= __('Remember Me') ?></label></li>
        <li class="uk-margin-small-top"> <?= __('Forgot Password?') ?> <a href="<?= $view->url('@user/resetpassword') ?>"><?= __('Request Password') ?></a></li>
        <?php if ($app->module('system/user')->config('registration') != 'admin') : ?>
        <li class="uk-margin-small-top"><?= __('No account yet?') ?> <a href="<?= $view->url('@user/registration') ?>"><?= __('Sign up now') ?></a></li>
        <?php endif ?>
    </ul>

    <input type="hidden" name="redirect" value="<?= $redirect ?>">
    <?php $view->token()->get() ?>

</form>
