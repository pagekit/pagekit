<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>@trans('Pagekit Installer')</title>
        <link href="@url.to('extension://system/theme/favicon.ico')" rel="shortcut icon" type="image/x-icon">
        <link href="@url.to('extension://system/theme/apple_touch_icon.png')" rel="apple-touch-icon-precomposed">
        @action('head')
        @style('theme', 'installer/css/installer.css')
        @script('installer', 'installer/js/installer.js', 'requirejs')
    </head>
    <body>

        <div id="installer" data-route="@url.route('installer')" data-config="@config">

            <div data-step="start" class="tm-slide uk-vertical-align uk-text-center uk-hidden">
                <div class="js-panel uk-vertical-align-middle tm-panel">

                    <form action="@url.route('@installer/installer/createconfig')" method="post">

                        <img class="uk-margin-top" src="@url.to('extension://system/assets/images/pagekit-logo-large.svg')" width="120" height="120" alt="Pagekit">

                        <p>
                            <button class="uk-button" type="submit">@trans('Begin')</button>
                        </p>

                    </form>
                </div>
            </div>

            <div data-step="database" class="tm-slide uk-vertical-align uk-text-center uk-hidden">
                <div class="js-panel uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <h1>@trans('Connect database')</h1>

                    <p>@trans('Enter your database connection details.') </p>

                    <form class="uk-form tm-form-horizontal uk-text-left" action="" method="post">

                        <div class="uk-form-row">
                            <label for="form-dbhost" class="uk-form-label">@trans('Driver')</label>
                            <div class="uk-form-controls">
                                <select class="uk-width-1-1" name="config[database.default]" id="form-dbdriver">
                                    <option value="mysql" selected>MySql</option>
                                    <option value="sqlite">SQLite</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-form-row js-hide-sqlite">
                            <label for="form-dbhost" class="uk-form-label">@trans('Hostname')</label>
                            <div class="uk-form-controls">
                                <input id="form-dbhost" class="uk-width-1-1 js-required" type="text" name="config[database.connections.mysql.host]" value="localhost" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Host cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row js-hide-sqlite">
                            <label for="form-dbuser" class="uk-form-label">@trans('User')</label>
                            <div class="uk-form-controls">
                                <input id="form-dbuser" class="uk-width-1-1 js-required" type="text" name="config[database.connections.mysql.user]" value="" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('User cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row js-hide-sqlite">
                            <label for="form-dbpassword" class="uk-form-label">@trans('Password')</label>
                            <div class="uk-form-controls">
                                <input id="form-dbpassword" class="uk-width-1-1" type="text" name="config[database.connections.mysql.password]" value="" autocomplete="off">
                            </div>
                        </div>

                        <div class="uk-form-row js-hide-sqlite">
                            <label for="form-dbname" class="uk-form-label">@trans('Database Name')</label>
                            <div class="uk-form-controls">
                                <input id="form-dbname" class="uk-width-1-1 js-required" type="text" name="config[database.connections.mysql.dbname]" value="pagekit" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Database name cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="form-dbprefix" class="uk-form-label">@trans('Table Prefix')</label>
                            <div class="uk-form-controls">
                                <input id="form-dbprefix" class="uk-width-1-1" type="text" name="config[database.connections.mysql.prefix]" value="pk_">
                            </div>
                        </div>

                        <p class="uk-text-right">
                            <button class="uk-button uk-button-primary" type="submit">@trans('Next') <i class="uk-icon-arrow-right"></i></button>
                        </p>

                    </form>

                </div>
            </div>

            <div data-step="user" class="tm-slide uk-vertical-align uk-text-center uk-hidden">
                <div class="js-panel uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <h1>@trans('Create your account')</h1>

                    <p>@trans('You will be the site administrator.')</p>

                    <form class="uk-form tm-form-horizontal uk-text-left" action="" method="post">

                        <div class="uk-form-row">
                            <label for="form-username" class="uk-form-label">@trans('Username')</label>
                            <div class="uk-form-controls">
                                <input id="form-username" class="uk-width-1-1" type="text" name="user[username]" value="admin" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Username cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="form-password" class="uk-form-label">@trans('Password')</label>
                            <div class="uk-form-controls">
                                <div class="uk-form-password uk-width-1-1">
                                    <input id="form-password" class="uk-width-1-1" type="password" name="user[password]" data-error-message="#js-user-password-error" required>
                                    <a href="" class="uk-form-password-toggle" data-uk-form-password>@trans('Show')</a>
                                </div>
                                <p id="js-user-password-error" class="uk-form-help-block uk-text-danger">@trans('Password cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="form-email" class="uk-form-label">@trans('Email')</label>
                            <div class="uk-form-controls">
                                <input id="form-email" class="uk-width-1-1" type="email" name="user[email]" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Field must be a valid email address.')</p>
                            </div>
                        </div>

                        <p class="uk-text-right">
                            <button class="uk-button uk-button-primary" type="submit">@trans('Next') <i class="uk-icon-arrow-right"></i></button>
                        </p>

                    </form>

                </div>
            </div>

            <div data-step="site" class="tm-slide uk-vertical-align uk-text-center uk-hidden">
                <div class="js-panel uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <h1>@trans('Setup your site')</h1>

                    <p>@trans('Enter your website details.')</p>

                    <form class="uk-form tm-form-horizontal uk-text-left" action="" method="post">

                        <div class="uk-form-row">
                            <label for="form-sitename" class="uk-form-label">@trans('Name')</label>
                            <div class="uk-form-controls">
                                <input id="form-sitename" class="uk-width-1-1" type="text" name="option[system:app.site_title]" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Site name cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="form-sitedescription" class="uk-form-label">@trans('Description')</label>
                            <div class="uk-form-controls">
                                <textarea id="form-sitedescription" class="uk-width-1-1" name="option[system:app.site_description]"></textarea>
                            </div>
                        </div>

                        <p class="uk-text-right">
                            <button class="uk-button uk-button-primary" type="submit">@trans('Next') <i class="uk-icon-arrow-right"></i></button>
                        </p>

                    </form>

                </div>
            </div>

            <div data-step="finish" class="tm-slide uk-vertical-align uk-text-center uk-hidden">
                <div class="js-panel uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <div data-status="install">
                        <h1>@trans('Installing Pagekit...')</h1>

                        <p>
                            <i class="uk-icon-spinner uk-icon-spin uk-icon-large"></i>
                        </p>
                    </div>

                    <div data-status="fail">
                        <h1>@trans('Installation failed!')</h1>

                        <div class="uk-alert uk-alert-danger js-error-message"></div>

                        <p>
                            <i class="uk-icon-times-circle uk-icon-large"></i>
                        </p>

                        <p>
                            <button type="button" class="uk-button uk-button-primary" onclick="Installer.onfinish()">@trans('Retry')</button>
                        </p>
                    </div>

                    <div data-status="finished">

                        <h1>@trans('Finished!')</h1>

                        <p>
                            <i class="uk-icon-check-circle uk-icon-large"></i>
                        </p>

                        <p>
                            <a class="uk-button uk-button-primary" href="@url.route('admin')">@trans('Login now')</a>
                        </p>

                    </div>

                </div>
            </div>

        </div>

    </body>
</html>