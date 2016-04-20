<template>

    <div>

        <div class="uk-grid uk-grid-medium uk-grid-match uk-grid-width-small-1-2 uk-grid-width-xlarge-1-3" data-uk-grid-margin="observe:true">
            <div v-for="pkg in packages">
                <div class="uk-panel uk-panel-box uk-overlay-hover">

                    <div class="uk-panel-teaser">
                        <div class="uk-overlay uk-display-block">
                            <div class="uk-cover-background uk-position-cover" :style="{ 'background-image': 'url(' + pkg.extra.image + ')' }"></div>
                            <canvas class="uk-responsive-width uk-display-block" width="1200" height="800"></canvas>
                            <div class="uk-overlay-panel uk-overlay-background pk-overlay-background uk-overlay-fade"></div>
                        </div>
                    </div>

                    <h2 class="uk-panel-title uk-margin-remove">{{ pkg.title }}</h2>

                    <p class="uk-text-muted uk-margin-remove">{{ pkg.author.name }}</p>
                    <a class="uk-position-cover" @click="details(pkg)"></a>

                </div>
            </div>
        </div>

        <v-pagination :page.sync="page" :pages="pages" v-show="pages > 1 || page > 0"></v-pagination>

        <div class="uk-modal" v-el:modal>
            <div class="uk-modal-dialog uk-modal-dialog-large" v-if="pkg">

                <div class="pk-modal-dialog-badge">
                    <button class="uk-button" disabled v-show="isInstalled(pkg)">{{ 'Installed' | trans }}</button>
                    <button class="uk-button uk-button-primary" @click="doInstall(pkg)" v-else>{{ 'Install' | trans }}</button>
                </div>

                <div class="uk-modal-header">
                    <h2 class="uk-margin-small-bottom">{{ pkg.title }}</h2>
                    <ul class="uk-subnav uk-subnav-line uk-margin-bottom-remove">
                        <li v-if="pkg.author.homepage"><a class="uk-link-muted" :href="pkg.author.homepage" target="_blank">{{ pkg.author.name }}</a></li>
                        <li class="uk-text-muted" v-else>{{ pkg.author.name }}</li>
                        <li class="uk-text-muted">{{ 'Version %version%' | trans {version:pkg.version} }}</li>
                        <li class="uk-text-muted" v-if="pkg.license">{{ pkg.license }}</li>
                    </ul>
                </div>

                <div class="uk-grid">
                    <div class="uk-width-medium-1-2">
                        <img width="1200" height="800" :alt="pkg.title" :src="pkg.extra.image">
                    </div>
                    <div class="uk-width-medium-1-2">

                        <div v-html="pkg.description | marked" v-if="pkg.description"></div>

                        <ul class="uk-grid uk-grid-small" data-uk-grid-margin>
                            <li v-if="pkg.demo"><a class="uk-button" :href="pkg.demo" target="_blank">{{ 'Demo' | trans }}</a></li>
                            <li v-if="pkg.support"><a class="uk-button" :href="pkg.support" target="_blank">{{ 'Support' | trans }}</a></li>
                            <li v-if="pkg.documentation"><a class="uk-button" :href="pkg.documentation" target="_blank">{{ 'Documentation' | trans }}</a></li>
                        </ul>

                    </div>
                </div>

            </div>
        </div>

        <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="packages && !packages.length">{{ 'Nothing found.' | trans }}</h3>

        <p class="uk-alert uk-alert-warning" v-show="status == 'error'">{{ 'Cannot connect to the marketplace. Please try again later.' | trans }}</p>

    </div>

</template>

<script>

    module.exports = {

        mixins: [
            require('../lib/package')
        ],

        props: {
            api: {type: String, default: ''},
            search: {type: String, default: ''},
            page: {type: Number, default: 0},
            type: {type: String, default: 'pagekit-extension'},
            installed: {
                type: Array, default: function () {
                    return [];
                }
            }
        },

        data: function () {
            return {
                pkg: null,
                packages: null,
                updates: null,
                pages: 0,
                iframe: '',
                status: ''
            };
        },

        created: function () {
            this.$options.name = this.type;
        },

        ready: function () {

            this.$watch('page', this.query, {immediate: true});

            this.queryUpdates(this.installed, function (res) {
                var data = res.data;
                this.$set('updates', data.packages.length ? data.packages : null);
            });
        },

        watch: {

            search: function () {
                if (this.page) {
                    this.page = 0;
                } else {
                    this.query();
                }
            },

            type: function () {
                if (this.page) {
                    this.page = 0;
                } else {
                    this.query();
                }
            }

        },

        methods: {

            query: function () {

                var url = this.api + '/api/package/search', options = {emulateJSON: true};

                this.$http.post(url, {q: this.search, type: this.type, page: this.page}, options).then(function (res) {
                            var data = res.data;
                            this.$set('packages', data.packages);
                            this.$set('pages', data.pages);
                        }, function () {
                            this.$set('packages', null);
                            this.$set('status', 'error');
                        });
            },

            details: function (pkg) {

                if (!this.modal) {
                    this.modal = UIkit.modal(this.$els.modal);
                }

                this.$set('pkg', pkg);
                this.$set('status', '');

                this.modal.show();
            },

            doInstall: function (pkg) {

                this.modal.hide();
                this.install(pkg, this.installed);

            },

            isInstalled: function (pkg) {
                return _.isObject(pkg) ? _.find(this.installed, 'name', pkg.name) : undefined;
            }
        },

        filters: {
            marked: marked
        }

    };

</script>
