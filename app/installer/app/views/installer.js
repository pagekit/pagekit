var Installer = {

    el: '#installer',

    data: function () {
        return _.merge({
            step: 'start',
            status: '',
            message: '',
            config: {},
            option: {},
            user: {}
        }, window.$installer);
    },

    ready: function () {

        this.resource = this.$resource('installer/:action', {}, {post: {method: 'POST'}});
        this.$set('config.database', {default: this.sqlite ? 'sqlite' : 'mysql'})

    },

    methods: {

        gotoStep: function (step) {

            if (UIkit.support.animation) {

                var vm = this, current = this.$els[this.step], next = this.$els[step];

                this.$compile(next);

                UIkit.Utils.animate(current, 'uk-animation-slide-left uk-animation-reverse').then(function () {

                    current.style.display = 'none';

                    UIkit.Utils.animate(next, 'uk-animation-slide-right').then(function () {
                        vm.$set('step', step);
                    });
                });

            } else {
                this.$set('step', step);
            }

        },

        stepLanguage: function () {

            this.$asset({js: [this.$url.route('system/intl/:locale', {locale: this.locale})]}).then(function () {
                this.$set('option.system.admin.locale', this.locale);
                this.$set('option.system.site.locale', this.locale);
                this.$locale = window.$locale;
                this.gotoStep('database');
            });

        },

        stepDatabase: function () {

            var config = _.cloneDeep(this.config);

            _.forEach(config.database.connections, function (connection, name) {
                if (name != config.database.default) {
                    delete(config.database.connections[name]);
                } else if (connection.host) {
                    connection.host = connection.host.replace(/:(\d+)$/, function (match, port) {
                        connection.port = port;
                        return '';
                    });
                }
            });

            this.resource.post({action: 'check'}, {config: config, locale: this.locale}).then(function (res) {

                var data = res.data;
                if (!Vue.util.isPlainObject(data)) {
                    data = {message: 'Whoops, something went wrong'};
                }

                if (data.status == 'no-tables') {
                    this.gotoStep('site');
                    this.config = config;
                } else {
                    this.$set('status', data.status);
                    this.$set('message', data.message);
                }

            });

        },

        stepSite: function () {

            this.gotoStep('finish');
            this.stepInstall();

        },

        stepInstall: function () {

            this.$set('status', 'install');

            this.resource.post({action: 'install'}, {config: this.config, option: this.option, user: this.user, locale: this.locale}).then(function (res) {

                var data = res.data;

                setTimeout(function () {

                    if (!Vue.util.isPlainObject(data)) {
                        data = {message: 'Whoops, something went wrong'};
                    }

                    if (data.status == 'success') {
                        this.$set('status', 'finished');

                        // redirect to login after 3s
                        setTimeout(function () {
                            location.href = this.$url.route('admin');
                        }.bind(this), 3000);

                    } else {
                        this.$set('status', 'failed');
                        this.$set('message', data.message);
                    }


                }.bind(this), 2000);
            });
        }

    }

};

Vue.ready(Installer);
