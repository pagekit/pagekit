<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= $view->render('head') ?>
        <?php $view->style('theme', 'alpha:css/theme.css') ?>
        <?php $view->script('jquery') ?>
        <?php $view->script('uikit') ?>
    </head>
    <body>

        <div class="uk-container uk-container-center">

            <?php if ($view->position()->exists('logo')) : ?>
            <div class="tm-logo uk-hidden-small">
                <a href="<?= $view->url()->get() ?>" class="tm-brand"><?= $view->position('logo') ?></a>
            </div>
            <?php endif ?>

            <?php if ($view->position()->exists('navbar') || $view->menu()->exists('main')) : ?>
            <div class="tm-navbar">

                <nav class="uk-navbar uk-hidden-small">

                    <?php if ($view->position()->exists('navbar')) : ?>
                    <?= $view->position('navbar', 'navbar') ?>
                    <?php endif ?>

                    <?php if ($view->menu()->exists('main')) : ?>
                        <?= $view->menu('main', 'menu-navbar') ?>
                    <?php endif ?>

                </nav>

                <?php if ($view->position()->exists('offcanvas')) : ?>
                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
                <?php endif ?>

                <?php if ($view->position()->exists('logo-small')) : ?>
                <div class="uk-navbar-content uk-navbar-center uk-visible-small">
                    <a href="<?= $view->url()->get() ?>" class="tm-brand"><?= $view->position('logo-small') ?></a>
                </div>
                <?php endif ?>

            </div>
            <?php endif ?>

            <?= $view->render('messages') ?>

            <?php if ($view->position()->exists('top')) : ?>
            <section class="uk-grid uk-grid-divider" data-uk-grid-match="{ target: '> div > .uk-panel' }" data-uk-grid-margin>
                <?= $view->position('top', 'grid') ?>
            </section>
            <?php endif ?>

            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match>

                <div class="<?= $theme->getClasses('columns.main.class') ?>">
                    <?= $view->render('content') ?>
                </div>

                <?php if ($view->position()->exists('sidebar-a')) : ?>
                <aside class="<?= $theme->getClasses('columns.sidebar-a.class') ?>">
                    <?= $view->position('sidebar-a', 'panel') ?>
                </aside>
                <?php endif ?>

                <?php if ($view->position()->exists('sidebar-b')) : ?>
                <aside class="<?= $theme->getClasses('columns.sidebar-b.class') ?>">
                    <?= $view->position('sidebar-b', 'panel') ?>
                </aside>
                <?php endif ?>

            </div>

            <?php if ($view->position()->exists('footer')) : ?>
            <footer class="uk-grid" data-uk-grid-match="{ target: '> div > .uk-panel' }" data-uk-grid-margin>
                <?= $view->position('footer', 'grid') ?>
            </footer>
            <?php endif ?>

        </div>

        <?php if ($view->position()->exists('offcanvas') || $view->menu()->exists('offcanvas')) : ?>
        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">

                <?php if ($view->position()->exists('offcanvas')) : ?>
                    <?= $view->position('offcanvas', 'offcanvas') ?>
                <?php endif ?>

                <?php if ($view->menu()->exists('offcanvas')) : ?>
                    <?= $view->menu('offcanvas') ?>
                <?php endif ?>

            </div>
        </div>
        <?php endif ?>

        <?= $view->render('footer') ?>

    </body>
</html>
