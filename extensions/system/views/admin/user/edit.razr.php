@script('requirejs')

<form class="uk-form uk-form-horizontal" action="@url('@system/user/save', ['id' => user.id ?: 0])" method="post">

    <div class="uk-grid" data-uk-grid-margin data-uk-grid-match>
        <div class="pk-sidebar uk-width-medium-1-4">
            <div class="pk-sidebar-panel uk-text-center">

                <p>@gravatar(user.email, ['size' => 300, 'attrs' => ['width' => '150', 'height' => '150', 'alt' => user.name, 'class' => 'uk-border-circle']])</p>
                @if (user.id)
                <ul class="uk-list">
                    <li><span class="uk-badge uk-badge-@( user.status ? 'success' : 'danger' )">@user.statusText</span></li>
                    <li>@user.name (@user.username)</li>
                    <li><a href="mailto:@user.email">@user.email</a></li>
                    <li>@trans('Registered since: %date%', ['%date%' => user.registered|date])</li>
                    <li>@trans('Last login: %date%', ['%date%' => user.login|date ?: trans('Never')])</li>
                </ul>
                @endif

            </div>
        </div>
        <div class="pk-content uk-width-medium-3-4">

            <div class="uk-form-row">
                <label for="form-username" class="uk-form-label">@trans('Username')</label>
                <div class="uk-form-controls">
                    <input id="form-username" class="uk-form-width-large" type="text" name="user[username]" value="@user.username" required>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-name" class="uk-form-label">@trans('Name')</label>
                <div class="uk-form-controls">
                    <input id="form-name" class="uk-form-width-large" type="text" name="user[name]" value="@user.name" required>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-email" class="uk-form-label">@trans('Email')</label>
                <div class="uk-form-controls">
                    <input id="form-email" class="uk-form-width-large" type="email" name="user[email]" value="@user.email" required>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-password" class="uk-form-label">@trans('Password')</label>

                @if (user.id)
                <div class="uk-form-controls uk-form-controls-text js-password">
                    <a href="#" data-uk-toggle="{ target: '.js-password' }">@trans('Change password')</a>
                </div>
                @endif

                <div class="uk-form-controls@( user.id ? ' js-password uk-hidden' )">
                    <div class="uk-form-password">
                        <input id="form-password" class="uk-form-width-large" type="password" name="password" value="">
                        <a href="" class="uk-form-password-toggle" data-uk-form-password>@trans('Show')</a>
                    </div>
                </div>

            </div>

            <div class="uk-form-row">
                <span class="uk-form-label">@trans('Status')</span>
                <div class="uk-form-controls uk-form-controls-text">
                    @foreach (user.statuses as status => name)
                    <p class="uk-form-controls-condensed">
                        <label><input type="radio" name="user[status]" value="@status"@(user.status == status ? ' checked') @(app.user.id == user.id ? 'disabled' : '')> @name</label>
                    </p>
                    @endforeach
                </div>
            </div>

            @if (roles)
            <div class="uk-form-row">
                <span class="uk-form-label">@trans('Roles')</span>
                <div class="uk-form-controls uk-form-controls-text">
                    @foreach (roles as role)
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" name="roles[]" value="@role.id" @(user.hasRole(role) ? 'checked')@(app.user.id == user.id && role.id == constant('Pagekit\\User\\Model\\RoleInterface::ROLE_ADMINISTRATOR') && user.hasRole(role) ? ' disabled' : '')> @role.name</label>
                    </p>
                    @endforeach
                </div>
            </div>
            @endif

            <p>
                <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
                <a class="uk-button" href="@url('@system/user/index')">@( user.id ? trans('Close') : trans('Cancel') )</a>
            </p>

        </div>
    </div>

    @token()

</form>

<script>

    require(['uikit', 'uikit!form-password'], function() {});

</script>