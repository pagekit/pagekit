<template>

    <a class="uk-button uk-button-primary uk-form-file">
        <span v-show="!progress">{{ 'Upload' | trans }}</span>
        <span v-show="progress"><i class="uk-icon-spinner uk-icon-spin"></i> {{ progress }}</span>
        <input type="file" name="file" v-el="input">
    </a>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog">

            <details api="{{ api }}" package="{{ package }}"></details>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" v-on="click: install()">{{ 'Install' | trans }}</button>
            </div>

        </div>
    </div>

</template>

<script>

    module.exports = {

        props: ['api', 'type'],

        data: function () {
            return {
                api: {},
                package: {},
                upload: null,
                progress: ''
            };
        },

        ready: function () {

            var type = this.type, settings = {
                action: this.$url('admin/system/package/upload'),
                type: 'json',
                param: 'file',
                before: function (options) {
                    $.extend(options.params, {_csrf: $pagekit.csrf, type: type});
                },
                loadstart: this.onStart,
                progress: this.onProgress,
                allcomplete: this.onComplete
            };

            UIkit.uploadSelect(this.$$.input, settings);

            this.modal = UIkit.modal(this.$$.modal);
        },

        methods: {

            onStart: function () {
                this.progress = '1%';
            },

            onProgress: function (percent) {
                this.progress = Math.ceil(percent) + '%';
            },

            onComplete: function (data) {

                var self = this;

                this.progress = '100%';

                setTimeout(function () {
                    self.progress = '';
                }, 250);

                if (data.error) {
                    UIkit.notify(data.error, 'danger');
                    return;
                }

                this.$set('upload', data);
                this.$set('package', data.package);

                this.modal.show();
            },

            install: function () {

                var vm = this;

                vm.modal.hide();

                this.$http.post('admin/system/package/install', { path: this.upload.install }, function (data) {

                    UIkit.notify(data.message, 'success');

                    setTimeout(function () {
                        location.reload();
                    }, 600);

                }).error(function (msg) {

                    UIkit.notify(msg, 'danger');
                });
            }

        }

    };

</script>
