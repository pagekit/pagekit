<?php $view->script('user-edit', 'app/modules/user/app/edit.js', ['vue-system', 'vue-validator', 'uikit-form-password', 'gravatar']) ?>

<form id="js-user-edit" name="form" class="uk-form uk-form-horizontal" v-on="valid: save" v-cloak>

    <?php $view->section()->start('toolbar') ?>
        <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        <a class="uk-button" v-attr="href: $url('admin/user')">{{ user.id ? 'Close' : 'Cancel' | trans }}</a>
    <?php $view->section()->stop(true) ?>

    <div class="uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match>
        <div class="uk-width-medium-1-4 pk-sidebar-left">

            <div class="uk-panel uk-panel-divider uk-text-center">

                <p>
                    <img v-gravatar="user.email" class="uk-border-circle" height="150" width="150">
                </p>
                <ul v-show="user.id" class="uk-list">
                    <li v-if="user.isNew"><span class="uk-badge">{{ 'New' | trans }}</span></li>
                    <li v-if="!user.isNew"><span class="uk-badge uk-badge-{{ user.status ? 'success' : 'danger' }}">{{ statuses[user.status] }}</span></li>

                    <li>{{ user.name }} ({{ user.username }})</li>
                    <li><a href="mailto:{{ user.email }}">{{ user.email }}</a><i v-show="config.emailVerification && user.data.verified" title="{{ 'Verified email address' | trans }}" class="uk-icon-check"></i></li>
                    <li>{{ $trans('Last login: %date%', { date: user.login ? $date('medium', user.login) : $trans('Never') }) }}</li>
                    <li>{{ $trans('Registered since: %date%', { date: $date('medium', user.registered) }) }}</li>
                </ul>

            </div>

        </div>
        <div class="uk-width-medium-3-4">

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
                    <input id="form-email" class="uk-form-width-large" type="text" name="email" v-model="user.email" v-valid="email" lazy>
                    <p class="uk-form-help-block uk-text-danger" v-show="form.email.invalid">{{ 'Field must be a valid email address.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label for="form-password" class="uk-form-label">{{ 'Password' | trans }}</label>

                <div v-show="user.id" class="uk-form-controls uk-form-controls-text js-password">
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
                    <p v-repeat="statuses" class="uk-form-controls-condensed">
                        <label><input type="radio" v-model="user.status" value="{{ $key }}" v-attr="disabled: config.currentUser == user.id"> {{ $value }}</label>
                    </p>
                </div>
            </div>

            <div v-show="" class="uk-form-row">
                <span class="uk-form-label">{{ 'Roles' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p v-repeat="role: roles" class="uk-form-controls-condensed">
                        <label><input type="checkbox" v-model="role.selected" v-attr="disabled: role.disabled"> {{ role.name }}</label>
                    </p>
                </div>
            </div>

        </div>
    </div>

</form>
