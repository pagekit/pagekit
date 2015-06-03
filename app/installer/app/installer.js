jQuery(function ($) {

    var Installer, Email = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    var vm = new Vue({

        el: '#installer',

        data: {
            step: 'start',
            status: '',
            message: '',
            config: {},
            option: {},
            user: {}
        },

        ready: function () {

            Installer   = this.$resource('installer/:action', {}, {'post': {method: 'POST'}});
        },

        methods: {

            gotoStep: function(step) {

                if (UIkit.support.animation) {

                    var vm      = this,
                        slides  = $('[data-step]', this.$el),
                        current = slides.filter(':visible'),
                        next    = slides.filter('[data-step="'+step+'"]');

                    UIkit.Utils.animate(current, 'uk-animation-slide-left uk-animation-reverse').then(function(){

                        current.css('display', 'none');

                        UIkit.Utils.animate(next, 'uk-animation-slide-right').then(function(){
                            vm.$set('step', step);
                        });
                    });

                } else {
                    this.$set('step', step);
                }
            },

            stepDatabase: function (e) {
                e.preventDefault();

                Installer.post({action: 'check'}, {config: this.config}, function (data) {

                    if (!Vue.util.isPlainObject(data)) {
                        data = {message: 'Whoops, something went wrong'};
                    }

                    if (data.status == 'no-tables') {
                        vm.gotoStep('user');
                    } else {
                        vm.$set('status', data.status);
                        vm.$set('message', data.message);
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

                Installer.post({action: 'install'}, {config: this.config, option: {'system': this.option}, user: this.user}, function (data) {

                    if (!Vue.util.isPlainObject(data)) {
                        data = {message: 'Whoops, something went wrong'};
                    }

                    if (data.status == 'success') {
                        vm.gotoStep('finished');
                    } else {
                        vm.$set('status', 'failed');
                        vm.$set('message', data.message);
                    }

                });
            }

        }

    });

});
