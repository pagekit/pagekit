var installer = {

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

    },

    methods: {

        gotoStep: function (step) {

            if (UIkit.support.animation) {

                var vm = this, current = this.$$[this.step], next = this.$$[step];

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

        stepLanguage: function (e) {
            e.preventDefault();

            this.$asset({js: [this.$url.route('system/intl/:locale', {locale: this.locale})]}, function () {

                this.$set('option.system.admin.locale', this.locale);
                this.$set('option.system.site.locale', this.locale);

                this.$locale = window.$locale;
                this.gotoStep('database');

            });

        },

        stepDatabase: function (e) {
            e.preventDefault();

            this.resource.post({action: 'check'}, {config: this.config}, function (data) {

                if (!Vue.util.isPlainObject(data)) {
                    data = {message: 'Whoops, something went wrong'};
                }

                if (data.status == 'no-tables') {
                    this.gotoStep('user');
                } else {
                    this.$set('status', data.status);
                    this.$set('message', data.message);
                }

            });
        },

        stepUser: function (e) {
            e.preventDefault();

            this.gotoStep('site');
        },

        stepSite: function (e) {
            e.preventDefault();

            this.gotoStep('finish');
            this.stepInstall();
        },

        stepInstall: function () {

            this.$set('status', 'install');

            this.resource.post({action: 'install'}, {config: this.config, option: this.option, user: this.user}, function (data) {

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

jQuery(function () {
    new Vue(installer).$mount('#installer');
});
