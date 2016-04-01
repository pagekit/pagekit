<?php $view->script('site-settings', 'system/site:app/bundle/settings.js', ['vue', 'editor']) ?>

<form id="settings" class="uk-form uk-form-horizontal" v-validator="form" @submit.prevent="save | valid" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side pk-nav-large" v-el:tab>
                    <li :class="{'uk-active': section.active}" v-for="section in sections | orderBy 'priority'"><a><i class="uk-icon-justify uk-icon-small uk-margin-right {{ section.icon }}"></i> {{ section.label | trans }}</a></li>
                </ul>

            </div>

        </div>
        <div class="pk-width-content">

            <ul class="uk-switcher uk-margin" v-el:content>
                <li v-for="section in sections | orderBy 'priority'">
                    <component :is="section.name" :config.sync="config"></component>
                </li>
            </ul>

        </div>
    </div>

</form>
