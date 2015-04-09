(function ($, window) {

    var defaults = {
        root    : '/',
        mode    : 'write',
        view    : 'table',
        path    : '/',
        selected: [],
        upload  : {}
    };

    var definition = {

        replace : true,
        template: '#finder-main',

        data: function() {
            return Vue.util.extend({}, defaults);
        },

        ready: function () {

            this.resource = this.$resource('system/finder/:cmd');

            this.load().success(function() {
                this.$dispatch('ready.finder', this);
            }.bind(this));
        },

        watch: {

            path: function() {
                this.load();
            },

            selected: function() {
                this.$dispatch('select.finder', this.getSelected(), this)
            }

        },

        filters: {

            searched: function (files) {
                var query = this.search;
                return query ? files.filter(function (file) {
                    return file.name.toLowerCase().indexOf(query.toLowerCase()) !== -1;
                }) : files;
            }

        },

        computed: {

            breadcrumbs: function () {

                var path = '',
                    crumbs = [{ path: '/', title: this.$trans('Home') }]
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

            getFullPath: function() {
                return (this.root+this.path).replace(/^\/+|\/+$/g, '')+'/';
            },

            getSelected: function() {
                var path = this.getFullPath();
                return this.selected.map(function(name) {
                    return path+name;
                });
            },

            toggleSelect: function(name) {

                if (name.targetVM) {
                    if (name.target.tagName == 'INPUT' || name.target.tagName == 'A')  return;
                    name = name.targetVM.$data.name;
                }

                var index  = this.selected.indexOf(name);
                -1 === index ? this.selected.push(name) : this.selected.splice(index, 1);
            },

            createFolder: function () {
                var name = prompt(this.$trans('Folder Name'), '');

                if (!name) return;

                this.command('createfolder', { name: name });
            },

            rename: function (oldname) {

                if (oldname.target) {
                    oldname = this.selected[0];
                }

                if (!oldname) return;

                var newname = prompt(this.$trans('New Name'), oldname);

                if (!newname) return;

                this.command('rename', { oldname: oldname, newname: newname });
            },

            remove: function (names) {

                if (names.target) {
                    names = this.selected;
                }

                if (!names || !confirm(this.$trans('Are you sure?'))) return;

                this.command('removefiles', { names: names });
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
                return url.match(/\.(?:gif|jpe?g|png|svg)/i);
            },

            command: function (cmd, params) {

                var self = this;

                return this.resource.save({ cmd: cmd }, $.extend({ path: this.path, root: this.root }, params), function (data) {

                    UIkit.notify(data.message, data.error ? 'danger' : 'success');

                    self.load();

                }).fail(function (jqXHR) {
                    UIkit.notify(jqXHR.status == 500 ? 'Unknown error.' : jqXHR.responseText, 'danger');
                });
            },

            load: function () {
                return this.resource.get({ path: this.path, root: this.root }, function (data) {

                    this.$set('selected', []);
                    this.$set('folders', data.folders || []);
                    this.$set('files', data.files || []);

                    this.$dispatch('path.finder', this.getFullPath(), this);

                }.bind(this));
            }

        },

        events: {

            /**
             * Init upload
             */

            'hook:ready': function() {

                var finder = this,
                    settings = {

                        action: this.$url('system/finder/upload'),

                        before: function (options) {
                            $.extend(options.params, { path: finder.path, root: finder.root });
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

                            UIkit.notify(data.message, data.error ? 'danger' : 'success');

                            finder.$set('upload.progress', 100);
                            setTimeout(function () {
                                finder.$set('upload.running', false);
                            }, 1500);
                        }

                    };

                UIkit.uploadSelect(this.$el.querySelector('.uk-form-file > input'), settings);
                UIkit.uploadDrop(this.$el, settings);
            }

        }

    };

    Vue.component('v-finder', Vue.util.extend({}, definition));

    var Finder = function(element, options) {
        return new Vue($.extend(true, {}, definition, { el: element, data: $.extend(true, {}, defaults, options)} ));
    };

    $(function () {
        $('[data-finder]').each(function () {
            new Finder(this, $(this).data('finder'));
        });
    });

    window.Finder = window.Finder || Finder;

})(jQuery, window);
