jQuery(function ($) {

    var vm = new Vue({

        el: '#js-update',

        data: {
            config: update.config,
            view: 'index',
            version: false,
            progress: 0,
            errors: [],
            steps: [
                { url: 'admin/system/update/download', msg: 'Downloading...', progress: 33 },
                { url: 'admin/system/update/copy', msg: 'Copying files...', progress: 66 },
                { url: 'admin/system/update/database', msg: 'Updating database...', progress: 100 }
            ]
        },

        compiled: function() {
            this.getVersions();
        },

        computed: {

            hasUpdate: function() {
                return this.version && this.version.version != this.config.version;
            }

        },

        methods: {

            getVersions: function() {

                $.post(this.config.api + '/update', function(data) {

                    vm.$set('version', data[vm.config.channel == 'nightly' ? 'nightly' : 'latest']);

                }, 'jsonp').fail(function() {

                    this.errors.push(self.$trans('Cannot connect to the server. Please try again later.'), 'error');

                }).always(function() {
                    // TODO emit ready
                });

            },

            install: function() {
                this.$set('view', 'installation');
                this.step({ update: this.version });
            },

            step: function(data) {

                var step = this.steps.shift();

                if (!step) return;

                this.$set('message', this.$trans(step.msg));

                $.getJSON(this.$url(step.url), data || {}, function(data) {

                    if (data !== 'success' || data.error) {
                        vm.errors.push(data.error || vm.$trans('Whoops, something went wrong.'));
                        return;
                    }

                    vm.$set('progress', step.progress);

                    if (!vm.steps.length) {

                        vm.$set('message', vm.$trans('Installed successfully.'));

                        setTimeout(function() {
                            window.location = vm.$url('admin');
                        }, 1000);
                    }

                    vm.step();

                })
                .error(function() {
                    vm.errors.push(vm.$trans('Whoops, something went wrong.'));
                });
            }

        }

    });

});
