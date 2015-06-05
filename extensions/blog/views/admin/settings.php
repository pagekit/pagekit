<?php $view->script('blog.settings', 'blog:app/admin/settings.js', 'vue') ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
                    <li><a><i class="uk-icon-paragraph uk-icon-small uk-margin-right"></i> {{ 'General' | trans }}</a></li>
                    <li><a><i class="uk-icon-comment-o uk-icon-small uk-margin-right"></i> {{ 'Comments' | trans }}</a></li>
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

                    <div class="uk-form-row">
                        <span class="uk-form-label">{{ 'Permalink' | trans }}</span>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="">
                                    {{ 'Numeric' | trans }} <code>{{ '/blog/123' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="{slug}">
                                    {{ 'Name' | trans }} <code>{{ '/blog/sample-post' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="{year}/{month}/{day}/{slug}">
                                    {{ 'Day and name' | trans }} <code>{{ '/blog/2014/06/12/sample-post' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="{year}/{month}/{slug}">
                                    {{ 'Month and name' | trans }} <code>{{ '/blog/2014/06/sample-post' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="custom">
                                    {{ 'Custom' | trans }}
                                </label>
                                <input class="uk-form-small" type="text" v-model="config.permalink.custom">
                            </p>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-form-label">{{ 'Posts per page' | trans }}</label>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <input type="number" v-model="config.posts.posts_per_page" class="uk-form-width-small" required>
                            </p>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-form-label">{{ 'Default post settings' | trans }}</span>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <label><input type="checkbox" v-model="config.posts.show_title"> {{ 'Show Title' | trans }}</label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label><input type="checkbox" v-model="config.posts.markdown_enabled"> {{ 'Enable Markdown' | trans }}</label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label><input type="checkbox" v-model="config.posts.comments_enabled"> {{ 'Enable Comments' | trans }}</label>
                            </p>
                        </div>
                    </div>

                </li>
                <li>

                    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                        <div data-uk-margin>

                            <h2 class="uk-margin-remove">{{ 'Comments' | trans }}</h2>

                        </div>
                        <div data-uk-margin>

                            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-form-label">{{ 'Comments' | trans }}</span>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <label><input type="checkbox" v-model="config.comments.require_name_and_email"> {{ 'Require name and e-mail.' | trans }}</label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <input type="checkbox" v-model="config.comments.autoclose"> {{ 'Close comments on articles older than' | trans }}
                                <input class="uk-form-small uk-form-width-mini" type="number" v-model="config.comments.autoclose_days" min="1"> {{ 'days.' | trans }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <span class="uk-form-label">{{ 'Appearance' | trans }}</span>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <label><input type="checkbox" v-model="config.comments.gravatar"> {{ 'Show Avatars from Gravatar.' | trans }}</label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>{{ 'Order comments by' | trans }}
                                    <select class="uk-form-small" v-model="config.comments.order">
                                        <option value="ASC">{{ 'latest last.' | trans }}</option>
                                        <option value="DESC">{{ 'latest first.' | trans }}</option>
                                    </select>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <input type="checkbox" v-model="config.comments.nested"> {{ 'Enable nested comments of' | trans }}
                                <input class="uk-form-small uk-form-width-mini" type="number" v-model="config.comments.max_depth" min="2" max="10"> {{ 'levels deep.' | trans }}
                            </p>
                        </div>
                    </div>

                </li>
            </ul>

        </div>
    </div>

</div>
