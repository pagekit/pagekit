<template>

    <div class="uk-modal-header uk-flex uk-flex-middle">
        <img class="uk-margin-right" width="50" height="50" alt="{{ package.title }}" v-attr="src: package.extra.image">
        <div class="uk-flex-item-1">
            <h2 class="uk-margin-remove">{{ package.title }}</h2>
            <div class="uk-text-muted">{{ package.authors[0].name }}</div>
        </div>
    </div>

    <div class="uk-alert uk-alert-danger" v-show="messages.checksum">
        {{ 'The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.' | trans }}
    </div>

    <div class="uk-alert" v-show="messages.update">
        {{ 'There is an update available for the uploaded package. Please consider installing it instead.' | trans }}
    </div>

    <p>{{ package.description }}</p>

    <ul class="uk-list">
        <li><strong>{{ 'Version' | trans }}</strong> {{ package.version }}</li>
        <li><strong>{{ 'Path:' | trans }}</strong> /{{ package.name }}</li>
        <li><strong>{{ 'License:' | trans }}</strong> {{ package.license }}</li>
        <li><strong>{{ 'Email:' | trans }}</strong> <a href="mailto:{{ package.authors[0].email }}">{{ package.authors[0].email }}</a></li>
        <li><strong>{{ 'Homepage:' | trans }}</strong> <a href="{{ package.authors[0].homepage }}" target="_blank">{{ package.authors[0].homepage }}</a></li>
    </ul>

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
