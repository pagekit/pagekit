<template>

    <form class="pk-panel-teaser uk-form uk-form-stacked" v-if="editing">

        <div class="uk-margin uk-flex uk-flex-middle">
            <h3 class="uk-margin-remove">{{ 'User Widget' | trans }}</h3>
            <a class="pk-icon-delete pk-icon-hover uk-margin-left" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()"></a>
        </div>

        <div class="uk-form-row">
            <span class="uk-form-label">{{ 'User Type' | trans }}</span>
            <div class="uk-form-controls uk-form-controls-text">
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="login" v-model="widget.show"> {{ 'Logged in' | trans }}</label>
                </p>
                <p class="uk-form-controls-condensed">
                    <label><input type="radio" value="registered" v-model="widget.show"> {{ 'Last registered' | trans }}</label>
                </p>
            </div>
        </div>

        <div class="uk-form-row">
            <label class="uk-form-label" for="form-user-number">{{ 'Number of Users' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-user-number" class="uk-width-1-1" v-model="widget.count" number>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                </select>
            </div>
        </div>

    </form>

    <h3 class="uk-panel-title" v-if="widget.show == 'registered'">
        {{ '{0} No users registered|{1} Last %users% registered user|]1,Inf[ Last %users% registered users' | transChoice users.length {users:users.length} }}
    </h3>
    <h3 class="uk-panel-title" v-if="widget.show != 'registered'">
        {{ '{0} No users logged in|{1} %users% user logged in|]1,Inf[ %users% users logged in' | transChoice users.length {users:users.length} }}
    </h3>

    <ul v-show="users.length" data-user class="uk-grid uk-grid-small uk-grid-width-1-4 uk-grid-width-small-1-6 uk-grid-width-medium-1-3 uk-grid-width-xlarge-1-4" data-uk-grid-margin>
        <li v-repeat="user: users">
            <a href="{{ $url('admin/user/edit', {id: user.id}) }}" title="{{ user.username }}">
                <img class="uk-border-rounded" width="200" height="200" alt="{{ user.username }}" v-gravatar="user.email">
            </a>
        </li>
    </ul>

</template>

<script>

    module.exports = {

        type: {

            id: 'user',
            label: 'User',
            description: function () {

            },
            defaults: {
                show: 'login',
                count: 5
            }

        },

        template: __vue_template__,

        ready: function() {

            this.$watch('widget.show', this.load, false, true);
            this.$watch('widget.count', this.load, false, false);

        },

        methods: {

            load: function() {

                var query;

                if (this.$get('widget.show') === 'registered') {
                    query = {
                        sort: 'registered',
                        order: 'DESC'
                    }
                } else {
                    query = {
                        filter: { access: 300 }
                    }
                }

                query.limit = this.$get('widget.count');

                this.$resource('api/user/:id').query(query, function(data) {
                    this.$set('users', data.users);
                });

            }

        }

    };

    window.Dashboard.component('user', module.exports)

</script>
