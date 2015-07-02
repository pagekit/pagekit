<?php $view->style('codemirror') ?>
<?php $view->script('site-settings', 'system/site:app/bundle/settings.js', ['vue', 'v-imagepicker', 'editor']) ?>

<div id="settings">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Settings' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

        </div>
    </div>


    <ul class="uk-tab" v-el="tab">
        <li><a>{{ 'General' | trans }}</a></li>
        <li><a>{{ 'Code' | trans }}</a></li>
    </ul>


    <div id="settings-panels" class="uk-switcher uk-margin-large-top">

        <div class="uk-form uk-form-horizontal">

            <div class="uk-form-row">
                <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-title" class="uk-form-width-large" type="text" v-model="config.title">
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-description" class="uk-form-label">{{ 'Description' | trans }}</label>
                <div class="uk-form-controls">
                    <textarea id="form-description" class="uk-form-width-large" rows="5" v-model="config.description"></textarea>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-offlinemessage" class="uk-form-label">{{ 'Offline Message' | trans }}</label>
                <div class="uk-form-controls">
                    <textarea id="form-offlinemessage" class="uk-form-width-large" placeholder="{{ &quot;We'll be back soon.&quot; | trans }}" rows="5" v-model="config.maintenance.msg"></textarea>
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" value="1" v-model="config.maintenance.enabled"> {{ 'Put the site offline and show the offline message.' | trans }}</label>
                    </p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-favicon" class="uk-form-label">{{ 'Site favicon' | trans }}</label>
                <div class="uk-form-controls uk-form-width-large">
                    <v-imagepicker src="config.icons.favicon"></v-imagepicker>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-appicon" class="uk-form-label">{{ 'Site appicon' | trans }}</label>
                <div class="uk-form-controls uk-form-width-large">
                    <v-imagepicker src="config.icons.appicon"></v-imagepicker>
                </div>
            </div>
        </div>

        <div class="uk-form uk-form-horizontal">
            <div class="uk-form-row">
                <label for="form-codeheader" class="uk-form-label">{{ 'Header' | trans }}</label>
                <div class="uk-form-controls">
                    <v-editor value="{{@ config.code.header }}"></v-editor>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-codeheader" class="uk-form-label">{{ 'Footer' | trans }}</label>
                <div class="uk-form-controls">
                    <v-editor value="{{@ config.code.footer }}"></v-editor>
                </div>
            </div>
        </div>

    </div>
</div>
