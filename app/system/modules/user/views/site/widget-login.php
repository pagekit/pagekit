<form class="uk-form" action="<?= $view->url('@user/authenticate') ?>" method="post">

    <div class="uk-form-row">
        <input class="uk-width-1-1" type="text" name="credentials[username]" value="<?= $last_username ?>" placeholder="<?= __('username') ?>" autofocus>
    </div>
    <div class="uk-form-row">
        <input class="uk-width-1-1" type="password" name="credentials[password]" value="" placeholder="<?= __('password') ?>">
    </div>
    <div class="uk-form-row">
        <input class="uk-button uk-button-primary uk-width-1-1" type="submit" value="<?= __('Login') ?>">
    </div>

    <p>
        <label><input type="checkbox" name="<?= $remember_me_param ?>"> <?= __('Remember Me') ?></label>
        <br><a href="<?= $view->url('@user/resetpassword') ?>"><?= __('Forgot Password?') ?></a>
        <?php if ($app['module']->get('system/user')->config('registration') != 'admin'): ?>
        <br><a href="<?= $view->url('@user/registration') ?>"><?= __('Sign up') ?></a>
        <?php endif ?>
    </p>

    <input type="hidden" name="redirect" value="<?= $redirect ?>">
    <?php $view->token()->get() ?>

</form>
