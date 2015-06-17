module.exports = Vue.extend({

    data: function() {
        return {
            config: window.$config,
            view: 'index',
            version: false,
            progress: 0,
            errors: [],
            steps: [
                { url: 'admin/system/update/download', msg: 'Downloading...', progress: 33 },
                { url: 'admin/system/update/copy', msg: 'Copying files...', progress: 66 },
                { url: 'admin/system/update/database', msg: 'Updating database...', progress: 100 }
            ]
        };
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

            this.$http.jsonp(this.config.api + '/update', function(data) {

                this.$set('version', data[this.config.channel == 'nightly' ? 'nightly' : 'latest']);

            }).error(function() {

                this.errors.push(self.$trans('Cannot connect to the server. Please try again later.'), 'error');

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

            this.$http.get(step.url, data || {}, function(data) {

                if (data !== 'success' || data.error) {
                    this.errors.push(data.error || this.$trans('Whoops, something went wrong.'));
                    return;
                }

                this.$set('progress', step.progress);

                if (!this.steps.length) {

                    this.$set('message', this.$trans('Installed successfully.'));

                    setTimeout(function() {
                        window.location = this.$url('admin');
                    }, 1000);
                }

                this.step();

            }).error(function() {
                this.errors.push(this.$trans('Whoops, something went wrong.'));
            });
        }

    }

});

jQuery(function () {

    (new module.exports()).$mount('#update');

});
