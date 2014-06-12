@script('blog.settings', 'system/js/settings/settings.js', 'requirejs')

@set(config = app.extensions.get('blog').config)

<form class="uk-form uk-form-horizontal uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match action="@url.route('@system/extensions/savesettings', ['name' => 'blog'])" method="post">

    <div class="uk-width-medium-1-4 pk-sidebar-left">

        <div class="uk-panel uk-panel-divider pk-panel-marginless">
            <ul class="uk-nav uk-nav-side" data-tabs="@tab">
                <li><a href="#">@trans('General')</a></li>
                <li><a href="#">@trans('Comments')</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-width-medium-3-4">

        <ul id="tab-content" class="uk-switcher uk-margin">
            <li>

                <h2 class="pk-form-heading">@trans('General')</h2>
                <div class="uk-form-row">
                    <span class="uk-form-label">@trans('Permalink Settings')</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label>
                                <input type="radio" name="config[permalink]" value=""@(!config['permalink'] ? ' checked')>
                                @trans('Numeric') <code>/blog/123</code>
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label>
                                <input type="radio" name="config[permalink]" value="blog/{slug}"@(config['permalink'] == 'blog/{slug}' ? ' checked')>
                                @trans('Name') <code>/blog/sample-post</code>
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label>
                                <input type="radio" name="config[permalink]" value="blog/{year}/{month}/{day}/{slug}"@(config['permalink'] == 'blog/{year}/{month}/{day}/{slug}' ? ' checked')>
                                @trans('Day and name') <code>/blog/2014/06/12/sample-post/</code>
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label>
                                <input type="radio" name="config[permalink]" value="blog/{year}/{month}/{slug}"@(config['permalink'] == 'blog/{year}/{month}/{slug}' ? ' checked')>
                                @trans('Month and name') <code>/blog/2014/06/sample-post/</code>
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label>
                                <input type="radio" name="config[permalink]" value="custom"@(config['permalink'] == 'custom' ? ' checked')>
                                @trans('Custom')
                            </label>
                            <input type="text" name="config[permalink.custom]" value="@config['permalink.custom']">
                        </p>
                    </div>
                </div>
            </li>
            <li>

                <h2 class="pk-form-heading">@trans('Comments')</h2>
                <div class="uk-form-row">
                    <span class="uk-form-label">@trans('Comments')</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <input type="hidden" name="config[comments.enabled]" value="0">
                            <label><input type="checkbox" name="config[comments.enabled]" value="1" @(config['comments.enabled'] ? 'checked')> @trans('Enable comments.')</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <input type="hidden" name="config[comments.require_name_and_email]" value="0">
                            <label><input type="checkbox" name="config[comments.require_name_and_email]" value="1" @(config['comments.require_name_and_email'] ? 'checked')> @trans('Require name and e-mail.')</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <input type="hidden" name="config[comments.autoclose]" value="0">
                            <input type="checkbox" name="config[comments.autoclose]" value="1" @(config['comments.autoclose'] ? 'checked')> @trans('Close comments on articles older than')
                            <input type="number" name="config[comments.autoclose.days]" value="@config['comments.autoclose.days']" min="1"> @trans('days.')
                        </p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">@trans('Appearance')</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <input type="hidden" name="config[comments.gravatar]" value="0">
                            <label><input type="checkbox" name="config[comments.gravatar]" value="1" @(config['comments.gravatar'] ? 'checked')> @trans('Show Avatars from Gravatar.')</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label>@trans('Order comments by')
                                <select name="config[comments.order]">
                                    <option value="ASC"@(config['comments.order'] != 'DESC' ? ' selected')>@trans('latest last.')</option>
                                    <option value="DESC"@(config['comments.order'] == 'DESC' ? ' selected')>@trans('latest first.')</option>
                                </select>
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <input type="hidden" name="config[comments.nested]" value="0">
                            <input type="checkbox" name="config[comments.nested]" value="1"@(config['comments.nested'] ? ' checked')> @trans('Enable nested comments of')
                            <input type="hidden" name="config[comments.max_depth]" value="0">
                            <input type="number" name="config[comments.max_depth]" value="@config['comments.max_depth']" min="2" max="10"> @trans('levels deep.')
                        </p>
                    </div>
                </div>

            </li>
        </ul>

        <p>
            <input type="hidden" name="tab" value="0">
            <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
            <a class="uk-button" href="@url.route('@system/system/index')">@trans('Close')</a>
        </p>

    </div>

    @token()

</form>