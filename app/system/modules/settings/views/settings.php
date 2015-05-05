<?php $view->script('settings', 'app/system/modules/settings/app/bundle/settings.js', ['system', 'uikit']) ?>

<form id="settings" class="uk-form uk-form-horizontal" v-cloak v-on="submit: save">

    <div class="uk-grid" data-uk-grid-margin>
        <div class="uk-width-medium-1-4">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side" v-el="tab">
                    <li v-repeat="section: $options.sections | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
                </ul>

            </div>

        </div>

        <div class="uk-width-medium-3-4">

            <ul class="uk-switcher uk-margin" v-el="content">
                <li v-repeat="section: $options.sections | orderBy 'priority'">
                    <div v-component="{{ section.name }}" v-with="options: options[section.name], config: config[section.name]"></div>
                </li>
            </ul>

        </div>
    </div>

</form>
