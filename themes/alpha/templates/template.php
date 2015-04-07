<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= $view->url()->getStatic('app/system/modules/theme/favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $view->url()->getStatic('app/system/modules/theme/apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <?= $view->render('head') ?>
        <?php $view->style('theme', 'themes/alpha/css/theme.css') ?>
        <?php $view->script('jquery') ?>
        <?php $view->script('uikit') ?>
    </head>
    <body>

        <div class="uk-container uk-container-center">

            <?php if ($view->section()->exists('logo')) : ?>
            <div class="tm-logo uk-hidden-small">
                <a href="<?= $view->url() ?>" class="tm-brand"><?= $view->render('logo', ['renderer' => 'blank']) ?></a>
            </div>
            <?php endif ?>

            <?php if ($view->section()->exists('navbar')) : ?>
            <div class="tm-navbar">

                <nav class="uk-navbar uk-hidden-small">
                    <?= $view->render('navbar', ['renderer' => 'navbar']) ?>
                </nav>

                <?php if ($view->section()->exists('offcanvas')) : ?>
                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
                <?php endif ?>

                <?php if ($view->section()->exists('logo-small')) : ?>
                <div class="uk-navbar-content uk-navbar-center uk-visible-small">
                    <a href="<?= $view->url() ?>" class="tm-brand"><?= $view->render('logo-small', ['renderer' => 'blank']) ?></a>
                </div>
                <?php endif ?>

            </div>
            <?php endif ?>

            <?= $view->render('messages') ?>

            <?php if ($view->section()->exists('top')) : ?>
            <section class="uk-grid uk-grid-divider" data-uk-grid-match="{ target: '> div > .uk-panel' }" data-uk-grid-margin>
                <?= $view->render('top', ['renderer' => 'grid']) ?>
            </section>
            <?php endif ?>

            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match>

                <div class="<?= $theme->getClasses()['columns']['main']['class'] ?>">
                    <?= $view->render('content') ?>
                </div>

                <?php if ($view->section()->exists('sidebar-a')) : ?>
                <aside class="<?= $theme->getClasses()['columns']['sidebar-a']['class'] ?>">
                    <?= $view->render('sidebar-a', ['renderer' => 'panel']) ?>
                </aside>
                <?php endif ?>

                <?php if ($view->section()->exists('sidebar-b')) : ?>
                <aside class="<?= $theme->getClasses()['columns']['sidebar-b']['class'] ?>">
                    <?= $view->render('sidebar-b', ['renderer' => 'panel']) ?>
                </aside>
                <?php endif ?>

            </div>

            <?php if ($view->section()->exists('footer')) : ?>
            <footer class="uk-grid" data-uk-grid-match="{ target: '> div > .uk-panel' }" data-uk-grid-margin>
                <?= $view->render('footer', ['renderer' => 'grid']) ?>
            </footer>
            <?php endif ?>

        </div>

        <?php if ($view->section()->exists('offcanvas')) : ?>
        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">
                <?= $view->render('offcanvas', ['renderer' => 'offcanvas']) ?>
            </div>
        </div>
        <?php endif ?>

    </body>
</html>
