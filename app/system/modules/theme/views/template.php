<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', $app['intl']->getDefaultLocale()) ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600&amp;subset=<?= $subset ?>" rel="stylesheet">
        <?php $view->style('theme', 'system/theme:css/theme.css') ?>
        <?php $view->script('theme', 'system/theme:js/theme.js', ['vue', 'uikit', 'uikit-notify', 'uikit-tooltip', 'uikit-sticky', 'uikit-sortable', 'uikit-pagination', 'uikit-form-select']) ?>
        <?= $view->render('head') ?>
    </head>
    <body>

        <header id="header" class="tm-header">

            <!-- <div class="tm-brand uk-flex uk-flex-middle">
                <img src="<?= $view->url()->getStatic('app/system/assets/images/pagekit-logo.svg') ?>" width="24" height="29" alt="Pagekit">
            </div> -->

            <div class="uk-container uk-container-center">

                <div class="tm-headerbar uk-flex uk-flex-space-between uk-flex-middle uk-hidden-small">
                    <div class="tm-headerbar-primary uk-flex uk-flex-middle" data-uk-dropdown="{mode:'click'}">

                        <i class="tm-icon-menu"></i>

                        <h1 class="tm-heading" v-text="item.label | trans"></h1>

                        <div class="uk-dropdown uk-dropdown-navbar tm-dropdown">
                            <ul class="uk-sortable uk-grid uk-grid-small uk-grid-width-1-3" data-url="<?= $view->url('@system/adminmenu') ?>" data-uk-sortable="{ dragCustomClass: 'tm-sortable-dragged', handleClass: 'uk-panel' }"  v-el="appnav">
                                <li v-repeat="item: nav" data-id="{{ item.id }}">
                                    <a class="uk-panel tm-panel-icon" v-attr="href: item.url">
                                        <img width="50" height="50" alt="{{ item.label | trans }}" v-attr="src: item.icon">
                                        <p>{{ item.label | trans }}</p>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="tm-contrast">

                        <ul class="uk-grid uk-grid-small uk-flex-middle">
                            <li><a class="tm-icon-visit" href="{{ $url() }}" title="{{ 'Visit Site' | trans }}" target="_blank"></a></li>
                            <li><a class="tm-icon-logout" href="{{ $url('user/logout', {redirect: 'admin/login'}) }}" title="{{ 'Logout' | trans }}"></a></li>
                            <li class="uk-margin-small-left"><a href="{{ $url('admin/user/edit', {id: user.id}) }}" title="{{ 'Profile' | trans }}"><img class="uk-border-circle uk-margin-small-right uk-invisible" height="32" width="32" title="{{ user.name }}" v-gravatar="user.email"> <span v-text="user.username"></span></a></li>
                        </ul>

                    </div>
                </div>

                <nav class="uk-navbar tm-navbar uk-hidden-small" v-show="subnav">
                    <ul class="uk-navbar-nav">
                        <li v-class="uk-active: item.active" v-repeat="item: subnav">
                            <a v-attr="href: item.url" v-text="item.label | trans"></a>
                        </li>
                    </ul>
                </nav>

                <div class="tm-headerbar uk-flex uk-flex-space-between uk-flex-middle uk-visible-small">
                    <a class="tm-icon-menu" href="#offcanvas" data-uk-offcanvas></a>

                    <h1 class="tm-heading uk-h3">{{ item.label | trans }}</h1>

                    <a href="#offcanvas-flip" data-uk-offcanvas>
                        <img class="uk-border-circle" height="36" width="36" alt="{{ user.username }}" v-gravatar="user.email">
                    </a>
                </div>

            </div>
        </header>

        <main class="tm-main uk-container uk-container-center">
            <?= $view->render('content') ?>
        </main>

        <div class="uk-hidden"><?= $view->render('messages') ?></div>

        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">

                <!-- <img src="<?= $view->url()->getStatic('app/system/assets/images/pagekit-logo.svg') ?>" width="24" height="29" alt="<?= __('Pagekit') ?>"> -->

                <ul class="uk-nav uk-nav-offcanvas">
                    <li class="uk-nav-header" v-show="subnav">{{ item.label | trans }}</li>
                    <li v-class="uk-active: item.active" v-repeat="item: subnav">
                        <a v-attr="href: item.url">{{ item.label | trans }}</a>
                    </li>
                    <li class="uk-nav-divider" v-show="subnav"></li>
                    <li class="uk-nav-header">{{ 'Extensions' | trans }}</li>
                    <li v-class="uk-active: item.active" v-repeat="item: nav">
                        <a v-attr="href: item.url">
                            <img class="uk-margin-small-right" width="34" height="34" alt="{{ item.label | trans }}" v-attr="src: item.icon"> {{ item.label | trans }}
                        </a>
                    </li>
                </ul>

            </div>
        </div>

        <div id="offcanvas-flip" class="uk-offcanvas">
            <div class="uk-offcanvas-bar uk-offcanvas-bar-flip">

                <ul class="uk-nav uk-nav-offcanvas">
                    <li class="uk-nav-header">{{ user.username }}</li>
                    <li><a href="{{ $url() }}" target="_blank">{{ 'Visit Site' | trans }}</a></li>
                    <li><a href="{{ $url('admin/user/edit', {id: user.id}) }}">{{ 'Settings' | trans }}</a></li>
                    <li><a href="{{ $url('user/logout', {redirect: 'admin/login'}) }}">{{ 'Logout' | trans }}</a></li>
                </ul>

            </div>
        </div>

    </body>
</html>
