<!DOCTYPE html>
<html class="uk-height-1-1">
    <head>
        <title><?= __('Error') ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <link href="<?= $view->url()->getStatic('app/modules/theme/favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $view->url()->getStatic('app/modules/theme/apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <link href="<?= $view->url()->getStatic('app/modules/theme/css/theme.css') ?>" rel="stylesheet">
    </head>
    <body class="uk-height-1-1">

        <div class="tm-height-4-5 uk-vertical-align uk-text-center">
            <div class="uk-vertical-align-middle">

                <div class="tm-container">

                    <img class="uk-margin-bottom" src="<?= $view->url()->getStatic('app/modules/system/assets/images/pagekit-logo-large.svg') ?>" width="120" height="120" alt="<?= __('Pagekit') ?>">

                    <div class="uk-panel uk-panel-box">
                        <h1 class="uk-h2"><?= $title ?></h1>
                    </div>

                </div>

            </div>
        </div>

    </body>
</html>
