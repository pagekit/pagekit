<template>

    <div class="uk-modal-header uk-flex uk-flex-middle">
        <img class="uk-margin-right" width="50" height="50" alt="{{ package.title }}" v-attr="src: package | icon" v-if="package.type == 'pagekit-extension'">
        <div class="uk-flex-item-1">
            <h2 class="uk-margin-small-bottom">{{ package.title }}</h2>
            <ul class="uk-subnav uk-subnav-line uk-margin-bottom-remove">
                <li class="uk-text-muted">{{ package.author.name }}</li>
                <li class="uk-text-muted">{{ 'Version %version%' | trans {version:package.version} }}</li>
            </ul>
        </div>
    </div>

    <div class="uk-alert uk-alert-danger" v-show="messages.checksum">
        {{ 'The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.' | trans }}
    </div>

    <div class="uk-alert" v-show="messages.update">
        {{ 'There is an update available for the uploaded package. Please consider installing it instead.' | trans }}
    </div>

    <div class="uk-grid uk-grid-medium" v-class="uk-grid-width-1-2: package.type == 'theme'">
        <div v-if="package.type == 'theme'">

            <img class="uk-margin-right" width="800" height="600" alt="{{ package.title }}" v-attr="src: package | icon" >

        </div>
        <div>

            <p>{{ package.description }}</p>

            <ul class="uk-list">
                <li><strong>{{ 'Folder:' | trans }}</strong> /{{ package | folder }}</li>
                <li><strong>{{ 'License:' | trans }}</strong> {{ package.license }}</li>
                <li v-if="package.author.homepage"><strong>{{ 'Homepage:' | trans }}</strong> <a href="{{ package.author.homepage }}" target="_blank">{{ package.author.homepage }}</a></li>
                <li v-if="package.author.email"><strong>{{ 'Email:' | trans }}</strong> <a href="mailto:{{ package.author.email }}">{{ package.author.email }}</a></li>
            </ul>

        </div>
    </div>

</template>

<script>

    var Version = require('../lib/version');

    module.exports = {

        mixins: [
            require('../lib/package')
        ],

        props: ['api', 'package'],

        data: function () {
            return {
                api: {},
                package: {},
                messages: {}
            };
        },

        filters: {

            icon: function (pkg) {

                var extra = pkg.extra || {};

                if (!extra.image) {
                    return this.$url('app/system/assets/images/placeholder-icon.svg');
                } else if (!extra.image.match(/^(https?:)?\//)) {
                    return pkg.url + '/' + extra.image;
                }

                return extra.image;
            },

            folder: function (pkg) {
                return pkg.url && pkg.url.match(/[^\/]+$/gi);
            }

        },

        watch: {

            package: function () {

                if (!this.package.name) {
                    return;
                }

                if (_.isArray(this.package.authors)) {
                    this.package.$add('author', this.package.authors[0]);
                }

                this.$set('messages', {});

                this.queryPackage(this.package, function (data) {

                    var version = this.package.version, pkg = data.versions[version];

                    // verify checksum
                    if (pkg && this.package.shasum) {
                        this.messages.$set('checksum', pkg.dist.shasum != this.package.shasum);
                    }

                    // check version
                    _.each(data.versions, function (pkg) {
                        if (Version.compare(pkg.version, version, '>')) {
                            version = pkg.version;
                        }
                    });

                    this.messages.$set('update', version != this.package.version);
                });
            }

        }

    }

</script>
