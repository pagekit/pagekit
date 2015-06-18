<?php $view->script('update', 'system/package:app/bundle/update.js', 'vue') ?>

<div id="update" v-cloak>

    <p class="uk-alert uk-alert-warning" v-repeat="errors">{{ $value }}</p>

    <div v-show="update && view == 'index'">

        <div v-show="hasUpdate">
            <h2>{{ 'There is an update available.' | trans }}</h2>
            <p>{{ 'Please update Pagekit to version %version%!' | trans update }}</p>
        </div>

        <div v-show="!hasUpdate">
            <h2>{{ 'You have the latest version of Pagekit.' | trans }}</h2>
            <p>{{ 'You have the latest version of Pagekit. You do not need to update. However, if you want to re-install version %version%, you can do so automatically or download the package and re-install manually.' | trans update }}</p>
        </div>

        <p>
            <a class="uk-button uk-button-primary" v-on="click: install()">
                <span v-show="hasUpdate">{{ 'Update' | trans }}</span>
                <span v-show="!hasUpdate">{{ 'Re-install' | trans }}</span>
            </a>
            <a class="uk-button uk-button-success" v-attr="href: update.url">{{ 'Download %version%' | trans update }}</a>
        </p>

    </div>

    <div v-show="view == 'installation'">

        <p>{{ message | trans }}</p>

        <div class="uk-progress uk-progress-striped" v-class="
            uk-progress-danger:  errors.length,
            uk-progress-success: progress == 100,
            uk-active:           progress != 100 && !errors.length
        ">
            <div class="uk-progress-bar" v-style="width: progress + '%'">{{ progress }}%</div>
        </div>

    </div>

</div>
