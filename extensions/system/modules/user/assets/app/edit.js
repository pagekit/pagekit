require(['jquery', 'uikit!form-password', 'gravatar', 'system!locale', 'domReady!'], function($, uikit, gravatar, system) {

    $(document).on('ajaxSend', function(e, xhr){
        xhr.setRequestHeader('X-XSRF-TOKEN', pagekit.csrf.token);
    });

    Vue.filter('trans', function(key, params) {
        return system.translator.trans(key, params);
    });

    Vue.filter('transChoice', function(key, num, params) {
        return system.translator.transChoice(key, num, params);
    });

    var app = new Vue({

        el: '#js-user-edit',

        data: {
            config: user.config,
            user: user.data.user,
            roles: user.data.roles,
            statuses: user.data.statuses
        },

        ready: function() {
            this.$watch('user.status', function(status) {
                if ('string' === typeof status) {
                    app.user.status = parseInt(status);
                }
            });
        },

        computed: {
            gravatar: function() {
                return gravatar.url(this.user.email, {s: 300, d: 'mm', r: 'g'});
            },

            loginText: function() {
                return system.trans('Last login: %date%', { '%date%': this.user.login ? this.getDate('medium', this.user.login) : system.translator.trans('Never') });
            },

            registeredText: function() {
                return system.trans('Registered since: %date%', { '%date%': this.getDate('medium', this.user.registered) });
            }
        },

        methods: {
            getDate: function (format, date) {
                return system.date(format, date);
            },

            save: function (e) {
                e.preventDefault();
                var roles = this.roles.filter(function(role) { return role.selected; }).map(function(role) { return role.id });

                $.post(this.config.urls.user, { id: this.user.id, user: this.user, password: this.password, roles: roles }, function(data) {

                    if (data.user) {
                        app.user = data.user;
                    }

                    uikit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            }
        }

    });

});
