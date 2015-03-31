<!DOCTYPE html>
<html class="uk-height-1-1">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= $view->url()->getStatic('app/modules/theme/favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $view->url()->getStatic('app/modules/theme/apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <?= $view->render('head') ?>
        <?php $view->style('theme', 'app/modules/theme/css/theme.css') ?>
        <?php $view->script('login', 'app/modules/theme/js/login.js', ['uikit']) ?>
    </head>
    <body class="uk-height-1-1">

      <div class="tm-height-4-5 uk-vertical-align uk-text-center">
            <div class="uk-vertical-align-middle">

                <div class="tm-container tm-container-small">

                    <img class="uk-margin-bottom" src="<?= $view->url()->getStatic('app/modules/system/assets/images/pagekit-logo-large.svg') ?>" width="120" height="120" alt="Pagekit">

                    <?= $view->section()->render('messages') ?>

                    <form class="js-login js-toggle uk-panel uk-panel-box uk-form" action="<?= $view->url('@user/auth/authenticate') ?>" method="post">

                        <div class="uk-form-row">
                            <input class="uk-form-large uk-width-1-1" type="text" name="credentials[username]" value="<?= $last_username ?>" placeholder="<?= __('Username') ?>" autofocus>
                        </div>
                        <div class="uk-form-row">
                            <div class="uk-form-password uk-width-1-1">
                                <input class="uk-form-large uk-width-1-1" type="password" name="credentials[password]" value="" placeholder="<?= __('Password') ?>">
                            </div>
                        </div>
                        <div class="uk-form-row">
                            <button class="uk-button uk-button-primary uk-button-large uk-width-1-1"><?= __('Login') ?></button>
                        </div>
                        <div class="uk-form-row uk-text-small">
                            <label class="uk-float-left"><input type="checkbox" name="<?= $remember_me_param ?>"> <?= __('Remember Me') ?></label>
                            <a class="uk-float-right uk-link uk-link-muted" data-uk-toggle="{ target: '.js-toggle' }"><?= __('Forgot Password?') ?></a>
                        </div>

                        <?php $view->token()->get() ?>
                        <input type="hidden" name="redirect" value="<?= $redirect ?>">

                    </form>

                    <form class="js-toggle uk-panel uk-panel-box uk-form uk-hidden" action="<?= $view->url('@system/resetpassword/reset') ?>" method="post">

                        <div class="uk-form-row">
                            <input class="uk-form-large uk-width-1-1" type="text" name="email" value="" placeholder="<?= __('Email') ?>" required>
                        </div>
                        <div class="uk-form-row">
                            <button class="uk-button uk-button-primary uk-button-large uk-width-1-1"><?= __('Reset Password') ?></button>
                        </div>

                        <?php $view->token()->get() ?>

                    </form>

                </div>

            </div>
        </div>

    </body>
</html>
