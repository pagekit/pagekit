<!DOCTYPE html>
<html lang="<?= $app['translator']->getLocale() ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= $view->url()->getStatic('app/modules/theme/favicon.ico') ?>" rel="shortcut icon" type="image/x-icon">
        <link href="<?= $view->url()->getStatic('app/modules/theme/apple_touch_icon.png') ?>" rel="apple-touch-icon-precomposed">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600&amp;subset=<?= $subset ?>" rel="stylesheet">
        <?= $view->section()->render('head') ?>
        <?php $view->style('theme', 'app/modules/theme/css/theme.css') ?>
        <?php $view->script('theme', 'app/modules/theme/js/theme.js', ['uikit', 'uikit-notify', 'uikit-sticky', 'uikit-sortable']) ?>
    </head>
    <body>

        <div class="tm-navbar">
            <div class="uk-container uk-container-center">

                <nav class="uk-navbar">

                    <div class="uk-navbar-brand uk-hidden-small" data-uk-dropdown="{mode:'click'}">

                        <img class="uk-margin-right" src="<?= $view->url()->getStatic('app/modules/system/assets/images/pagekit-logo.svg') ?>" width="24" height="29" alt="Pagekit">

                        <?= __($subnav) ?>

                        <div class="uk-dropdown uk-dropdown-navbar tm-dropdown">
                            <ul class="uk-sortable uk-grid uk-grid-small uk-grid-width-1-3 js-admin-menu" data-url="<?= $view->url('@system/system/adminmenu') ?>" data-uk-sortable="{ dragCustomClass: 'tm-sortable-dragged', handleClass: 'uk-panel' }">
                            <?php foreach ($nav as $item): ?>
                                <li<?= $item->getAttribute('active') ? ' class="uk-active"' : '' ?> data-id="<?= $item->getId() ?>">
                                    <a class="uk-panel pk-panel-icon" href="<?= $view->url($item->getUrl()) ?>">
                                        <img src="<?= $view->url()->getStatic($item->getIcon() ?: 'app/modules/system/assets/images/placeholder-icon.svg') ?>" width="50" height="50" alt="<?= __($item) ?>">
                                        <p><?= __($item) ?></p>
                                    </a>
                                </li>
                            <?php endforeach ?>
                            </ul>
                        </div>

                    </div>

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
                        <img src="<?= $view->url()->getStatic('app/modules/system/assets/images/pagekit-logo.svg') ?>" width="24" height="29" alt="<?= __('Pagekit') ?>">
                    </a>

                    <div class="uk-navbar-flip">

                        <div class="uk-navbar-content uk-hidden-small">
                            <div class="uk-display-inline-block" data-uk-dropdown="{mode:'click'}">
                                <?= $view->gravatar($user->getEmail(), ['size' => 72, 'attrs' => ['width' => '36', 'height' => '36', 'class' => 'uk-border-circle', 'alt' => $user->getUsername()]]) ?>
                                <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li class="uk-nav-header"><?= $user->getUsername() ?></li>
                                        <li><a href="<?= $view->url()->base() ?>" target="_blank"><?= __('Visit Site') ?></a></li>
                                        <li><a href="<?= $view->url('@system/user/edit', ['id' => $user->getId()]) ?>"><?= __('Settings') ?></a></li>
                                        <li><a href="<?= $view->url('@system/auth/logout', ['redirect' => $view->url('@system/admin', [], true)]) ?>"><?= __('Logout') ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

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
        </div>

        <div class="tm-main uk-container uk-container-center">
            <?= $view->section()->render('content') ?>
        </div>

        <div class="uk-hidden"><?= $view->section()->render('messages') ?></div>

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
                                <img class="uk-margin-small-right" src="<?= $view->url()->getStatic($item->getIcon() ?: 'app/modules/system/assets/images/placeholder-icon.svg') ?>" width="34" height="34" alt="<?= __($item) ?>">
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
                    <li><a href="<?= $view->url('@system/user/edit', ['id' => $user->getId()]) ?>"><?= __('Settings') ?></a></li>
                    <li><a href="<?= $view->url('@system/auth/logout', ['redirect' => $view->url('@system/admin', [], true)]) ?>"><?= __('Logout') ?></a></li>
                </ul>

            </div>
        </div>

    </body>
</html>
