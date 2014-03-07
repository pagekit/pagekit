<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @action('head')
        @style('theme', 'extension://system/theme/css/theme.css')
        @script('theme', 'extension://system/theme/js/theme.js', ['jquery', 'uikit', 'uikit-notify'])
    </head>
    <body>

        <div class="tm-navbar">
            <div class="uk-container uk-container-center">

            	<nav class="uk-navbar">

	                <a class="uk-navbar-brand uk-hidden-small" href="@url('@system/system/admin')">
                        <img src="@url('extension://system/assets/images/pagekit-logo.svg')" width="23" height="29" alt="@trans('Pagekit')">
                    </a>

                    @include('extension://system/theme/views/menu/main.razr.php', ['root' => nav])

                    <div class="uk-navbar-flip">

                        <div class="uk-navbar-content" data-uk-dropdown>
                            @set(user = app.users.get())
                            <a href="#" title="@user.username">@gravatar(user.email, ['size' => 72, 'attrs' => ['width' => '36', 'height' => '36', 'class' => 'uk-border-circle', 'alt' => user.username]])</a>
                            <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                                <ul class="uk-nav uk-nav-dropdown">
                                    <li class="uk-nav-header">@user.username</li>
                                    <li><a href="@url('@system/user/edit', ['id' => user.id])">@trans('Settings')</a></li>
                                    <li><a href="@url('@system/auth/logout', ['redirect' => url('@system/system/admin', [], true)])">@trans('Logout')</a></li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <a href="#tm-offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>

                    <a class="uk-navbar-brand uk-navbar-center uk-visible-small" href="@url('@system/system/admin')">
                        <img src="@url('extension://system/assets/images/pagekit-logo.svg')" width="23" height="29" alt="@trans('Pagekit')">
                    </a>

           		</nav>

           	</div>
        </div>

        <div class="uk-container uk-container-center">

            @action('messages')

            @include('extension://system/theme/views/menu/subnav.razr.php', ['root' => subnav])

            @set(title = app.view.get('head.title'))
            @if (title)
            <h1 class="tm-heading">@title</h1>
            @endif

            @if (app.view.get('theme.boxed', true))
        	<div class="tm-main uk-panel uk-panel-box">@action('content')</div>
            @else
            @action('content')
            @endif

        </div>

        <div id="tm-offcanvas" class="uk-offcanvas">
            <div class="uk-offcanvas-bar">

                @include('extension://system/theme/views/menu/offcanvas.razr.php', ['root' => nav])

            </div>
        </div>

    </body>
</html>
