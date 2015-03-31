<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="app/modules/theme/favicon.ico" rel="shortcut icon" type="image/x-icon">
        <link href="app/modules/theme/apple_touch_icon.png" rel="apple-touch-icon-precomposed">
        <?php $view->style('installer', 'app/modules/installer/assets/css/installer.css') ?>
        <?php $view->script('locale', 'app/modules/locale/assets/js/locale.js') ?>
        <?php $view->script('jquery', 'vendor/assets/jquery/dist/jquery.min.js') ?>
        <?php $view->script('uikit', 'vendor/assets/uikit/js/uikit.min.js') ?>
        <?php $view->script('uikit-form-password', 'vendor/assets/uikit/js/components/form-password.min.js') ?>
        <?php $view->script('vue', 'vendor/assets/vue/dist/vue.min.js') ?>
        <?php $view->script('vue-validator', 'app/modules/system/app/vue-validator.js') ?>
        <?php $view->script('vue-resource', 'app/modules/system/app/vue-resource.js') ?>
        <?php $view->script('vue-system', 'app/modules/system/app/vue-system.js') ?>
        <?php $view->script('installer', 'app/modules/installer/app/installer.js') ?>
        <?= $view->section()->render('head') ?>
    </head>
    <body>

        <div id="installer">

            <div v-show="step == 'start'" class="tm-slide uk-vertical-align uk-text-center">
                <div class="uk-vertical-align-middle tm-panel">

                    <img class="uk-margin-top" src="app/modules/system/assets/images/pagekit-logo-large.svg" width="120" height="120" alt="Pagekit">
                    <p>
                        <button class="uk-button" v-on="click: step = 'database'">{{ 'Begin' | trans }}</button>
                    </p>

                </div>
            </div>

            <div v-show="step == 'database'" class="tm-slide uk-vertical-align uk-text-center">
                <div class="uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <h1>{{ 'Connect database' | trans }}</h1>
                    <p>{{ 'Enter your database connection details.' | trans }}</p>
                    <div class="uk-alert uk-alert-danger uk-margin" v-show="message"><p>{{ message }}</p></div>

                    <form class="uk-form tm-form-horizontal uk-text-left" name="formDatabase" v-on="valid: stepDatabase">

                        <div class="uk-form-row">
                            <label for="form-dbdriver" class="uk-form-label">{{ 'Driver' | trans }}</label>
                            <div class="uk-form-controls">
                                <select id="form-dbdriver" class="uk-width-1-1" v-model="config.database.default">
                                    <option value="mysql" selected>MySql</option>
                                    <option value="sqlite">SQLite</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-form-row" v-show="config.database.default == 'mysql'">
                            <div class="uk-form-row">
                                <label for="form-mysql-dbhost" class="uk-form-label">{{ 'Hostname' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-mysql-dbhost" class="uk-width-1-1" type="text" value="localhost" name="host" v-model="config.database.connections.mysql.host" v-valid="required">
                                    <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.host.invalid">{{ 'Host cannot be blank.' | trans }}</p>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label for="form-mysql-dbuser" class="uk-form-label">{{ 'User' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-mysql-dbuser" class="uk-width-1-1" type="text" value="" name="user" v-model="config.database.connections.mysql.user" v-valid="required">
                                    <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.user.invalid">{{ 'User cannot be blank.' | trans }}</p>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label for="form-mysql-dbpassword" class="uk-form-label">{{ 'Password' | trans }}</label>
                                <div class="uk-form-controls">
                                    <div class="uk-form-password uk-width-1-1">
                                        <input id="form-mysql-dbpassword" class="uk-width-1-1" type="password" value="" autocomplete="off" v-model="config.database.connections.mysql.password">
                                        <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: 'Show', lblHide: 'Hide' }">{{ 'Show' | trans }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label for="form-mysql-dbname" class="uk-form-label">{{ 'Database Name' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-mysql-dbname" class="uk-width-1-1" type="text" value="pagekit" name="dbname" v-model="config.database.connections.mysql.dbname" v-valid="required">
                                    <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.dbname.invalid">{{ 'Database name cannot be blank.' | trans }}</p>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label for="form-mysql-dbprefix" class="uk-form-label">{{ 'Table Prefix' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-mysql-dbprefix" class="uk-width-1-1" type="text" value="pk_" v-model="config.database.connections.mysql.prefix">
                                </div>
                            </div>
                        </div>

                        <div class="uk-form-row" v-show="config.database.default == 'sqlite'">
                            <div class="uk-form-row">
                                <label for="form-sqlite-dbprefix" class="uk-form-label">{{ 'Table Prefix' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-sqlite-dbprefix" class="uk-width-1-1" type="text" value="pk_" v-model="config.database.connections.sqlite.prefix">
                                </div>
                            </div>
                        </div>

                        <p class="uk-text-right">
                            <button class="uk-button uk-button-primary" type="submit">{{ 'Next' | trans }} <i class="uk-icon-arrow-right"></i></button>
                        </p>

                    </form>

                </div>
            </div>

            <div v-show="step == 'user'" class="tm-slide uk-vertical-align uk-text-center">
                <div class="uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <h1>{{ 'Create your account' | trans }}</h1>
                    <p>{{ 'You will be the site administrator.' | trans }}</p>

                    <form class="uk-form tm-form-horizontal uk-text-left" name="formUser" v-on="valid: stepUser">

                        <div class="uk-form-row">
                            <label for="form-username" class="uk-form-label">{{ 'Username' | trans }}</label>
                            <div class="uk-form-controls">
                                <input id="form-username" class="uk-width-1-1" type="text" value="admin" name="user" v-model="user.username" v-valid="required">
                                <p class="uk-form-help-block uk-text-danger" v-show="formUser.user.invalid">{{ 'Username cannot be blank.' | trans }}</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="form-password" class="uk-form-label">{{ 'Password' | trans }}</label>
                            <div class="uk-form-controls">
                                <div class="uk-form-password uk-width-1-1">
                                    <input id="form-password" class="uk-width-1-1" type="password" name="password" v-model="user.password" v-valid="required">
                                    <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: 'Show', lblHide: 'Hide' }">{{ 'Show' | trans }}</a>
                                </div>
                                <p class="uk-form-help-block uk-text-danger" v-show="formUser.password.invalid">{{ 'Password cannot be blank.' | trans }}</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="form-email" class="uk-form-label">{{ 'Email' | trans }}</label>
                            <div class="uk-form-controls">
                                <input id="form-email" class="uk-width-1-1" type="email" name="email" v-model="user.email" v-valid="email">
                                <p class="uk-form-help-block uk-text-danger" v-show="formUser.email.invalid">{{ 'Field must be a valid email address.' | trans }}</p>
                            </div>
                        </div>

                        <p class="uk-text-right">
                            <button class="uk-button uk-button-primary" type="submit">{{ 'Next' | trans }} <i class="uk-icon-arrow-right"></i></button>
                        </p>

                    </form>

                </div>
            </div>

            <div v-show="step == 'site'" class="tm-slide uk-vertical-align uk-text-center">
                <div class="uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <h1>{{ 'Setup your site' | trans }}</h1>
                    <p>{{ 'Enter your website details.' | trans }}</p>

                    <form class="uk-form tm-form-horizontal uk-text-left" name="formSite" v-on="valid: stepSite">

                        <div class="uk-form-row">
                            <label for="form-sitename" class="uk-form-label">{{ 'Name' | trans }}</label>
                            <div class="uk-form-controls">
                                <input id="form-sitename" class="uk-width-1-1" type="text" name="name" v-model="option.site.title" v-valid="required">
                                <p class="uk-form-help-block uk-text-danger" v-show="formSite.name.invalid">{{ 'Site name cannot be blank.' | trans }}</p>
                            </div>
                        </div>

                        <div class="uk-form-row">
                            <label for="form-sitedescription" class="uk-form-label">{{ 'Description' | trans }}</label>
                            <div class="uk-form-controls">
                                <textarea id="form-sitedescription" class="uk-width-1-1" v-model="option.site.description"></textarea>
                            </div>
                        </div>

                        <p class="uk-text-right">
                            <button class="uk-button uk-button-primary" type="submit">{{ 'Next' | trans }} <i class="uk-icon-arrow-right"></i></button>
                        </p>

                    </form>

                </div>
            </div>

            <div v-show="step == 'finish'" class="tm-slide uk-vertical-align uk-text-center">
                <div class="uk-panel uk-panel-box tm-panel uk-vertical-align-middle">

                    <div v-show="status == 'install'">
                        <h1>{{ 'Installing Pagekit...' | trans }}</h1>
                        <p>
                            <i class="uk-icon-spinner uk-icon-spin uk-icon-large"></i>
                        </p>
                    </div>

                    <div v-show="status == 'finished'">
                        <h1>{{ 'Finished!' | trans }}</h1>
                        <p>
                            <i class="uk-icon-check-circle uk-icon-large"></i>
                        </p>
                        <p>
                            <a class="uk-button uk-button-primary" href="<?= $redirect ?>">{{ 'Login now' | trans }}</a>
                        </p>
                    </div>

                    <div v-show="status == 'failed'">
                        <h1>{{ 'Installation failed!' | trans }}</h1>
                        <div class="uk-alert uk-alert-danger">{{ message }}</div>
                        <p>
                            <i class="uk-icon-times-circle uk-icon-large"></i>
                        </p>
                        <p>
                            <button type="button" class="uk-button uk-button-primary">{{ 'Retry' | trans }}</button>
                        </p>
                    </div>

                </div>
            </div>

        </div>

    </body>
</html>
