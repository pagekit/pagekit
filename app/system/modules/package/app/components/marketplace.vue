<template>

    <div data-uk-observe>

       <ul class="uk-grid uk-grid-match uk-grid-width-small-1-2 uk-grid-width-xlarge-1-3" data-uk-grid-margin>
            <li v-repeat="pkg: packages">
                <a class="uk-panel uk-panel-box pk-marketplace-panel uk-overlay-hover">
                    <div class="uk-panel-teaser">
                        <img width="800" height="600" alt="{{ pkg.title }}" v-attr="src: pkg.extra.teaser">
                    </div>
                    <h2 class="uk-panel-title uk-margin-remove">{{ pkg.title }}</h2>
                    <p class="uk-margin-remove uk-text-small uk-text-muted">{{ pkg.author.name }}</p>
                    <div class="uk-overlay-panel uk-overlay-background uk-flex uk-flex-center uk-flex-middle">
                        <div>
                            <button class="uk-button uk-button-primary uk-button-large" v-on="click: details(pkg)">{{ 'Details' | trans }}</button>
                        </div>
                    </div>
                </a>
            </li>
        </ul>

        <v-pagination page="{{ page }}" pages="{{ pages }}" v-show="pages > 1"></v-pagination>

        <div class="uk-modal" v-el="modal">
            <div class="uk-modal-dialog uk-modal-dialog-large pk-marketplace-modal-dialog">

                <div class="pk-marketplace-modal-action">
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

    var $ = require('jquery');
    var _ = require('lodash');

    module.exports = {

        replace: true,

        mixins: [
            require('./package')
        ],

        paramAttributes: ['api', 'search', 'type', 'installed'],

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

        ready: function () {

            var vm = this;

            this.query();
            this.queryUpdates(this.api, this.installed).success(function (data) {
                vm.$set('updates', data.packages.length ? data.packages : null);
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
                    UIkit.notify(data, 'danger');
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
