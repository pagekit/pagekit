<?php $view->script('site-edit', 'site:app/bundle/edit.js', ['vue', 'uikit']) ?>

<div id="site-edit" v-cloak>

    <form class="uk-form uk-form-horizontal" name="form" v-on="valid: save">

        <div class="uk-clearfix uk-margin">

            <div class="uk-float-left">

                <h2 class="uk-h2" v-if="node.id">{{ node.title }} ({{ type.label }})</h2>
                <h2 class="uk-h2" v-if="!node.id">{{ 'Add %type%' | trans {type:type.label} }}</h2>

            </div>

            <div class="uk-float-right">

                <a class="uk-button uk-margin-small-right" v-attr="href: $url('admin/site')">{{ node.id ? 'Close' : 'Cancel' | trans }}</a>
                <button class="uk-button uk-button-primary" type="submit" v-attr="disabled: form.invalid">{{ 'Save' | trans }}</button>

            </div>

        </div>

        <ul class="uk-tab" v-el="tab">
            <li v-repeat="section: sections | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
        </ul>

        <div class="uk-switcher uk-margin" v-el="content">
            <div v-repeat="section: sections | orderBy 'priority'">
                <div v-component="{{ section.name }}" node="{{ node }}" form="{{ form }}" type="{{ type }}"></div>
            </div>
        </div>

    </form>

</div>
