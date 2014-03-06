<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>@trans('Pagekit Installer')</title>
        @action('head')
        @style('theme', 'installer/css/installer.css')
        @script('installer', 'installer/js/installer.js', 'requirejs')
    </head>
    <body>

        <div id="installer" data-route="@url.to('installer')" data-config="@config">

            <div data-step="start" class="tm-slide uk-vertical-align uk-text-center uk-hidden">
                <div class="js-panel uk-vertical-align-middle tm-panel">

                    <form action="@url.to('@installer/installer/createconfig')" method="post">

                        <img class="tm-logo" src="@url.to('extension://system/assets/images/pagekit-logo-large.svg')" width="120" height="120" alt="Pagekit">

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
                            <label class="uk-form-label" for="">@trans('Hostname')</label>
                            <div class="uk-form-controls">
                                <input class="uk-width-1-1" type="text" name="config[database.connections.mysql.host]" value="localhost" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Host cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label class="uk-form-label" for="">@trans('User')</label>
                            <div class="uk-form-controls">
                                <input class="uk-width-1-1" type="text" name="config[database.connections.mysql.user]" value="" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('User cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label class="uk-form-label" for="">@trans('Password')</label>
                            <div class="uk-form-controls"><input class="uk-width-1-1" type="text" name="config[database.connections.mysql.password]" value="" autocomplete="off"></div>
                        </div>

                        <div class="uk-form-row">
                            <label class="uk-form-label" for="">@trans('Database Name')</label>
                            <div class="uk-form-controls">
                                <input class="uk-width-1-1" type="text" name="config[database.connections.mysql.dbname]" value="pagekit" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Database name cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label class="uk-form-label" for="">@trans('Table Prefix')</label>
                            <div class="uk-form-controls"><input class="uk-width-1-1" type="text" name="config[database.connections.mysql.prefix]" value="pk_"></div>
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
                            <label class="uk-form-label" for="">@trans('Username')</label>
                            <div class="uk-form-controls">
                                <input class="uk-width-1-1" type="text" name="user[username]" value="admin" tabindex="1" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Username cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label class="uk-form-label" for="">@trans('Email')</label>
                            <div class="uk-form-controls">
                                <input class="uk-width-1-1" type="email" name="user[email]" tabindex="2" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Field must be a valid email address.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label class="uk-form-label" for="">@trans('Password')</label>
                            <div class="uk-form-controls">
                                <div class="uk-form-password uk-width-1-1">
                                    <input class="uk-width-1-1" type="password" name="user[password]" data-error-message="#js-user-password-error" required>
                                    <a href="" class="uk-form-password-toggle" data-uk-form-password>@trans('Show')</a>
                                </div>
                                <p id="js-user-password-error" class="uk-form-help-block uk-text-danger">@trans('Password cannot be blank.')</p>
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
                            <label class="uk-form-label" for="">@trans('Name')</label>
                            <div class="uk-form-controls">
                                <input class="uk-width-1-1" type="text" name="option[system:app.site_title]" required>
                                <p class="uk-form-help-block uk-text-danger">@trans('Site name cannot be blank.')</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label class="uk-form-label" for="">@trans('Description')</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-width-1-1" name="option[system:app.site_description]"></textarea>
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
                            <a class="uk-button uk-button-primary" href="@url.to('admin')">@trans('Login now')</a>
                        </p>

                    </div>

                </div>
            </div>

        </div>

    </body>
</html>