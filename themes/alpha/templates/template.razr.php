<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="@url.to('extension://system/theme/favicon.ico')" rel="shortcut icon" type="image/x-icon">
        <link href="@url.to('extension://system/theme/apple_touch_icon.png')" rel="apple-touch-icon-precomposed">
        @action('head')
        @style('theme', 'theme://alpha/css/theme.css')
        @script('jquery')
        @script('uikit')
    </head>
    <body>

        <div class="uk-container uk-container-center">

            @if (position.exists('logo'))
            <div class="tm-logo uk-hidden-small">
                <a href="@url.base" class="tm-brand">@position.render('logo', ['renderer' => 'blank'])</a>
            </div>
            @endif

            @if (position.exists('navbar'))
            <div class="tm-navbar">

                <nav class="uk-navbar uk-hidden-small">
                    @position.render('navbar', ['renderer' => 'navbar', 'classes' => 'uk-hidden-small'])
                </nav>

                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>

                @if (position.exists('logo-small'))
                <div class="uk-navbar-content uk-navbar-center uk-visible-small">
                    <a href="@url.base" class="tm-brand">@position.render('logo-small', ['renderer' => 'blank'])</a>
                </div>
                @endif

            </div>
            @endif

            @action('messages')

            @if (position.exists('top'))
            <section class="uk-grid uk-grid-divider" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
                @position.render('top', ['renderer' => 'grid'])
            </section>
            @endif

            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match>

                <div class="@theme.classes.columns.main.class">
                    @action('content')
                </div>

                @if (position.exists('sidebar-a'))
                <aside class="@theme.classes.columns['sidebar-a'].class">
                    @position.render('sidebar-a', ['renderer' => 'panel'])
                </aside>
                @endif

                @if (position.exists('sidebar-b'))
                <aside class="@theme.classes.columns['sidebar-b'].class">
                    @position.render('sidebar-b', ['renderer' => 'panel'])
                </aside>
                @endif

            </div>

            @if (position.exists('footer'))
            <footer class="uk-grid" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
               @position.render('footer', ['renderer' => 'grid'])
            </footer>
            @endif

        </div>

        @if (position.exists('offcanvas'))
        <div id="offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">
                @position.render('offcanvas', ['renderer' => 'offcanvas'])
            </div>
        </div>
        @endif

    </body>
</html>
