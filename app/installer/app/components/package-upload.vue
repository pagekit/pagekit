<template>

    <a class="uk-button uk-button-primary uk-form-file">
        <span v-show="!progress">{{ 'Upload' | trans }}</span>
        <span v-else><i class="uk-icon-spinner uk-icon-spin"></i> {{ progress }}</span>
        <input type="file" name="file" v-el:input>
    </a>

    <div class="uk-modal" v-el:modal>
        <div class="uk-modal-dialog">

            <package-details :api="api" :package="package"></package-details>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" @click.prevent="doInstall">{{ 'Install' | trans }}</button>
            </div>

        </div>
    </div>

</template>

<script>

    module.exports = {

        mixins: [
            require('../lib/package')
        ],

        props: {
            api: {type: String, default: ''},
            packages: Array,
            type: String
        },

        data: function () {
            return {
                package: {},
                upload: null,
                progress: ''
            };
        },

        ready: function () {

            var type = this.type,
                settings = {
                    action: this.$url.route('admin/system/package/upload'),
                    type: 'json',
                    param: 'file',
                    before: function (options) {
                        _.merge(options.params, {_csrf: $pagekit.csrf, type: type});
                    },
                    loadstart: this.onStart,
                    progress: this.onProgress,
                    allcomplete: this.onComplete
                };

            UIkit.uploadSelect(this.$els.input, settings);

            this.modal = UIkit.modal(this.$els.modal);
        },

        methods: {

            onStart: function () {
                this.progress = '1%';
            },

            onProgress: function (percent) {
                this.progress = Math.ceil(percent) + '%';
            },

            onComplete: function (data) {

                var vm = this;

                this.progress = '100%';

                setTimeout(function () {
                    vm.progress = '';
                }, 250);

                if (!data.package) {
                    this.$notify(data, 'danger');
                    return;
                }

                this.$set('upload', data);
                this.$set('package', data.package);

                this.modal.show();
            },

            doInstall: function () {

                this.modal.hide();

                this.install(this.upload.package, this.packages,
                    function (output) {
                        if (output.status === 'success') {
                            setTimeout(function () {
                                location.reload();
                            }, 300);
                        }
                    }, true);
            }

        },

        components: {

            'package-details': require('./package-details.vue')

        }
    };

</script>
