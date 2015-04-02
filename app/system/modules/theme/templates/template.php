<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', $app['translator']->getLocale()) ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= $view->url()->getStatic('app/system/modules/theme/favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $view->url()->getStatic('app/system/modules/theme/apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600&amp;subset=<?= $subset ?>" rel="stylesheet">
        <?php $view->style('theme', 'app/system/modules/theme/css/theme.css') ?>
        <?php $view->script('theme', 'app/system/modules/theme/js/theme.js', ['uikit', 'uikit-notify', 'uikit-sticky', 'uikit-sortable']) ?>
        <?= $view->render('head') ?>
    </head>
    <body>

        <header class="tm-header">
            <div class="uk-container uk-container-center">

                <div class="tm-headerbar uk-flex uk-flex-space-between uk-flex-middle uk-hidden-small">
                    <div class="tm-header-primary uk-flex uk-flex-middle" data-uk-dropdown="{mode:'click'}">

                        <img class="tm-icon" src="<?= $view->url()->getStatic('app/system/assets/images/pagekit-logo.svg') ?>" width="24" height="29" alt="Pagekit">

                        <h1 class="tm-heading"><?= __($subnav) ?></h1>

                        <div class="uk-dropdown uk-dropdown-navbar tm-dropdown">
                            <ul class="uk-sortable uk-grid uk-grid-small uk-grid-width-1-3 js-admin-menu" data-url="<?= $view->url('@system/system/adminmenu') ?>" data-uk-sortable="{ dragCustomClass: 'tm-sortable-dragged', handleClass: 'uk-panel' }">
                            <?php foreach ($nav as $item): ?>
                                <li<?= $item->getAttribute('active') ? ' class="uk-active"' : '' ?> data-id="<?= $item->getId() ?>">
                                    <a class="uk-panel pk-panel-icon" href="<?= $view->url($item->getUrl()) ?>">
                                        <img src="<?= $view->url()->getStatic($item->getIcon() ?: 'app/system/assets/images/placeholder-icon.svg') ?>" width="50" height="50" alt="<?= __($item) ?>">
                                        <p><?= __($item) ?></p>
                                    </a>
                                </li>
                            <?php endforeach ?>
                            </ul>
                        </div>

                    </div>
                    <div class="tm-contrast">

                        <ul class="uk-grid uk-grid-small uk-flex-middle">
                            <li><a class="uk-icon-hover uk-icon-small uk-icon-home" href="<?= $view->url()->base() ?>" title="<?= __('Visit Site') ?>" target="_blank"></a></li>
                            <li><a class="uk-icon-hover uk-icon-small uk-icon-sign-out" href="<?= $view->url('@user/auth/logout', ['redirect' => $view->url('@system/admin', [], true)]) ?>" title="<?= __('Logout') ?>"></a></li>
                            <li><a href="<?= $view->url('@user/edit', ['id' => $user->getId()]) ?>" title="<?= __('Profile') ?>">
                                <?= $view->gravatar($user->getEmail(), ['size' => 64, 'attrs' => ['width' => '32', 'height' => '32', 'class' => 'uk-border-circle', 'alt' => $user->getUsername()]]) ?>
                                <?= $user->getUsername() ?>
                            </a></li>
                        </ul>

                    </div>
                </div>

                <nav class="uk-navbar tm-navbar">

                    <?php if ($subnav->getChildren()) : ?>
                    <ul class="uk-navbar-nav uk-hidden-small">
                        <?php foreach ($subnav->getChildren() as $item): ?>
                        <li<?= $item->getAttribute('active') ? ' class="uk-active"' : '' ?>>
                            <a href="<?= $view->url($item->getUrl()) ?>"><?= __($item) ?></a>
                        </li>
                        <?php endforeach ?>
                    </ul>
                    <?php endif ?>

                    <a class="uk-navbar-content uk-visible-small" href="#offcanvas" data-uk-offcanvas>
                        <img src="<?= $view->url()->getStatic('app/system/assets/images/pagekit-logo.svg') ?>" width="24" height="29" alt="<?= __('Pagekit') ?>">
                    </a>

                    <div class="uk-navbar-flip">

                        <a class="uk-navbar-content uk-visible-small" href="#offcanvas-flip" data-uk-offcanvas>
                            <?= $view->gravatar($user->getEmail(), ['size' => 72, 'attrs' => ['width' => '36', 'height' => '36', 'class' => 'uk-border-circle', 'alt' => $user->getUsername()]]) ?>
                        </a>

                    </div>

                    <div class="uk-navbar-content uk-navbar-center uk-visible-small">
                        <?php
                            if ($subnav->getChildren()) {
                                foreach ($subnav->getChildren() as $item) {
                                    echo $subnav->getAttribute('active') ? __($item) : '';
                                }
                            } else {
                                echo __($subnav);
                            }
                        ?>
                    </div>

                </nav>

            </div>
        </header>

        <main class="tm-main uk-container uk-container-center">
            <?= $view->render('content') ?>
        </main>

        <div class="uk-hidden"><?= $view->render('messages') ?></div>

        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">

                <ul class="uk-nav uk-nav-offcanvas">
                    <?php if ($subnav->getChildren()): ?>
                        <li class="uk-nav-header"><?= __($subnav) ?></li>
                        <li<?= $subnav->getAttribute('active') ? ' class="uk-active"' : '' ?>>
                            <a href="<?= $view->url($subnav->getUrl()) ?>"><?= __($subnav) ?></a>
                        </li>
                        <?php foreach ($subnav->getChildren() as $item): ?>
                            <li<?= $item->getAttribute('active') ? ' class="uk-active"' : '' ?>>
                                <a href="<?= $view->url($item->getUrl()) ?>"><?= __($item) ?></a>
                            </li>
                        <?php endforeach ?>
                        <li class="uk-nav-divider"></li>
                    <?php endif ?>
                    <li class="uk-nav-header"><?= __('Extensions') ?></li>
                    <?php foreach ($nav->getChildren() as $item): ?>
                        <li<?= $item->getAttribute('active') ? ' class="uk-active"' : '' ?>>
                            <a href="<?= $view->url($item->getUrl()) ?>">
                                <img class="uk-margin-small-right" src="<?= $view->url()->getStatic($item->getIcon() ?: 'app/system/assets/images/placeholder-icon.svg') ?>" width="34" height="34" alt="<?= __($item) ?>">
                                <?= __($item) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>

            </div>
        </div>

        <div id="offcanvas-flip" class="uk-offcanvas">
            <div class="uk-offcanvas-bar uk-offcanvas-bar-flip">

                <ul class="uk-nav uk-nav-offcanvas">
                    <li class="uk-nav-header"><?= $user->getUsername() ?></li>
                    <li><a href="<?= $view->url()->base() ?>" target="_blank"><?= __('Visit Site') ?></a></li>
                    <li><a href="<?= $view->url('@user/edit', ['id' => $user->getId()]) ?>"><?= __('Settings') ?></a></li>
                    <li><a href="<?= $view->url('@user/auth/logout', ['redirect' => $view->url('@system/admin', [], true)]) ?>"><?= __('Logout') ?></a></li>
                </ul>

            </div>
        </div>

    </body>
</html>
