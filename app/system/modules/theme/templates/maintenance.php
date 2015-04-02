<!DOCTYPE html>
<html class="uk-height-1-1">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= $view->url()->getStatic('app/system/modules/theme/favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $view->url()->getStatic('app/system/modules/theme/apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <?= $view->render('head') ?>
        <?php $view->style('theme', 'app/system/modules/theme/css/theme.css') ?>
        <?php $view->script('uikit') ?>
    </head>
    <body class="uk-height-1-1">

        <div class="tm-height-4-5 uk-vertical-align uk-text-center">
            <div class="uk-vertical-align-middle">

                <div class="tm-container">

                    <img class="uk-margin-bottom" src="<?= $view->url()->getStatic('app/system/assets/images/pagekit-logo-large.svg') ?>" width="120" height="120" alt="Pagekit">

                    <div class="uk-panel uk-panel-box">
                        <h1><?= __('Maintenance') ?></h1>
                        <p><?= $message ?></p>
                    </div>

                </div>

            </div>
        </div>

    </body>
</html>
