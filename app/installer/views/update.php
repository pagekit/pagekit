<?php $view->script('update', 'installer:app/bundle/update.js', 'vue') ?>

<div id="update" v-cloak>

    <p class="uk-alert uk-alert-warning" v-repeat="errors">{{ $value }}</p>

    <div v-show="update">

        <div v-show="hasUpdate">
            <h2>{{ 'There is an update available.' | trans }}</h2>
            <p>{{ 'Please update Pagekit to version %version%!' | trans update }}</p>
        </div>

        <div v-show="!hasUpdate">
            <h2>{{ 'You have the latest version of Pagekit.' | trans }}</h2>
            <p>{{ 'You have the latest version of Pagekit. You do not need to update. However, if you want to re-install version %version%, you can download the package and re-install manually.' | trans update }}</p>
        </div>

        <p>
            <a class="uk-button uk-button-primary" v-on="click: install()" v-show="hasUpdate">
                <span>{{ 'Update' | trans }}</span>
            </a>
            <a class="uk-button uk-button-success" v-attr="href: update.url">{{ 'Download %version%' | trans update }}</a>
        </p>

    </div>

</div>