<!DOCTYPE html>
<html class="uk-height-1-1">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= $app['url']->getStatic('extensions/system/modules/theme/favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $app['url']->getStatic('extensions/system/modules/theme/apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <?= $app['sections']->render('head') ?>
        <?php $app['styles']->queue('theme', 'extensions/system/modules/theme/css/theme.css') ?>
        <?php $app['scripts']->queue('uikit') ?>
    </head>
    <body class="uk-height-1-1">

        <div class="tm-height-4-5 uk-vertical-align uk-text-center">
            <div class="uk-vertical-align-middle">

                <div class="tm-container">

                    <img class="uk-margin-bottom" src="<?= $app['url']->getStatic('extensions/system/assets/images/pagekit-logo-large.svg') ?>" width="120" height="120" alt="Pagekit">

                    <div class="uk-panel uk-panel-box">
                        <h1><?= __('Maintenance') ?></h1>
                        <p><?= $message ?></p>
                    </div>

                </div>

            </div>
        </div>

    </body>
</html>
