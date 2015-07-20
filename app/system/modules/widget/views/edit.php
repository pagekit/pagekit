<?php $view->style('codemirror'); $view->script('widget-edit', 'widget:app/bundle/edit.js', ['widgets', 'editor', 'input-link', 'input-image']) ?>

<form id="widget-edit" class="uk-form" name="widgetForm" v-on="submit: save" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove" v-if="widget.title">{{ 'Edit Widget' | trans }}: {{ widget.title }}</h2>
            <h2 class="uk-margin-remove" v-if="!widget.title">{{ 'Add Widget' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-margin-small-right" href="<?= $view->url('@widget') ?>">{{ widget.title ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <ul class="uk-tab" v-el="tab">
        <li v-repeat="section: sections | active | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div class="uk-switcher uk-margin-large-top" v-el="content">
        <div v-repeat="section: sections | active | orderBy 'priority'">
            <component is="{{ section.name }}" widget="{{@ widget }}" config="{{ config }}"></component>
        </div>
    </div>

</form>
