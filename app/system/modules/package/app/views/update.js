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

            this.$http.jsonp(this.api.url + '/update', function (data) {

                this.$set('update', data[this.channel == 'nightly' ? 'nightly' : 'latest']);

            }).error(function () {

                this.errors.push(self.$trans('Cannot connect to the server. Please try again later.'), 'error');

            });

        },

        install: function () {

            var output = this.$addChild(Output);
            var vm = this;
            output.onClose(function () {
                window.location = vm.$url.route('admin');
            });

            return this.$http.post('admin/system/update/run', {update: this.update}, null, {
                beforeSend: function (request) {
                    output.init(request, this.$trans('Updating to Pagekit %version%', {version: this.update.version}));
                }
            }).error(function (msg) {
                output.close();
                this.$notify(msg, 'danger');
            });
        }

    }

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#update');

});
