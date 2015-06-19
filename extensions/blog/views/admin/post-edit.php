<?php $view->style('codemirror'); $view->script('post-edit', 'blog:app/bundle/post-edit.js', ['vue', 'v-imagepicker', 'uikit-datepicker', 'uikit-timepicker', 'editor']) ?>

<form id="post" class="uk-form uk-form-stacked" name="form" v-on="valid: save" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove" v-if="post.id">{{ 'Edit Post' | trans }}</h2>
            <h2 class="uk-margin-remove" v-if="!post.id">{{ 'Add Post' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-margin-small-right" v-attr="href: $url('admin/blog/post')">{{ post.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="uk-flex-item-1">

            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="title" placeholder="{{ 'Enter Title' | trans }}" v-model="post.title" v-valid="required">
                <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <v-editor id="post-content" value="{{@ post.content }}" options="{{ {markdown : post.data.markdown} }}"></v-editor>
            </div>
            <div class="uk-form-row">
                <label class="uk-form-label">{{ 'Excerpt' | trans }}</label>
                <div class="uk-form-controls">
                    <textarea class="uk-width-1-1" type="text" placeholder="{{ 'Enter Excerpt' | trans }}" rows="5" v-model="post.excerpt"></textarea>
                </div>
            </div>

        </div>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <div class="uk-form-row">
                    <label for="form-image" class="uk-form-label">{{ 'Image' | trans }}</label>
                    <div class="uk-form-controls">
                        <v-imagepicker src="post.data.image"></v-imagepicker>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-status" class="uk-width-1-1" v-model="post.status" options="statuses"></select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-author" class="uk-form-label">{{ 'Author' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-author" class="uk-width-1-1" v-model="post.user_id" options="authors"></select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Publish on' | trans }}</span>
                    <div class="uk-form-controls">
                        <div class="uk-grid uk-grid-small" data-uk-grid-margin>
                            <div class="uk-width-large-1-2">
                                <div class="uk-form-icon uk-display-block">
                                    <i class="pk-icon-calendar pk-icon-muted"></i>
                                    <input class="uk-width-1-1" type="text" data-uk-datepicker="{ format: 'YYYY-MM-DD', pos: 'bottom' }" v-model="date" lazy>
                                </div>
                            </div>
                            <div class="uk-width-large-1-2">
                                <div class="uk-form-icon uk-display-block" data-uk-timepicker="{format:'12h'}">
                                    <i class="pk-icon-time pk-icon-muted"></i>
                                    <input class="uk-width-1-1" type="text" v-model="time" lazy>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-slug" class="uk-width-1-1" type="text" v-model="post.slug">
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Restrict Access' | trans }}</span>
                    <div v-repeat="role: data.roles" class="uk-form-controls">
                        <label><input type="checkbox" v-checkbox="post.roles" value="{{ role.id }}"> {{ role.name }}</label>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Options' | trans }}</span>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="post.data.title" value="1"> {{ 'Show Title' | trans }}</label>
                    </div>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="post.data.markdown" value="1"> {{ 'Enable Markdown' | trans }}</label>
                    </div>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="post.comment_status" value="1"> {{ 'Enable Comments' | trans }}</label>
                    </div>
                </div>

            </div>

        </div>
    </div>

</form>
