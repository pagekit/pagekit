<?php $view->script('user-edit', 'system/user:app/bundle/user-edit.js', ['vue', 'users', 'uikit-form-password']) ?>

<form id="user-edit" class="uk-form uk-form-horizontal" name="form" v-on="valid: save" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove" v-if="user.id">{{ 'Edit User' | trans }}</h2>
            <h2 class="uk-margin-remove" v-if="!user.id">{{ 'Add User' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-margin-small-right" v-attr="href: $url('admin/user')">{{ user.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-grid" data-uk-grid-margin>
        <div class="uk-width-medium-2-3 uk-width-large-3-4">

			<ul class="uk-tab" v-el="tab">
				<li v-repeat="section: sections | active | orderBy 'priority'"><a>{{ section.label | trans }}</a></li>
			</ul>

			<div class="uk-switcher uk-margin-large-top" v-el="content">
				<div v-repeat="section: sections | active | orderBy 'priority'">
					<component is="{{ section.name }}"></component>
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
