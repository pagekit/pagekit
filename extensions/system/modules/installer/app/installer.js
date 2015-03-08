jQuery(function($) {

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

        ready: function() {

            Installer = this.$resource('installer/installer/:action', {}, {'post': {type: 'POST'}});

        },

        methods: {

            stepDatabase: function(e) {
                e.preventDefault();

                if (!this.validate('formDatabase')) {
                    return;
                }

                Installer.post({action: 'check'}, {config: this.config}, function(data) {

                    if (!Vue.util.isPlainObject(data)) {
                        data = {message: 'Whoops, something went wrong'};
                    }

                    if (data.status == 'no-tables') {
                        vm.$set('step', 'user');
                    } else {
                        vm.$set('status', data.status);
                        vm.$set('message', data.message);
                    }

                });

            },

            stepUser: function(e) {
                e.preventDefault();

                if (!this.validate('formUser')) {
                    return;
                }

                this.$set('step', 'site');
            },

            stepSite: function(e) {
                e.preventDefault();

                if (!this.validate('formSite')) {
                    return;
                }

                this.$set('step', 'finish');
                this.$set('status', 'install');

                Installer.post({action: 'install'}, {config: this.config, option: {'system:settings': this.option}, user: this.user}, function(data) {

                    if (!Vue.util.isPlainObject(data)) {
                        data = {message: 'Whoops, something went wrong'};
                    }

                    if (data.status == 'success') {
                        vm.$set('status', 'finished');
                    } else {
                        vm.$set('status', 'failed');
                        vm.$set('message', data.message);
                    }

                });

            },

            validate: function(name) {

                var form = $('form[name="' + name + '"]'), validation = true;

                $('input', form).each(function() {

                    var el = $(this), key = el.attr('name'), value = el.val(), check;

                    if (!key) {
                        return;
                    }

                    key = name + '.' + key + '.';

                    if (el.attr('required')) {

                        check = !!value;

                        if (validation && !check) {
                            validation = false;
                        }

                        vm.$set(key + 'required', check);
                    }

                    if (el.attr('type') == 'email') {

                        check = Email.test(value);

                        if (validation && !check) {
                            validation = false;
                        }

                        vm.$set(key + 'email', check);
                    }

                });

                return validation;
            }

        }

    });

});