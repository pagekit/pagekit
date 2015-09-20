<?php $view->script('user-edit', 'system/user:app/bundle/user-edit.js', ['vue', 'uikit-form-password']) ?>

<form id="user-edit" class="uk-form uk-form-horizontal" name="form" v-on="submit: save | valid" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove" v-if="user.id">{{ 'Edit User' | trans }}</h2>
            <h2 class="uk-margin-remove" v-if="!user.id">{{ 'Add User' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-margin-small-right" v-attr="href: $url.route('admin/user')">{{ user.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-grid" data-uk-grid-margin>
        <div class="uk-width-medium-2-3 uk-width-large-3-4">

            <div class="uk-form-row">
                <label for="form-username" class="uk-form-label">{{ 'Username' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-username" class="uk-form-width-large" type="text" name="username" v-model="user.username" v-valid="required">
                    <p class="uk-form-help-block uk-text-danger" v-show="form.username.invalid">{{ 'Username cannot be blank.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-name" class="uk-form-width-large" type="text" name="name" v-model="user.name" v-valid="required">
                    <p class="uk-form-help-block uk-text-danger" v-show="form.name.invalid">{{ 'Name cannot be blank.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-email" class="uk-form-label">{{ 'Email' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-email" class="uk-form-width-large" type="text" name="email" v-model="user.email" v-valid="email, required" lazy>
                    <p class="uk-form-help-block uk-text-danger" v-show="form.email.invalid">{{ 'Field must be a valid email address.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-password" class="uk-form-label">{{ 'Password' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text js-password" v-show="user.id">
                    <a href="#" data-uk-toggle="{ target: '.js-password' }">{{ 'Change password' | trans }}</a>
                </div>
                <div class="uk-form-controls js-password" v-class="'uk-hidden' : user.id">
                    <div class="uk-form-password">
                        <input id="form-password" class="uk-form-width-large" type="password" v-model="password">
                        <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: '{{ 'Show' | trans }}', lblHide: '{{ 'Hide' | trans }}' }">{{ 'Show' | trans }}</a>
                    </div>
                </div>
            </div>

            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Status' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed" v-repeat="statuses">
                        <label><input type="radio" v-model="user.status" value="{{ $key }}" v-attr="disabled: config.currentUser == user.id" number> {{ $value }}</label>
                    </p>
                </div>
            </div>

            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Roles' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed" v-repeat="role: roles">
                        <label><input type="checkbox" value="{{ role.id }}" v-checkbox="user.roles" v-attr="disabled: role.disabled" number> {{ role.name }}</label>
                    </p>
                </div>
            </div>

            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Last login' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p>{{ $trans('%date%', { date: user.login ? $date(user.login) : $trans('Never') }) }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Registered since' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    {{ $trans('%date%', { date: $date(user.registered) }) }}
                </div>
            </div>

        </div>
        <div class="uk-width-medium-1-3 uk-width-large-1-4">

            <div class="uk-panel uk-panel-box uk-text-center" v-show="user.name">

                <div class="uk-panel-teaser">
                    <img height="280" width="280" v-attr="alt: user.name" v-gravatar="user.email">
                </div>

                <h3 class="uk-panel-tile uk-margin-bottom-remove uk-text-break">{{ user.name }}
                    <i title="{{ (isNew ? 'New' : statuses[user.status]) | trans }}" v-class="
                        pk-icon-circle-primary: isNew,
                        pk-icon-circle-success: user.access && user.status,
                        pk-icon-circle-danger: !user.status
                    "></i>
                </h3>

                <div>
                    <a class="uk-text-break" href="mailto:{{ user.email }}">{{ user.email }}</a><i class="uk-icon-check" title="{{ 'Verified email address' | trans }}" v-show="config.emailVerification && user.data.verified"></i>
                </div>

            </div>

        </div>
    </div>

</form>
