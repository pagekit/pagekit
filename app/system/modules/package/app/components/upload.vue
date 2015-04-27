<template>

    <div class="uk-placeholder uk-text-center uk-text-muted" v-el="drop">
        <img v-attr="src: $url.static('app/system/assets/images/finder-droparea.svg')" width="22" height="22" alt="{{ 'Droparea' | trans }}"> {{ 'Drop files here or' | trans }} <a class="uk-form-file">{{ 'select one' | trans }}<input type="file" name="file" v-el="select"></a>
    </div>

    <div class="uk-progress" v-show="progress">
        <div class="uk-progress-bar" v-style="width: progress">{{ progress }}</div>
    </div>

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
                <button class="uk-button uk-button-primary">{{ 'Install' | trans }}</button>
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

        data: function () {
            return {
                pkg: {},
                action: '',
                progress: ''
            };
        },

        ready: function () {

            var settings = {
                action: this.action,
                type: 'json',
                param: 'file',
                loadstart: this.onStart,
                progress: this.onProgress,
                allcomplete: this.onComplete
            };

            UIkit.uploadSelect(this.$$.select, settings);
            UIkit.uploadDrop(this.$$.drop, settings);

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

                setTimeout(function (){
                    self.progress = '';
                }, 250);

                if (data.error) {
                    UIkit.notify(data.error, 'danger');
                    return;
                }

                // $.post(params.api + '/package/' + data.package.name, function (info) {

                //     var version = info.versions[data.package.version];

                //     if (version && version.dist.shasum != data.package.shasum) {
                //         show('checksum-mismatch', upload);
                //     }

                // }, 'jsonp');

                this.modal.show();
            }

        }

    };

    Vue.component('v-upload', module.exports);

</script>