<template>

    <div class="uk-modal-header uk-flex uk-flex-middle">
        <img class="uk-margin-right" width="50" height="50" :alt="package.title" :src="package | icon" v-if="package.extra && package.extra.icon">

        <div class="uk-flex-item-1">
            <h2 class="uk-margin-small-bottom">{{ package.title }}</h2>
            <ul class="uk-subnav uk-subnav-line uk-margin-bottom-remove">
                <li class="uk-text-muted" v-if="package.authors && package.authors[0]">{{ package.authors[0].name }}</li>
                <li class="uk-text-muted">{{ 'Version %version%' | trans {version:package.version} }}</li>
            </ul>
        </div>
    </div>

    <div class="uk-alert uk-alert-danger" v-show="messages.checksum">
        {{ 'The checksum of the uploaded package does not match the one from the marketplace. The file might be
        manipulated.' | trans }}
    </div>

    <div class="uk-alert" v-show="messages.update">
        {{ 'There is an update available for the package.' | trans }}
    </div>

    <p>{{ package.description }}</p>

    <ul class="uk-list">
        <li v-if="package.license"><strong>{{ 'License:' | trans }}</strong> {{ package.license }}</li>
        <template v-if="package.authors && package.authors[0]">
        <li v-if="package.authors[0].homepage"><strong>{{ 'Homepage:' | trans }}</strong>
            <a :href="package.authors[0].homepage" target="_blank">{{ package.authors[0].homepage }}</a></li>
        <li v-if="package.authors[0].email"><strong>{{ 'Email:' | trans }}</strong>
            <a href="mailto:{{ package.authors[0].email }}">{{ package.authors[0].email }}</a></li>
        </template>
    </ul>

    <img width="1200" height="800" :alt="package.title" :src="package | image" v-if="package.extra && package.extra.image">

</template>

<script>

    var Version = require('../lib/version');

    module.exports = {

        props: {
            api: {
                type: String,
                default: ''
            },
            package: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },

        data: function () {
            return {
                messages: {}
            };
        },

        filters: {

            icon: function (pkg) {

                var extra = pkg.extra || {};

                if (!extra.icon) {
                    return this.$url('app/system/assets/images/placeholder-icon.svg');
                } else if (!extra.icon.match(/^(https?:)?\//)) {
                    return pkg.url + '/' + extra.icon;
                }

                return extra.icon;
            },

            image: function (pkg) {

                var extra = pkg.extra || {};

                if (!extra.image) {
                    return this.$url('app/system/assets/images/placeholder-image.svg');
                } else if (!extra.image.match(/^(https?:)?\//)) {
                    return pkg.url + '/' + extra.image;
                }

                return extra.image;
            }

        },

        watch: {

            package: {

                handler: function () {

                    if (!this.package.name) {
                        return;
                    }

                    if (_.isArray(this.package.authors)) {
                        this.package.author = this.package.authors[0];
                    }

                    this.$set('messages', {});

                    this.queryPackage(this.package, function (res) {
                        var data = res.data;

                        var version = this.package.version, pkg = data.versions[version];

                        // verify checksum
                        if (pkg && this.package.shasum) {
                            this.$set('messages.checksum', pkg.dist.shasum != this.package.shasum);
                        }

                        // check version
                        _.each(data.versions, function (pkg) {
                            if (Version.compare(pkg.version, version, '>')) {
                                version = pkg.version;
                            }
                        });

                        this.$set('messages.update', version != this.package.version);
                    });
                },

                immediate: true

            }
        },

        methods: {

            queryPackage: function (pkg, success) {
                return this.$http.get(this.api + '/api/package/{+name}', {
                    name: _.isObject(pkg) ? pkg.name : pkg
                }).then(success, this.error);
            }

        }

    }

</script>
