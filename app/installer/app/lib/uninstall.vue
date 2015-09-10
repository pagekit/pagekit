<template>

    <div>
        <v-modal v-ref="output" options="{{ options }}">

            <div class="uk-modal-header uk-flex uk-flex-middle">
                <h2>{{ 'Removing %title% %version%' | trans {title:pkg.title,version:pkg.version} }}</h2>
            </div>

            <pre class="pk-pre uk-text-break" v-html="output"></pre>

            <v-loader v-show="status == 'loading'"></v-loader>

            <div class="uk-alert uk-alert-success" v-show="status == 'success'">{{ 'Successfully removed.' | trans }}</div>
            <div class="uk-alert uk-alert-danger" v-show="status == 'error'">{{ 'Error' | trans}}</div>

            <div class="uk-modal-footer uk-text-right" v-show="status != 'loading'">
                <a class="uk-button uk-button-link" v-on="click: close">{{ 'Close' | trans }}</a>
            </div>

        </v-modal>
    </div>

</template>

<script>

    module.exports = {

        methods: {

            uninstall: function (pkg, packages) {
                this.$set('pkg', pkg);

                var vm = this;
                return this.$http.post('admin/system/package/uninstall', {name: pkg.name}, null, {
                    beforeSend: function (request) {
                        vm.init(request);
                    }
                }).success(function () {
                    if (this.status === 'success' && packages) {
                        packages.splice(packages.indexOf(pkg), 1);
                    }
                }).error(function (msg) {
                    this.$notify(msg, 'danger');
                    this.close();
                });
            }

        },

        mixins: [
            require('./output')
        ]
    };

</script>
