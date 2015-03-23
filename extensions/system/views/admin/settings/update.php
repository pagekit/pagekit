<?php $view->script('update', 'extensions/system/app/update.js', 'vue-system') ?>

<div id="js-update" v-cloak>

    <p v-repeat="errors" class="uk-alert uk-alert-warning">{{ $value }}</p>

    <div v-show="version && view == 'index'">

        <div v-show="hasUpdate">
            <h2>{{ 'There is an update available.' | trans }}</h2>
            <p>{{ 'Please update Pagekit to version %version%!' | trans version }}</p>
        </div>

        <div v-show="!hasUpdate">
            <h2>{{ 'You have the latest version of Pagekit.' | trans }}</h2>
            <p>{{ 'You have the latest version of Pagekit. You do not need to update. However, if you want to re-install version %version%, you can do so automatically or download the package and re-install manually.' | trans version }}</p>
        </div>

        <p>
            <a class="uk-button uk-button-primary" v-on="click: install()">
                <span v-show="hasUpdate">{{ 'Update' | trans }}</span>
                <span v-show="!hasUpdate">{{ 'Re-install' | trans }}</span>
            </a>
            <a class="uk-button uk-button-success" v-attr="href: version.url">{{ 'Download %version%' | trans version }}</a>
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
