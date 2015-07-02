<?php $view->style('codemirror'); $view->script('widget-edit', 'widget:app/bundle/edit.js', ['widgets', 'editor']) ?>

<form id="widget-edit" name="widgetForm" class="uk-form uk-form-stacked" v-on="submit: save" v-cloak>

    <div class="uk-clearfix uk-margin" data-uk-margin>

        <div class="uk-float-left">

            <h2 class="uk-h2" v-if="widget.id">{{ widget.title }}</h2>
            <h2 class="uk-h2" v-if="!widget.id">{{ 'Add %type%' | trans {type:widget.type} }}</h2>

        </div>

        <div class="uk-float-right">

            <a class="uk-button" href="<?= $view->url('@widget') ?>">{{ 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>

    </div>

    <div class="uk-grid" data-uk-grid-margin>

        <div class="uk-width-medium-3-4 uk-form-horizontal">

            <ul class="uk-tab" v-el="tab">
                <li v-repeat="section: sections | active | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
            </ul>

            <div class="uk-switcher uk-margin" v-el="content">
                <div v-repeat="section: sections | active | orderBy 'priority'">
                    <component is="{{ section.name }}" widget="{{ widget }}" type="{{ type }}" config="{{ config }}" form="{{ widgetForm }}"></component>
                </div>
            </div>

        </div>

        <div class="uk-width-medium-1-4">

            <div class="uk-panel">

                <div class="uk-form-row">
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-status" class="uk-width-1-1" v-model="widget.status">
                            <option value="1">{{ 'Enabled' | trans }}</option>
                            <option value="0">{{ 'Disabled' | trans }}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label for="form-position" class="uk-form-label">{{ 'Position' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-position" name="position" class="uk-width-1-1" v-model="widget.position" options="positionOptions"></select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Restrict Access' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p v-repeat="role: config.roles" class="uk-form-controls-condensed">
                            <label><input type="checkbox" value="{{ role.id }}" v-checkbox="widget.roles"> {{ role.name }}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Options' | trans }}</span>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="widget.data.show_title"> {{ 'Show Title' | trans }}</label>
                    </div>
                </div>

            </div>

        </div>

    </div>

</form>
