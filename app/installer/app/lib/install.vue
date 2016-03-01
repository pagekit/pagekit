<template>

    <div>
        <v-modal v-ref:output :options="options">

            <div class="uk-modal-header uk-flex uk-flex-middle">
                <h2>{{ 'Installing %title% %version%' | trans {title:pkg.title,version:pkg.version} }}</h2>
            </div>

            <pre class="pk-pre uk-text-break" v-html="output"></pre>

            <v-loader v-show="status == 'loading'"></v-loader>

            <div class="uk-alert uk-alert-success" v-show="status == 'success'">{{ 'Successfully installed.' | trans }}</div>
            <div class="uk-alert uk-alert-danger" v-show="status == 'error'">{{ 'Error' | trans}}</div>

            <div class="uk-modal-footer uk-text-right" v-show="status != 'loading'">
                <a class="uk-button uk-button-link" @click.prevent="close">{{ 'Close' | trans }}</a>
                <a class="uk-button uk-button-primary" @click.prevent="enable" v-show="status == 'success'">{{ 'Enable' | trans }}</a>
            </div>

        </v-modal>
    </div>

</template>

<script>

    module.exports = {

        mixins: [require('./output')],

        methods: {

            install: function (pkg, packages, onClose, packagist) {
                this.$set('pkg', pkg);
                this.cb = onClose;


                return this.$http.post('admin/system/package/install', {package: pkg, packagist: Boolean(packagist)},null , {xhr: this.init()}).then(function () {
                            if (this.status === 'success' && packages) {
                                var index = _.findIndex(packages, 'name', pkg.name);

                                if (-1 !== index) {
                                    packages.splice(index, 1, pkg);
                                } else {
                                    packages.push(pkg);
                                }
                            }
                        }, function (msg) {
                            this.$notify(msg.data, 'danger');
                            this.close();
                        });
            },

            enable: function () {
                this.$parent.enable(this.pkg);
                this.close();
            }
        }

    };

</script>
