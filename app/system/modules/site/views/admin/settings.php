<?php $view->style('codemirror') ?>
<?php $view->script('site-settings', 'system/site:app/bundle/settings.js', ['vue', 'editor', 'input-image']) ?>

<div id="settings" class="uk-grid pk-grid-large" data-uk-grid-margin>
    <div class="pk-width-sidebar">

        <div class="uk-panel">
            <ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
                <li class="uk-active"><a> <i class="pk-icon-large-settings uk-margin-right"></i>{{ 'General' | trans }}</a></li>
                <li><a><i class="pk-icon-large-code uk-margin-right"></i> {{ 'Code' | trans }}</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-flex-item-1">

        <ul id="tab-content" class="uk-switcher uk-margin">
            <li>

                <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                    <div data-uk-margin>
                        <h2 class="uk-margin-remove">{{ 'General' | trans }}</h2>
                    </div>
                    <div data-uk-margin>
                        <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
                    </div>
                </div>

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
                        <label for="form-favicon" class="uk-form-label">{{ 'Favicon' | trans }}</label>
                        <div class="uk-form-controls uk-form-width-large">
                            <input-image src="{{@ config.icons.favicon }}"></input-image>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <label for="form-appicon" class="uk-form-label">{{ 'Appicon' | trans }}</label>
                        <div class="uk-form-controls uk-form-width-large">
                            <input-image src="{{@ config.icons.appicon }}"></input-image>
                        </div>
                    </div>
                </div>

            </li>
            <li>

                <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                    <div data-uk-margin>
                        <h2 class="uk-margin-remove">{{ 'Code' | trans }}</h2>
                    </div>
                    <div data-uk-margin>
                        <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
                    </div>
                </div>

                <p>{{ 'Insert code in the header and footer of your theme. This is useful for tracking codes and meta tags.' | trans }}</p>

                <div class="uk-form uk-form-stacked">
                    <div class="uk-form-row">
                        <label for="form-codeheader" class="uk-form-label">{{ 'Header' | trans }}</label>
                        <div class="uk-form-controls">
                            <v-editor type="code" value="{{@ config.code.header }}"></v-editor>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <label for="form-codeheader" class="uk-form-label">{{ 'Footer' | trans }}</label>
                        <div class="uk-form-controls">
                            <v-editor type="code" value="{{@ config.code.footer }}"></v-editor>
                        </div>
                    </div>
                </div>

            </li>
        </ul>

    </div>
</div>
