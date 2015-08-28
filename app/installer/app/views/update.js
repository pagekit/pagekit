var Output = require('../components/output.vue');
var Version = require('../lib/version');

module.exports = {

    data: function () {
        return _.extend({
            update: false,
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

            this.$http.get(this.api + '/update', function (data) {
                this.$set('update', data[this.channel == 'nightly' ? 'nightly' : 'latest']);
            }).error(function () {
                this.errors.push(this.$trans('Cannot connect to the server. Please try again later.'));
            });

        },

        install: function () {

            var output = this.$addChild(Output);
            var vm = this;

            output.onClose(function () {
                if (this.status == 'success') {
                    window.location = vm.$url.route('admin');
                }
            });

            this.$http.post('admin/system/update/download', {url: this.update.url, shasum: this.update.shasum})
                .error(function (msg) {
                    this.$notify(msg, 'danger');
                })
                .success(function (data) {

                    this.$http.get('', {file: data.file}, null, {
                        headers: {
                            'X_UPDATE_MODE': true,
                            'X_SECURITY_TOKEN': data.token
                        },
                        beforeSend: function (request) {
                            output.init(request, this.$trans('Updating to Pagekit %version%', {version: this.update.version}));
                        }
                    }).error(function (msg) {
                        output.close();
                        this.$notify(msg, 'danger');
                    });

                });
        }

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#update');

});
