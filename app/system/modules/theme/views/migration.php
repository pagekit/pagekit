<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= $view->url()->getStatic('system/theme:favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $view->url()->getStatic('system/theme:apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <?= $view->render('head') ?>
        <?php $view->style('theme', 'system/theme:css/theme.css') ?>
        <?php $view->script('uikit') ?>
    </head>
    <body>

        <div class="uk-height-viewport uk-flex uk-flex-center uk-flex-middle uk-text-center">
            <div class="tm-container">

                <img class="uk-margin-large-bottom" src="<?= $view->url()->getStatic('app/system/assets/images/pagekit-logo-large-black.svg') ?>" alt="Pagekit">

                <form class="uk-panel uk-panel-box" action="<?= $view->url('@system/migration/migrate') ?>">
                    <h1><?= __('Update Pagekit') ?></h1>
                    <p><?= __('Pagekit has been updated! Before we send you on your way, we have to update your database to the newest version.') ?></p>
                    <p>
                        <?php if ($redirect): ?>
                            <input type="hidden" name="redirect" value="<?php echo $redirect ?>">
                        <?php endif; ?>
                        <button class="uk-button uk-button-primary" type="submit" value=""><?= __('Update') ?></button>
                        <?php $view->token()->get() ?>
                    </p>
                </form>

            </div>
        </div>

    </body>
</html>
