<?php $view->script('update', 'installer:app/bundle/update.js', 'vue') ?>

<div id="update" v-cloak>

    <p class="uk-alert uk-alert-warning" v-for="error in errors">{{ error }}</p>

    <div v-show="update && view == 'index'">

        <div v-show="hasUpdate">
            <h2>{{ 'There is an update available.' | trans }}</h2>
            <p>{{ 'Please update Pagekit to version %version%!' | trans update }}</p>
        </div>

        <div v-show="!hasUpdate">
            <h2>{{ 'You have the latest version of Pagekit.' | trans }}</h2>
            <p>{{ 'You have the latest version of Pagekit. You do not need to update. However, if you want to re-install version %version%, you can download the package and re-install manually.' | trans update }}</p>
        </div>

        <p>
            <a class="uk-button uk-button-primary" @click.prevent="install" v-show="hasUpdate">
                <span>{{ 'Update' | trans }}</span>
            </a>
            <a class="uk-button uk-button-success" :href="update.url">{{ 'Download %version%' | trans update }}</a>
        </p>

    </div>

    <div v-show="view == 'installation'">

        <p>{{ message | trans }}</p>

        <div class="uk-progress uk-progress-striped" :class="{
            'uk-progress-danger':  errors.length,
            'uk-progress-success': progress == 100,
            'uk-active':           progress != 100 && !errors.length
        }">
            <div class="uk-progress-bar" :style="{width: progress + '%'}">{{ progress }}%</div>
        </div>

        <pre v-html="output" v-show="output"></pre>

        <a class="uk-button uk-button-{{ status }}" :href="$url.route('admin')" v-show="finished">{{ 'Ok' | trans }}</a>

    </div>

</div>
