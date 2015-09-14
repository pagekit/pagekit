<template>

    <div data-uk-observe>

        <div class="uk-grid uk-grid-medium uk-grid-match uk-grid-width-small-1-2 uk-grid-width-xlarge-1-3" data-uk-grid-margin>
            <div v-repeat="pkg: packages">
                <div class="uk-panel uk-panel-box uk-overlay-hover">

                    <div class="uk-panel-teaser">
                        <div class="uk-overlay uk-display-block">
                            <div class="uk-cover-background uk-position-cover" style="background-image: url({{ pkg.extra.image }});"></div>
                            <canvas class="uk-responsive-width uk-display-block" width="800" height="550"></canvas>
                            <div class="uk-overlay-panel uk-overlay-background pk-overlay-background uk-overlay-fade"></div>
                        </div>
                    </div>

                    <h2 class="uk-panel-title uk-margin-remove">{{ pkg.title }}</h2>

                    <p class="uk-text-muted uk-margin-remove">{{ pkg.author.name }}</p>
                    <a class="uk-position-cover" v-on="click: details(pkg)"></a>

                </div>
            </div>
        </div>

        <v-pagination page="{{@ page }}" pages="{{ pages }}" v-show="pages > 1"></v-pagination>

        <div class="uk-modal" v-el="modal">
            <div class="uk-modal-dialog uk-modal-dialog-large">

                <div class="pk-modal-dialog-badge">
                    <button class="uk-button" disabled v-show="isInstalled(pkg)">{{ 'Installed' | trans }}</button>
                    <button class="uk-button uk-button-primary" v-on="click: doInstall(pkg)" v-show="!isInstalled(pkg)">
                        {{ 'Install' | trans }} <i class="uk-icon-spinner uk-icon-spin" v-show="status == 'installing'"></i>
                    </button>
                </div>

                <div class="uk-modal-header">
                    <h2 class="uk-margin-small-bottom">{{ pkg.title }}</h2>
                    <ul class="uk-subnav uk-subnav-line uk-margin-bottom-remove">
                        <li class="uk-text-muted">{{ pkg.author.name }}</li>
                        <li class="uk-text-muted">{{ 'Version %version%' | trans {version:pkg.version} }}</li>
                    </ul>
                </div>

                <div class="uk-grid">
                    <div class="uk-width-medium-1-2">
                        <img width="800" height="600" alt="{{ pkg.title }}" v-attr="src: pkg.extra.image">
                    </div>
                    <div class="uk-width-medium-1-2">
                        <div>{{ pkg.description }}</div>


                        <ul class="uk-list">
                            <li v-if="pkg.license"><strong>{{ 'License:' | trans }}</strong> {{ pkg.license }}</li>
                            <li v-if="pkg.author.homepage"><strong>{{ 'Homepage:' | trans }}</strong> <a href="{{ pkg.author.homepage }}" target="_blank">{{ pkg.author.homepage }}</a></li>
                            <li v-if="pkg.author.email"><strong>{{ 'Email:' | trans }}</strong> <a href="mailto:{{ pkg.author.email }}">{{ pkg.author.email }}</a></li>
                        </ul>

                    </div>
                </div>

            </div>
        </div>

        <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="!packages.length">{{ 'Nothing found.' | trans }}</h3>

        <p class="uk-alert uk-alert-warning" v-show="status == 'error'">{{ 'Cannot connect to the marketplace. Please try again later.' | trans }}</p>

    </div>

</template>

<script>

    module.exports = {

        mixins: [
            require('../lib/package')
        ],

        props: ['api', 'search', 'type', 'installed'],

        data: function () {
            return {
                api: '',
                search: '',
                type: 'pagekit-extension',
                pkg: null,
                packages: null,
                updates: null,
                installed: [],
                page: 0,
                pages: 0,
                iframe: '',
                status: ''
            };
        },

        created: function () {
            this.query();
            this.queryUpdates(this.installed, function (data) {
                this.$set('updates', data.packages.length ? data.packages : null);
            });
        },

        watch: {

            search: function () {
                this.query();
            },

            type: function () {
                this.query();
            },

            page: function () {
                this.query(this.page);
            }

        },

        methods: {

            query: function (page) {

                var url = this.api + '/api/package/search', options = {emulateJSON: true};

                this.$http.post(url, {q: this.search, type: this.type, page: page || 0}, function (data) {
                    this.$set('packages', data.packages);
                    this.$set('pages', data.pages);
                }, options).error(function () {
                    this.$set('packages', null);
                    this.$set('status', 'error');
                });
            },

            details: function (pkg) {

                if (!this.modal) {
                    this.modal = UIkit.modal(this.$$.modal);
                }

                this.$set('pkg', pkg);
                this.$set('status', '');

                this.modal.show();
            },

            doInstall: function (pkg) {

                this.$set('status', 'installing');
                this.modal.hide();

                this.install(pkg, this.installed)
                    .always(function () {
                        this.$set('status', '');
                    });
            },

            isInstalled: function (pkg) {
                return _.isObject(pkg) ? _.find(this.installed, 'name', pkg.name) : undefined;
            }
        }

    };

</script>
