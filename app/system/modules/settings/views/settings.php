<?php $view->script('settings', 'app/system/modules/settings/app/bundle/settings.js', ['vue', 'uikit']) ?>

<form id="settings" class="uk-form uk-form-horizontal" v-on="submit: save" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side pk-nav-large" v-el="tab">
                    <li v-repeat="section: sections | orderBy 'priority'"><a><i class="uk-icon-justify uk-icon-small uk-margin-right {{ section.icon }}"></i> {{ section.label | trans }}</a></li>
                </ul>

            </div>

        </div>
        <div class="uk-flex-item-1">

            <ul class="uk-switcher uk-margin" v-el="content">
                <li v-repeat="section: sections | orderBy 'priority'">
                    <component is="{{ section.name }}" config="{{ config[section.name] }}" options="{{ options[section.name] }}"></component>
                </li>
            </ul>

        </div>
    </div>

</form>
