<template>

    <a class="uk-button uk-button-primary uk-form-file">
        <span v-show="!progress">{{ 'Upload' | trans }}</span>
        <span v-show="progress"><i class="uk-icon-spinner uk-icon-spin"></i> {{ progress }}</span>
        <input id="upload-extension" type="file" name="file">
    </a>


    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog">

            <div class="uk-alert uk-alert-danger uk-hidden" data-msg="checksum-mismatch">
                {{ 'The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.' | trans }}
            </div>

            <div class="uk-alert uk-alert-success uk-hidden" data-msg="update-available">
                {{ 'There is an update available for the uploaded package. Please consider installing it instead.' | trans }}
            </div>

            <div class="uk-grid">
                <div class="uk-width-1-1">
                    <img class="uk-align-left uk-margin-bottom-remove" width="50" height="50" alt="{{ pkg.title }}" v-attr="src: pkg.extra.image">
                    <h1 class="uk-h2 uk-margin-remove">{{ pkg.title }}</h1>
                    <ul class="uk-subnav uk-subnav-line uk-margin-top-remove">
                        <li>{{ pkg.author.name }}</li>
                        <li>{{ 'Version' | trans }} {{ pkg.version }}</li>
                    </ul>
                </div>
            </div>

            <hr class="uk-grid-divider">

            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <div>{{ pkg.description }}</div>
                    <ul>
                        <li>{{ 'Path:' | trans }} {{ pkg.name }}</li>
                        <li>{{ 'Type:' | trans }} {{ pkg.type }}</li>
                    </ul>
                </div>
            </div>

            <p>
                <button class="uk-button uk-button-primary" v-on="click: install()">{{ 'Install' | trans }}</button>
                <button class="uk-button uk-modal-close">{{ 'Cancel' | trans }}</button>
            </p>

        </div>
    </div>

</template>

<script>

    var $ = require('jquery');
    var Vue = require('vue');

    module.exports = {

        replace: true,

        template: __vue_template__,

        paramAttributes: ['type'],

        data: function () {
            return {
                pkg: {},
                upload: null,
                action: '',
                progress: ''
            };
        },

        ready: function () {

            var vm = this;

            var settings = {
                action: this.$url('admin/system/package/upload'),
                type: 'json',
                param: 'file',
                before: function (options) {
                    $.extend(options.params, { _csrf: $pagekit.csrf, type: vm.type });
                },
                loadstart: this.onStart,
                progress: this.onProgress,
                allcomplete: this.onComplete
            };

            UIkit.uploadSelect($('#upload-extension'), settings);

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

                console.log(data)

                var self = this;

                this.progress = '100%';

                setTimeout(function (){
                    self.progress = '';
                }, 250);

                if (data.error) {
                    UIkit.notify(data.error, 'danger');
                    return;
                }

                this.$set('upload', data);
                this.$set('pkg', data.package);

                // $.post(params.api + '/package/' + data.package.name, function (info) {

                //     var version = info.versions[data.package.version];

                //     if (version && version.dist.shasum != data.package.shasum) {
                //         show('checksum-mismatch', upload);
                //     }

                // }, 'jsonp');

                this.modal.show();
            },

            install: function() {

                var vm = this;

                vm.modal.hide();

                this.$http.post('admin/system/package/install', {path: this.upload.install}, function (data) {

                    UIkit.notify(data.message, 'success');

                    setTimeout(function() { location.reload(); }, 600);

                }).error(function(msg) {

                    UIkit.notify(msg, 'danger');
                });
            }

        }

    };

    Vue.component('v-upload', module.exports);

</script>
