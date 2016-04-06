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
            releases: [],
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

            this.$http.get(this.api + '/api/update', {version: this.version}).then(
                function (res) {
                    var data = res.data;
                    var channel = data[this.channel == 'nightly' ? 'nightly' : 'latest'];

                    if (channel) {
                        this.update = channel;
                        this.releases = data.versions;
                    } else {
                        this.error(this.$trans('Cannot obtain versions. Please try again later.'));
                    }
                }, function () {
                    this.error(this.$trans('Cannot connect to the server. Please try again later.'));
                }
            );

        },

        install: function () {
            this.$set('view', 'installation');
            this.doDownload(this.update);
        },

        doDownload: function (update) {
            this.$set('progress', 33);
            this.$http.post('admin/system/update/download', {url: update.url}).then(this.doInstall, this.error);
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
            this.errors.push(error.data || this.$trans('Whoops, something went wrong.'));

            this.status = 'error';
            this.finished = true;
        }

    },

    filters: {

        changelog: function (md) {

            var renderer = new marked.Renderer(),
                section;

            renderer.heading = function (text) {
                section = text;
                return '';
            };

            renderer.listitem = function (text) {
                switch (section) {
                    case 'Added':
                        return '<li><span class="uk-badge pk-badge-justify uk-badge-success uk-margin-right">' + section + '</span> ' + text + '</li>';
                    case 'Deprecated':
                        return '<li><span class="uk-badge pk-badge-justify uk-badge-warning uk-margin-right">' + section + '</span> ' + text + '</li>';
                    case 'Removed':
                        return '<li><span class="uk-badge pk-badge-justify uk-badge-warning uk-margin-right">' + section + '</span> ' + text + '</li>';
                    case 'Fixed':
                        return '<li><span class="uk-badge pk-badge-justify uk-badge-danger uk-margin-right">' + section + '</span> ' + text + '</li>';
                    case 'Security':
                        return '<li><span class="uk-badge pk-badge-justify uk-badge-danger uk-margin-right">' + section + '</span> ' + text + '</li>';
                    default:
                        return '<li><span class="uk-badge pk-badge-justify uk-margin-right">' + section + '</span> ' + text + '</li>';
                }
            };

            renderer.list = function (text) {
                return text;
            };

            return marked(md, {renderer: renderer});
        },

        showChangelog: function (version) {
            return Version.compare(version, this.version, '>');
        }

    }

};

Vue.ready(module.exports);
