<template>

    <a class="uk-button uk-button-primary uk-form-file">
        <span v-show="!progress">{{ 'Upload' | trans }}</span>
        <span v-show="progress"><i class="uk-icon-spinner uk-icon-spin"></i> {{ progress }}</span>
        <input id="upload-extension" type="file" name="file">
    </a>


    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog">

            <div class="uk-alert" v-class="uk-alert-danger:apiserver.error" v-show="apiserver.message">
                {{ apiserver.message | trans }}
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

    module.exports = {

        replace: true,

        template: __vue_template__,

        paramAttributes: ['type'],

        data: function () {
            return {
                pkg: {},
                upload: null,
                action: '',
                progress: '',
                apiserver: {
                    requesting: false,
                    message: '',
                    error: false
                }
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
                this.$set('pkg', data.package);

                this.apiserver.requesting = true;
                this.apiserver.message = '';
                this.apiserver.error = false;

                this.$http.jsonp('http://pagekit.com/api/package/' + data.package.name, function (info) {

                    var pkg = info.versions[data.package.version];

                    if (pkg) {

                        if (pkg.dist.shasum != data.package.shasum) {

                            self.apiserver.message = 'The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.';
                            self.apiserver.error = true;

                        } else {

                            var currentversion = data.package.version;

                            Object.keys(info.versions).forEach(function (version) {

                                if (version_compare(version, currentversion, '>')) {
                                    currentversion = version;
                                }
                            });

                            if (currentversion != data.package.version) {
                                self.apiserver.message = 'There is an update available for the uploaded package. Please consider installing it instead.';
                            }
                        }
                    }

                }).always(function () {
                    self.apiserver.requesting = false;
                });

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

    // source: https://raw.githubusercontent.com/kvz/phpjs/master/functions/info/version_compare.js
    function version_compare(v1, v2, operator) {

        var i = 0, x = 0, compare = 0, vm = { 'dev': -6, 'alpha': -5, 'a': -5, 'beta': -4, 'b': -4, 'RC': -3, 'rc': -3, '#': -2, 'p': 1, 'pl': 1 };

        var prepVersion = function (v) {
            v = ('' + v).replace(/[_\-+]/g, '.');
            v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');
            return (!v.length ? [-8] : v.split('.'));
        };

        numVersion = function (v) {
            return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
        };

        v1 = prepVersion(v1);
        v2 = prepVersion(v2);
        x = Math.max(v1.length, v2.length);

        for (i = 0; i < x; i++) {

            if (v1[i] == v2[i]) {
                continue;
            }

            v1[i] = numVersion(v1[i]);
            v2[i] = numVersion(v2[i]);

            if (v1[i] < v2[i]) {
                compare = -1;
                break;
            } else if (v1[i] > v2[i]) {
                compare = 1;
                break;
            }
        }

        if (!operator) {
            return compare;
        }

        switch (operator) {
            case '>':
            case 'gt':
                return (compare > 0);
            case '>=':
            case 'ge':
                return (compare >= 0);
            case '<=':
            case 'le':
                return (compare <= 0);
            case '==':
            case '=':
            case 'eq':
                return (compare === 0);
            case '<>':
            case '!=':
            case 'ne':
                return (compare !== 0);
            case '':
            case '<':
            case 'lt':
                return (compare < 0);
            default:
                return null;
        }
    }

    Vue.component('v-upload', module.exports);

</script>
