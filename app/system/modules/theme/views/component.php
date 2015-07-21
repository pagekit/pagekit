<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', $app['translator']->getLocale()) ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600&amp;subset=<?= $subset ?>" rel="stylesheet">
        <?php $view->style('theme', 'system/theme:css/theme.css') ?>
        <?= $view->render('head') ?>
    </head>
    <body>
        <?= $view->render('content') ?>
    </body>
</html>
