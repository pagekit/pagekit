<?php $view->script('widget-edit', 'system/widget:app/bundle/edit.js', ['widgets', 'editor']) ?>

<form id="widget-edit" class="uk-form" name="form" v-on="submit: save | valid" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove" v-if="widget.id">{{ 'Edit Widget' | trans }}</h2>
            <h2 class="uk-margin-remove" v-if="!widget.id">{{ 'Add Widget' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-margin-small-right" href="<?= $view->url('@site/widget') ?>">{{ widget.title ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <ul class="uk-tab" v-el="tab">
        <li v-repeat="section: sections | active | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div class="uk-switcher uk-margin-large-top" v-el="content">
        <div v-repeat="section: sections | active | orderBy 'priority'">
            <component is="{{ section.name }}" widget="{{@ widget }}" config="{{ config }}" form="{{@ form}}"></component>
        </div>
    </div>

</form>
