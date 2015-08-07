<template>

    <div data-uk-observe>

        <div class="uk-grid uk-grid-medium uk-grid-match uk-grid-width-small-1-2 uk-grid-width-xlarge-1-3" data-uk-grid-margin>
            <div v-repeat="pkg: packages">
                <div class="uk-panel uk-panel-box uk-overlay-hover">

                    <div class="uk-panel-teaser uk-position-relative">
                        <div class="uk-overlay uk-display-block">
                            <div class="uk-cover-background uk-position-cover" style="background-image: url({{pkg.extra.teaser}});"></div>
                            <canvas class="uk-responsive-width uk-display-block" width="800" height="600"></canvas>
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
            <div class="uk-modal-dialog uk-modal-dialog-large pk-modal-dialog-iframe">

                <div class="pk-modal-dialog-badge">
                    <button class="uk-button" disabled v-show="isInstalled(pkg)">{{ 'Installed' | trans }}</button>
                    <button class="uk-button uk-button-primary" v-on="click: install(pkg)" v-show="!isInstalled(pkg)">
                        {{ 'Install' | trans }} <i class="uk-icon-spinner uk-icon-spin" v-show="status == 'installing'"></i>
                    </button>
                </div>

                <iframe class="uk-width-1-1 uk-height-1-1" v-attr="src: iframe"></iframe>

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
                api: {},
                search: '',
                type: 'extension',
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

                var url = this.api.url + '/package/search';

                this.$http.jsonp(url, {q: this.search, type: this.type, page: page || 0}, function (data) {
                    this.$set('packages', data.packages);
                    this.$set('pages', data.pages);
                }).error(function () {
                    this.$set('packages', null);
                    this.$set('status', 'error');
                });
            },

            details: function (pkg) {

                if (!this.modal) {
                    this.modal = UIkit.modal(this.$$.modal);
                }

                this.$set('iframe', this.api.url.replace(/\/api$/, '') + '/marketplace/frame/' + pkg.name);
                this.$set('pkg', pkg);

                this.modal.show();
            },

            install: function (pkg) {

                this.$set('status', 'installing');

                this.installPackage(pkg, this.installed).error(function (data) {
                    this.$notify(data, 'danger');
                }).always(function (data) {
                    this.$set('status', '');
                });
            },

            isInstalled: function (pkg) {
                return _.isObject(pkg) ? _.find(this.installed, 'name', pkg.name) : undefined;
            }
        }

    };

</script>
