<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        @action('head')
        @style('theme', 'theme://alpha/css/theme.css')
        @script('jquery')
        @script('uikit')
    </head>
    <body>

        <div class="uk-container uk-container-center">

            @if (position.exists('logo'))
            <div class="uk-text-center uk-margin-large uk-margin-large-top">
                <a href="@url.base" class="tm-brand">@position.render('logo', ['renderer' => 'blank'])</a>
                <a href="#offcanvas" class="uk-navbar-toggle uk-hidden-large tm-navbar-toggle" data-uk-offcanvas></a>
            </div>
            @endif

            @if (position.exists('navbar'))
            <div class="tm-navbar u-margin-large">
                <nav class="uk-navbar uk-hidden-small">
                    @position.render('navbar', ['renderer' => 'navbar'])
                </nav>
            </div>
            @endif

            @action('messages')

            @if (position.exists('top'))
            <section class="uk-grid uk-grid-divider" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
                @position.render('top', ['renderer' => 'grid'])
            </section>
            @endif

            <div class="uk-grid">
                <div class="uk-width-medium-2-3 uk-width-large-3-4">
                    @action('content')
                </div>
                <div class="uk-width-medium-1-3 uk-width-large-1-4">
                    @position.render('sidebar', ['renderer' => 'panel'])
                </div>
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
                <nav class="uk-navbar uk-hidden-small">
                    @position.render('offcanvas', ['renderer' => 'offcanvas'])
                </nav>
            </div>
        </div>
        @endif

    </body>
</html>
