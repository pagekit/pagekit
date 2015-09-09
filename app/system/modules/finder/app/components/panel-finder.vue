<template>

    <div class="uk-form" data-uk-observe v-show="items">

        <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
            <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

                <h2 class="uk-margin-remove" v-show="!selected.length">{{ '{0} %count% Files|{1} %count% File|]1,Inf[ %count% Files' | transChoice count {count:count} }}</h2>
                <h2 class="uk-margin-remove" v-show="selected.length">{{ '{1} %count% File selected|]1,Inf[ %count% Files selected' | transChoice selected.length {count:selected.length} }}</h2>

                <div class="uk-margin-left" v-if="isWritable" v-show="selected.length">
                    <ul class="uk-subnav pk-subnav-icon">
                        <li v-show="selected.length === 1"><a class="pk-icon-edit pk-icon-hover" title="{{ 'Rename' | trans 'domain' 'asdf' 'asdf2' }}" data-uk-tooltip="{delay: 500}" v-on="click: rename"></a></li>
                        <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove" v-confirm="'Delete files?'"></a></li>
                    </ul>
                </div>

                <div class="pk-search">
                    <div class="uk-search">
                        <input class="uk-search-field" type="text" v-model="search">
                    </div>
                </div>

            </div>
            <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

                <div class="uk-margin-right">
                    <ul class="uk-subnav pk-subnav-icon">
                        <li v-class="'uk-active': view == 'table'">
                            <a class="pk-icon-table pk-icon-hover" title="{{ 'Table View' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: view = 'table'"></a>
                        </li>
                        <li v-class="'uk-active': view == 'thumbnail'">
                            <a class="pk-icon-thumbnails pk-icon-hover" title="{{ 'Thumbnails View' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: view = 'thumbnail'"></a>
                        </li>
                    </ul>
                </div>

                <div>
                    <button class="uk-button uk-margin-small-right" v-on="click: createFolder()">{{ 'Add Folder' | trans }}</button>
                    <span class="uk-button uk-form-file" v-class="uk-button-primary: !modal">
                        {{ 'Upload' | trans }}
                        <input type="file" name="files[]" multiple="multiple">
                    </span>
                </div>

            </div>
        </div>

        <ul class="uk-breadcrumb uk-margin-large-top">
            <li v-repeat="breadcrumbs" v-class="'uk-active': current">
                <span v-show="current">{{ title }}</span>
                <a v-show="!current" v-on="click: setPath(path)">{{ title }}</a>
            </li>
        </ul>

        <div class="uk-progress uk-progress-mini uk-margin-remove" v-show="upload.running">
            <div class="uk-progress-bar" v-style="width: upload.progress + '%'"></div>
        </div>

        <div class="uk-overflow-container tm-overflow-container">
            <partial name="{{ view }}"></partial>
            <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="!hasItems">{{ 'No files found.' | trans }}</h3>
        </div>

    </div>

</template>

<script>

    module.exports = {

        replace : true,

        props: ['root', 'path', 'mode', 'view', 'modal'],

        data: function () {
            return {
                root: '/',
                path: '/',
                mode: 'write',
                view: 'table',
                upload: {},
                selected: [],
                items: false
            };
        },

        ready: function () {

            this.resource = this.$resource('system/finder/:cmd');

            this.load().success(function () {
                this.$dispatch('ready.finder', this);
            });

            UIkit.init(this.$el);
        },

        watch: {

            path: function () {
                this.load();
            },

            selected: function () {
                this.$dispatch('select.finder', this.getSelected(), this)
            }

        },

        filters: {

            searched: function (files) {
                return files.filter(function (file) {
                    return !this.search || file.name.toLowerCase().indexOf(this.search.toLowerCase()) !== -1;
                }, this);
            }

        },

        computed: {

            breadcrumbs: function () {

                var path = '',
                    crumbs = [{path: '/', title: this.$trans('Home')}]
                        .concat(this.path.substr(1).split('/')
                            .filter(function (str) {
                                return str.length;
                            })
                            .map(function (part) {
                                return { path: path += '/' + part, title: part };
                            })
                    );

                crumbs[crumbs.length - 1].current = true;

                return crumbs;
            },

            hasItems: function() {
                return this.$options.filters.searched(this.items || []).length;
            },

            count: function() {
                return this.items ? this.items.length : 0;
            },

            folders: function () {
                return _.filter(this.items, 'mime', 'application/folder');
            },

            files: function () {
                return _.filter(this.items, 'mime', 'application/file');
            }

        },

        methods: {

            /**
             * API
             */

            setPath: function (path) {
                this.$set('path', path);
            },

            getPath: function () {
                return this.path;
            },

            getFullPath: function () {
                return this.getRoot()+'/'+this.path.replace(/^\/+|\/+$/g, '')+'/';
            },

            getRoot: function() {
                return this.root.replace(/^\/+|\/+$/g, '')
            },

            getSelected: function () {
                return this.selected.map(function (name) {
                    return _.find(this.items, 'name', name).url;
                }, this);
            },

            removeSelection: function() {
                this.selected = [];
            },

            toggleSelect: function (name) {

                if (name.targetVM) {
                    if (name.target.tagName == 'INPUT' || name.target.tagName == 'A') {
                        return;
                    }
                    name = name.targetVM.$data.name;
                }

                var index = this.selected.indexOf(name);

                -1 === index ? this.selected.push(name) : this.selected.splice(index, 1);
            },

            isSelected: function (name) {
                return this.selected.indexOf(name.toString()) != -1;
            },

            createFolder: function () {

                UIkit.modal.prompt(this.$trans('Folder Name'), '', function(name){

                    if (!name) return;

                    this.command('createfolder', { name: name });

                }.bind(this));
            },

            rename: function (oldname) {

                if (oldname.target) {
                    oldname = this.selected[0];
                }

                if (!oldname) return;

                UIkit.modal.prompt(this.$trans('Name'), oldname, function(newname){

                    if (!newname) return;

                    this.command('rename', { oldname: oldname, newname: newname });

                }.bind(this), {title: this.$trans('Rename')});
            },

            remove: function (names) {

                if (names.target) {
                    names = this.selected;
                }

                if (names) {
                    this.command('removefiles', { names: names });
                }
            },

            /**
             * Helper functions
             */

            encodeURI: function (url) {
                return encodeURI(url).replace(/'/g, '%27');
            },

            isWritable: function () {
                return this.mode === 'w' || this.mode === 'write';
            },

            isImage: function (url) {
                return url.match(/\.(?:gif|jpe?g|png|svg|ico)$/i);
            },

            isVideo: function (url) {
                return url.match(/\.(mpeg|ogv|mp4|webm|wmv)$/i);
            },

            command: function (cmd, params) {

                return this.resource.save({cmd: cmd}, $.extend({path: this.path, root: this.getRoot()}, params), function (data) {

                    this.load();
                    this.$notify(data.message, data.error ? 'danger' : '');

                }).error(function (data, status) {

                    this.$notify(status == 500 ? 'Unknown error.' : data, 'danger');
                });
            },

            load: function () {

                return this.resource.get({path: this.path, root: this.getRoot()}, function (data) {

                    this.$set('items', data.items || []);
                    this.$set('selected', []);
                    this.$dispatch('path.finder', this.getFullPath(), this);

                }).error(function() {

                    this.$notify('Unable to access directory.', 'danger');

                });
            }

        },

        events: {

            /**
             * Init upload
             */

            'hook:ready': function () {

                var finder = this,
                    settings = {

                        action: this.$url.route('system/finder/upload'),

                        before: function (options) {
                            $.extend(options.params, { path: finder.path, root: finder.getRoot(), _csrf: $pagekit.csrf });
                        },

                        loadstart: function () {
                            finder.$set('upload.running', true);
                            finder.$set('upload.progress', 0);
                        },

                        progress: function (percent) {
                            finder.$set('upload.progress', Math.ceil(percent));
                        },

                        allcomplete: function (response) {

                            var data = $.parseJSON(response);

                            finder.load();

                            finder.$notify(data.message, data.error ? 'danger' : '');

                            finder.$set('upload.progress', 100);

                            setTimeout(function () {
                                finder.$set('upload.running', false);
                            }, 1500);
                        }

                    };

                UIkit.uploadSelect(this.$el.querySelector('.uk-form-file > input'), settings);
                UIkit.uploadDrop($(this.$el).parents('.uk-modal').length ? this.$el: $('html'), settings);
            }

        },

        partials: {

            table: require('../templates/table.html'),
            thumbnail: require('../templates/thumbnail.html')

        }

    };

    Vue.component('panel-finder', function (resolve) {
        resolve(module.exports);
    });

</script>
