<template>

    <div class="uk-alert uk-alert-danger" v-show="messages.checksum">
        {{ 'The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.' | trans }}
    </div>

    <div class="uk-alert" v-show="messages.update">
        {{ 'There is an update available for the uploaded package. Please consider installing it instead.' | trans }}
    </div>

    <div class="uk-grid">
        <div class="uk-width-1-1">
            <img class="uk-align-left uk-margin-bottom-remove" width="50" height="50" alt="{{ package.title }}" v-attr="src: package.extra.image">

            <h1 class="uk-h2 uk-margin-remove">{{ package.title }}</h1>
            <ul class="uk-subnav uk-subnav-line uk-margin-top-remove">
                <li>{{ package.author.name }}</li>
                <li>{{ 'Version' | trans }} {{ package.version }}</li>
            </ul>
        </div>
    </div>

    <hr class="uk-grid-divider">

    <div class="uk-grid">
        <div class="uk-width-1-2">
            <div>{{ package.description }}</div>
            <ul>
                <li>{{ 'Path:' | trans }} /{{ package.name }}</li>
                <li>{{ 'Type:' | trans }} {{ package.type }}</li>
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

        watch: {

            package: function () {

                if (!this.package.name) {
                    return;
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
