<template>

    <a class="uk-button uk-button-primary uk-form-file">
        <span v-show="!progress">{{ 'Upload' | trans }}</span>
        <span v-show="progress"><i class="uk-icon-spinner uk-icon-spin"></i> {{ progress }}</span>
        <input type="file" name="file" v-el="input">
    </a>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog">

            <package-details api="{{ api }}" package="{{ package }}"></package-details>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" v-on="click: install">{{ 'Install' | trans }}</button>
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

                var vm = this;

                this.progress = '100%';

                setTimeout(function () {
                    vm.progress = '';
                }, 250);

                if (data.error) {
                    this.$notify(data.error, 'danger');
                    return;
                }

                this.$set('upload', data);
                this.$set('package', data.package);

                this.modal.show();
            },

            install: function (e) {

                e.preventDefault();
                var output = this.$addChild(require('./output.vue'));

                this.modal.hide();
                output.open();

                this.$http.post('admin/system/package/install', {package: this.upload.package}, null, {
                    beforeSend: function (request) {
                        request.onprogress = function () {
                            output.setOutput(this.responseText);
                        };
                    }
                }).success(function () {
                    output.close();
                    this.$notify(this.$trans('"%title%" installed.', {title: this.package.title}));

                    setTimeout(function () {
                        location.reload();
                    }, 600);

                }).error(function (msg) {
                    this.$notify(msg, 'danger');
                });
            }

        }

    };

</script>
