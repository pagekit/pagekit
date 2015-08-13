module.exports = {

    data: function() {
        return _.extend({
            view: 'index',
            update: false,
            progress: 0,
            errors: [],
            steps: [
                { action: 'download', msg: 'Downloading...', progress: 33 },
                { action: 'copy', msg: 'Copying files...', progress: 66 },
                { action: 'database', msg: 'Updating database...', progress: 100 }
            ]
        }, window.$data);
    },

    created: function() {
        this.resource = this.$resource('admin/system/update/:action');
        this.getVersions();
    },

    computed: {

        hasUpdate: function() {
            return this.update && this.update.version != this.version;
        }

    },

    methods: {

        getVersions: function() {

            this.$http.jsonp(this.api.url + '/update', function(data) {

                this.$set('update', data[this.channel == 'nightly' ? 'nightly' : 'latest']);

            }).error(function() {

                this.errors.push(self.$trans('Cannot connect to the server. Please try again later.'), 'error');

            });

        },

        install: function() {
            this.$set('view', 'installation');
            this.step({ update: this.update });
        },

        step: function(data) {

            var vm = this, step = this.steps.shift();

            if (!step) return;

            this.$set('message', this.$trans(step.msg));

            this.resource.get(_.extend({ action: step.action }, data), function(data) {

                if (data.error) {
                    this.errors.push(data.error || this.$trans('Whoops, something went wrong.'));
                    return;
                }

                this.$set('progress', step.progress);

                if (!this.steps.length) {

                    this.$set('message', this.$trans('Installed successfully.'));

                    setTimeout(function() {
                        window.location = vm.$url.route('admin');
                    }, 1000);
                }

                this.step();

            }).error(function() {
                this.errors.push(this.$trans('Whoops, something went wrong.'));
            });
        }

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#update');

});
