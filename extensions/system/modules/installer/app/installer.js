jQuery(function($) {

    var Installer, vm = new Vue({

        el: '#installer',

        data: {
            step: 'start',
            error: '',
            config: {},
            user: {}
        },

        ready: function() {

            Installer = this.$resource('installer/installer/:action', {}, {'get': {type: 'POST'}});

        },

        methods: {

            stepDatabase: function(e) {
                e.preventDefault();

                Installer.get({action: 'check'}, {config: this.config}, function(data) {

                    if (data && data.status) {

                        if (data.status == 'no-tables') {
                            vm.$set('step', 'user');
                            vm.$set('error', '');
                        } else {
                            vm.$set('error', data.message);
                        }

                    } else {
                        alert('Whoops, something went wrong');
                    }

                });

            },

            stepUser: function(e) {
                e.preventDefault();

                this.$set('step', 'site');
            },

            stepSite: function(e) {
                e.preventDefault();

                this.$set('step', 'finish');
            }

        }

    });

});