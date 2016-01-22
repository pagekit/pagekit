var Version = require('../lib/version');

module.exports = {

    el: '#update',

    data: function () {
        return _.extend({
            view: 'index',
            status: 'success',
            finished: false,
            update: false,
            output: '',
            progress: 0,
            changelog: [],
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

            this.$http.get(this.api + '/api/update').then(
                function (res) {
                    var data = res.data;
                    var channel = data[this.channel == 'nightly' ? 'nightly' : 'latest'];

                    if (channel) {
                        this.$set('update', channel);
                    } else {
                        this.error(this.$trans('Cannot obtain versions. Please try again later.'));
                    }
                }, function () {
                    this.error(this.$trans('Cannot connect to the server. Please try again later.'));
                });

        },

        install: function () {
            this.$set('view', 'installation');
            this.doDownload(this.update);
        },

        doDownload: function (update) {
            this.$set('progress', 33);
            this.$http.post('admin/system/update/download', {
                url: update.url
            }).then(this.doInstall, this.error);
        },

        doInstall: function () {
            var vm = this;

            this.$set('progress', 66);
            this.$http.get('admin/system/update/update', null, {
                xhr: {
                    onprogress: function () {
                        vm.setOutput(this.responseText);
                    }
                }
            }).then(this.doMigration, this.error);
        },

        doMigration: function () {
            this.$set('progress', 100);
            if (this.status === 'success') {
                this.$http.get('admin/system/migration/migrate').then(function (res) {
                    var data = res.data;
                    this.output += "\n\n" + data.message;
                    this.finished = true;
                }, this.error);
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

    },

    filters: {
        marked: marked
    },

    watch: {

        update: function (update) {

            var changelog;

            this.$http.get('https://api.github.com/repos/pagekit/pagekit/releases', null, {
                cache: {
                    key: 'changelog-' + update.version,
                    lifetime: 60
                }
            }).then(function (res) {

                var vm = this, changelog = [];

                res.data.forEach(function (release) {

                    if (Version.compare(release.name, vm.version, '>')) {
                        changelog.push({version: release.name, desc: release.body})
                    }

                });

                this.changelog = changelog;

            });
        }

    }

};

Vue.ready(module.exports);
