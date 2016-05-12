<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link href="app/system/modules/theme/favicon.ico" rel="shortcut icon" type="image/x-icon">
        <link href="app/system/modules/theme/apple_touch_icon.png" rel="apple-touch-icon-precomposed">
        <?php $view->style('installer', 'app/installer/assets/css/installer.css') ?>
        <?php $view->script('installer', 'app/installer/app/views/installer.js', ['vue', 'uikit-form-password']) ?>
        <?= $view->render('head') ?>
    </head>
    <body>

        <div id="installer" class="tm-background uk-height-viewport uk-flex uk-flex-center uk-flex-middle" >
            <div class="tm-container">

                <div class="uk-text-center" v-el:start v-show="step == 'start'">

                    <a class="uk-panel" @click="gotoStep('language')">
                        <img src="app/system/assets/images/pagekit-logo-large.svg" alt="Pagekit">
                        <p>
                            <svg class="tm-arrow" width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                                <line fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" x1="2" y1="18" x2="36" y2="18"/>
                                <polyline fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="26.071,6.5 37.601,18.03 26,29.631 "/>
                            </svg>
                        </p>
                    </a>

                </div>

                <div class="uk-panel uk-panel-box" v-el:language v-show="step == 'language'" >
                    <div v-pre>

                        <h1 class="uk-margin-small-bottom uk-text-center">{{ 'Choose language' | trans }}</h1>
                        <div class="uk-margin-large-bottom uk-text-muted uk-text-center">{{ "Select your site language." | trans }}</div>

                        <form class="uk-form" @submit.prevent="stepLanguage">

                            <select class="uk-width-1-1" size="10" v-model="locale">
                                <option v-for="lang in locales" :value="$key">{{ lang }}</option>
                            </select>

                            <p class="uk-text-right">
                                <button class="uk-button uk-button-primary" type="submit">
                                    <span class="uk-flex-inline uk-flex-middle">{{ 'Next' | trans }}
                                        <svg class="uk-margin-small-left" width="18" height="11" viewBox="0 0 18 11" xmlns="http://www.w3.org/2000/svg">
                                            <line fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-miterlimit="10" x1="3" y1="5.5" x2="15" y2="5.5"/>
                                            <path fill="#FFFFFF" d="M10.5,10.9c-0.128,0-0.256-0.049-0.354-0.146c-0.195-0.195-0.195-0.512,0-0.707l4.597-4.597l-4.597-4.597
                                            c-0.195-0.195-0.195-0.512,0-0.707s0.512-0.195,0.707,0l4.95,4.95c0.195,0.195,0.195,0.512,0,0.707l-4.95,4.95
                                            C10.756,10.852,10.628,10.9,10.5,10.9z"/>
                                        </svg>
                                    </span>
                                </button>
                            </p>
                        </form>

                    </div>

                </div>

                <div class="uk-panel uk-panel-box" v-el:database v-show="step == 'database'">
                    <div v-pre>

                        <h1 class="uk-margin-small-bottom uk-text-center">{{ 'Connect database' | trans }}</h1>
                        <div class="uk-margin-large-bottom uk-text-muted uk-text-center">{{ 'Enter your database connection details.' | trans }}</div>

                        <div class="uk-alert uk-alert-danger uk-margin uk-text-center" v-show="message"><p>{{ message }}</p></div>

                        <form class="uk-form uk-form-horizontal tm-form-horizontal" v-validator="formDatabase" @submit.prevent="stepDatabase | valid">
                            <div class="uk-form-row">
                                <label for="form-dbdriver" class="uk-form-label">{{ 'Driver' | trans }}</label>
                                <div class="uk-form-controls">
                                    <select id="form-dbdriver" class="uk-width-1-1" name="dbdriver" v-model="config.database.default">
                                        <option value="sqlite" v-if="sqlite">SQLite</option>
                                        <option value="mysql">MySQL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-form-row" v-if="config.database.default == 'mysql'">
                                <div class="uk-form-row">
                                    <label for="form-mysql-dbhost" class="uk-form-label">{{ 'Hostname' | trans }}</label>
                                    <div class="uk-form-controls">
                                        <input id="form-mysql-dbhost" class="uk-width-1-1" type="text" name="host" value="localhost" v-model="config.database.connections.mysql.host" v-validate:required>
                                        <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.host.invalid">{{ 'Host cannot be blank.' | trans }}</p>
                                    </div>
                                </div>
                                <div class="uk-form-row">
                                    <label for="form-mysql-dbuser" class="uk-form-label">{{ 'User' | trans }}</label>
                                    <div class="uk-form-controls">
                                        <input id="form-mysql-dbuser" class="uk-width-1-1" type="text" name="user" value="" v-model="config.database.connections.mysql.user" v-validate:required>
                                        <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.user.invalid">{{ 'User cannot be blank.' | trans }}</p>
                                    </div>
                                </div>
                                <div class="uk-form-row">
                                    <label for="form-mysql-dbpassword" class="uk-form-label">{{ 'Password' | trans }}</label>
                                    <div class="uk-form-controls">
                                        <div class="uk-form-password uk-width-1-1">
                                            <input id="form-mysql-dbpassword" class="uk-width-1-1" type="password" name="password" value="" autocomplete="off" v-model="config.database.connections.mysql.password">
                                            <a class="uk-form-password-toggle" href="" tabindex="-1" data-uk-form-password="{ lblShow: 'Show', lblHide: 'Hide' }">{{ 'Show' | trans }}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-form-row">
                                    <label for="form-mysql-dbname" class="uk-form-label">{{ 'Database Name' | trans }}</label>
                                    <div class="uk-form-controls">
                                        <input id="form-mysql-dbname" class="uk-width-1-1" type="text" name="dbname" value="pagekit" v-model="config.database.connections.mysql.dbname" v-validate:required>
                                        <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.dbname.invalid">{{ 'Database name cannot be blank.' | trans }}</p>
                                    </div>
                                </div>
                                <div class="uk-form-row">
                                    <label for="form-mysql-dbprefix" class="uk-form-label">{{ 'Table Prefix' | trans }}</label>
                                    <div class="uk-form-controls">
                                        <input id="form-mysql-dbprefix" class="uk-width-1-1" type="text" name="mysqlprefix" value="pk_" v-model="config.database.connections.mysql.prefix" v-validate:pattern.literal="/^[a-zA-Z][a-zA-Z0-9._\-]*$/">
                                        <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.mysqlprefix.invalid">{{ 'Prefix must start with a letter and can only contain alphanumeric characters (A-Z, 0-9) and underscore (_)' | trans }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-form-row" v-show="config.database.default == 'sqlite'">
                                <div class="uk-form-row">
                                    <label for="form-sqlite-dbprefix" class="uk-form-label">{{ 'Table Prefix' | trans }}</label>
                                    <div class="uk-form-controls">
                                        <input id="form-sqlite-dbprefix" class="uk-width-1-1" type="text" name="sqliteprefix" value="pk_" v-model="config.database.connections.sqlite.prefix" v-validate:pattern.literal="/^[a-zA-Z][a-zA-Z0-9._\-]*$/">
                                        <p class="uk-form-help-block uk-text-danger" v-show="formDatabase.sqliteprefix.invalid">{{ 'Prefix must start with a letter and can only contain alphanumeric characters (A-Z, 0-9) and underscore (_)' | trans }}</p>
                                    </div>
                                </div>
                            </div>
                            <p class="uk-text-right">
                                <button class="uk-button uk-button-primary" type="submit">
                                    <span class="uk-flex-inline uk-flex-middle">{{ 'Next' | trans }}
                                        <svg class="uk-margin-small-left" width="18" height="11" viewBox="0 0 18 11" xmlns="http://www.w3.org/2000/svg">
                                            <line fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-miterlimit="10" x1="3" y1="5.5" x2="15" y2="5.5"/>
                                            <path fill="#FFFFFF" d="M10.5,10.9c-0.128,0-0.256-0.049-0.354-0.146c-0.195-0.195-0.195-0.512,0-0.707l4.597-4.597l-4.597-4.597
                                            c-0.195-0.195-0.195-0.512,0-0.707s0.512-0.195,0.707,0l4.95,4.95c0.195,0.195,0.195,0.512,0,0.707l-4.95,4.95
                                            C10.756,10.852,10.628,10.9,10.5,10.9z"/>
                                        </svg>
                                    </span>
                                </button>
                            </p>
                        </form>

                    </div>
                </div>

                <div class="uk-panel uk-panel-box" v-el:site v-show="step == 'site'">
                    <div v-pre>

                        <h1 class="uk-margin-small-bottom uk-text-center">{{ 'Setup your site' | trans }}</h1>
                        <div class="uk-margin-large-bottom uk-text-muted uk-text-center">{{ 'Choose a title and create the administrator account.' | trans }}</div>

                        <form class="uk-form uk-form-horizontal tm-form-horizontal" v-validator="formSite" @submit.prevent="stepSite | valid">
                            <div class="uk-form-row">
                                <label for="form-sitename" class="uk-form-label">{{ 'Site Title' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-sitename" class="uk-width-1-1" type="text" name="name" v-model="option['system/site'].title" v-validate:required>
                                    <p class="uk-form-help-block uk-text-danger" v-show="formSite.name.invalid">{{ 'Site title cannot be blank.' | trans }}</p>
                                </div>
                            </div>

                            <div class="uk-form-row">
                                <label for="form-username" class="uk-form-label">{{ 'Username' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-username" class="uk-width-1-1" type="text" name="user" value="admin" v-model="user.username" v-validate:pattern.literal="/^[a-zA-Z0-9._\-]{3,}$/">
                                    <p class="uk-form-help-block uk-text-danger" v-show="formSite.user.invalid">{{ 'Username cannot be blank and may only contain alphanumeric characters (A-Z, 0-9) and some special characters ("._-")' | trans }}</p>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label for="form-password" class="uk-form-label">{{ 'Password' | trans }}</label>
                                <div class="uk-form-controls">
                                    <div class="uk-form-password uk-width-1-1">
                                        <input id="form-password" class="uk-width-1-1" type="password" name="password" v-model="user.password" v-validate:required>
                                        <a class="uk-form-password-toggle" href="" tabindex="-1" data-uk-form-password="{ lblShow: 'Show', lblHide: 'Hide' }">{{ 'Show' | trans }}</a>
                                    </div>
                                    <p class="uk-form-help-block uk-text-danger" v-show="formSite.password.invalid">{{ 'Password cannot be blank.' | trans }}</p>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label for="form-email" class="uk-form-label">{{ 'Email' | trans }}</label>
                                <div class="uk-form-controls">
                                    <input id="form-email" class="uk-width-1-1" type="email" name="email" v-model="user.email" v-validate:email v-validate:required>
                                    <p class="uk-form-help-block uk-text-danger" v-show="formSite.email.invalid">{{ 'Field must be a valid email address.' | trans }}</p>
                                </div>
                            </div>
                            <p class="uk-text-right">
                                <button class="uk-button uk-button-primary" type="submit">
                                    <span class="uk-flex-inline uk-flex-middle">{{ 'Install' | trans }}
                                        <svg class="uk-margin-small-left" width="18" height="11" viewBox="0 0 18 11" xmlns="http://www.w3.org/2000/svg">
                                            <line fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-miterlimit="10" x1="3" y1="5.5" x2="15" y2="5.5"/>
                                            <path fill="#FFFFFF" d="M10.5,10.9c-0.128,0-0.256-0.049-0.354-0.146c-0.195-0.195-0.195-0.512,0-0.707l4.597-4.597l-4.597-4.597
                                            c-0.195-0.195-0.195-0.512,0-0.707s0.512-0.195,0.707,0l4.95,4.95c0.195,0.195,0.195,0.512,0,0.707l-4.95,4.95
                                            C10.756,10.852,10.628,10.9,10.5,10.9z"/>
                                        </svg>
                                    </span>
                                </button>
                            </p>
                        </form>

                    </div>
                </div>

                <div v-el:finish v-show="step == 'finish'">
                    <div v-pre>
                        <div class="uk-text-center" v-show="status == 'install'">
                            <svg class="tm-loader" width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
                                <g><circle cx="0" cy="0" r="70" fill="none" stroke-width="2"/></g>
                            </svg>
                        </div>

                        <div class="uk-text-center" v-show="status == 'finished'">
                            <a class="uk-panel" :href="$url.route('admin')">
                                <svg class="tm-checkmark" width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <polyline fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" points="5.125,63.25 27.375,89.375 95.25,18.875"/>
                                </svg>
                            </a>
                        </div>

                        <div class="uk-panel uk-panel-box" v-show="status == 'failed'">
                            <h1>{{ 'Installation failed!' | trans }}</h1>
                            <div class="uk-text-break">{{ message }}</div>
                            <p class="uk-text-right">
                                <button type="button" class="uk-button uk-button-primary" @click="stepInstall">{{ 'Retry' | trans }}</button>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </body>
</html>
