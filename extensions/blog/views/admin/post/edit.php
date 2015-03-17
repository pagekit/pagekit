<form id="js-post" name="form" class="uk-form uk-form-stacked" v-on="valid: save" v-cloak>

    <?php $view->section()->start('toolbar', 'show') ?>
        <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        <a class="uk-button" v-attr="href: $url('admin/blog/post')">{{ post.id ? 'Close' : 'Cancel' | trans }}</a>
    <?php $view->section()->end() ?>

    <div class="uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match>
        <div class="uk-width-medium-3-4">

            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="title" v-model="post.title" placeholder="{{ 'Enter Title' | trans }}" v-valid="required">
                <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <?= $view->editor('', $post->getContent(), ['id' => 'post-content', 'v-model' => 'post.content', 'data-markdown' => $post->get('markdown', '0') ]) ?>
            </div>

            <div class="uk-form-row">
                <label class="uk-form-label">{{ 'Excerpt' | trans }}</label>
                <div class="uk-form-controls">
                    <textarea class="uk-width-1-1" type="text" v-model="post.excerpt" placeholder="{{ 'Enter Excerpt' | trans }}" rows="5"></textarea>
                </div>
            </div>

        </div>
        <div class="uk-width-medium-1-4 pk-sidebar-right">

            <div class="uk-panel uk-panel-divider">
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
                                <div class="uk-form-icon">
                                    <i class="uk-icon-calendar"></i>
                                    <input class="uk-form-width-1-1 uk-form-small" type="text" data-uk-datepicker="{ format: 'YYYY-MM-DD' }" v-model="date" lazy>
                                </div>
                            </div>
                            <div class="uk-width-large-1-2">
                                <div class="uk-form-icon" data-uk-timepicker>
                                    <i class="uk-icon-clock-o"></i>
                                    <input class="uk-form-width-1-1 uk-form-small" type="text" v-model="time" lazy>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-slug" class="uk-width-1-1" type="text" v-model="post.slug"">
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
