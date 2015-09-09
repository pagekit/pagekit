var Version = require('../lib/version');

module.exports = {

    data: function () {
        return _.extend({
            view: 'index',
            status: 'success',
            finished: false,
            update: false,
            output: '',
            progress: 0,
            errors: []
        }, window.$data);
    },

    created: function () {
        this.getVersions();
    },

    computed: {

        hasUpdate: function () {
            return this.update && Version.compare(this.update.version, this.version, '>');
        }

    },

    methods: {

        getVersions: function () {

            this.$http.get(this.api + '/api/update', function (data) {
                this.$set('update', data[this.channel == 'nightly' ? 'nightly' : 'latest']);
            }).error(function () {
                this.errors.push(this.$trans('Cannot connect to the server. Please try again later.'));
            });

        },

        install: function () {
            this.$set('view', 'installation');
            this.doDownload(this.update);
        },

        doDownload: function (update) {
            this.$set('progress', 33);
            this.$http.post('admin/system/update/download', {url: update.url, shasum: update.shasum})
                .success(this.doInstall)
                .error(this.error);
        },

        doInstall: function () {
            var vm = this;

            this.$set('progress', 66);
            this.$http.get('admin/system/update/update', this.doMigration, {
                xhr: {
                    onprogress: function () {
                        vm.setOutput(this.responseText);
                    }
                }
            }).error(this.error);
        },

        doMigration: function () {
            this.$set('progress', 100);
            if (this.status === 'success') {
                this.$http.get('admin/system/migration/migrate', function (data) {
                    this.output += "\n\n" + data.message;
                    this.finished = true;
                }).error(this.error);
            } else {
                this.error();
            }
        },

        setOutput: function (output) {
            var lines = output.split("\n");
            var match = lines[lines.length - 1].match(/^status=(success|error)$/);

            if (match) {
                this.status = match[1];
                delete lines[lines.length - 1];
                this.output = lines.join("\n");
            } else {
                this.output = output;
            }

        },

        error: function (error) {
            this.errors.push(error || this.$trans('Whoops, something went wrong.'));

            this.status = 'error';
            this.finished = true;
        }

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#update');

});
