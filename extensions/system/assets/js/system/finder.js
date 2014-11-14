define(['jquery', 'system', 'uikit!upload', 'rowselect', 'handlebars'], function($, system, uikit, RowSelect, Handlebars) {

    Handlebars.registerHelper('isImage', function(url, block) {
        return url.match(/\.(?:gif|jpe?g|png|svg)/i) ? block.fn(this) : block.inverse(this);
    });

    var Finder = function(element, options) {
        this.options  = $.extend({}, Finder.defaults, options);
        this.url      = this.options.url;
        this.path     = this.options.path;
        this.view     = this.options.view;
        this.element  = $(element);
        this.deferred = null;
    };

    $.extend(Finder.prototype, {

        _init: function() {

            if (!this.deferred) {
                this.deferred = $.Deferred();

                require(['tmpl!finder.main,finder.table,finder.thumbnail'], function(tmpl) {

                    var tmpls = {};
                    tmpls['main']      = Handlebars.compile(tmpl.get('finder.main'));
                    tmpls['table']     = Handlebars.compile(tmpl.get('finder.table'));
                    tmpls['thumbnail'] = Handlebars.compile(tmpl.get('finder.thumbnail'));

                    this.deferred.resolve(tmpls);
                }.bind(this));
            }

            if (this.deferred.state() == 'resolved') return this.deferred;

            this.deferred.done(function(tmpls) {

                this.element.html(tmpls.main());

                this.main   = this.element.find('.js-finder-files');
                this.filter = this.element.find('.js-search:first');
                this.crumbs = this.element.find('.js-breadcrumbs:first');

                var $this       = this,
                    progress    = this.element.find('.uk-progress'),
                    progressbar = progress.find('.uk-progress-bar'),
                    callbacks   = {

                        show: function() {
                            progress.removeClass('uk-hidden');
                        },
                        hide: function() {
                            progress.addClass('uk-hidden');
                        },
                        update: function(percent) {
                            progressbar.css('width', percent + '%').text(percent + '%');
                        }

                    },
                    uploadsettings = {

                        action: this.url + 'upload',
                        before: function(options) {
                            options = $.extend(options.params, { path: $this.path, root: $this.options.root });
                        },
                        loadstart: function() {
                            callbacks.update(0);
                            callbacks.show();
                        },
                        progress: function(percent) {
                            callbacks.update(Math.ceil(percent));
                        },
                        allcomplete: function(response) {

                            var data = $.parseJSON(response);

                            $this.loadPath();

                            uikit.notify(data.message, data.error ? 'danger' : 'success');

                            callbacks.update(100);
                            setTimeout(callbacks.hide, 500);
                        }

                    };

                uikit.uploadSelect(this.element.find('.uk-form-file > input'), uploadsettings);
                new RowSelect(this.element, { rows: '[data-row]' });

                this.options['messages'] = this.element.find('[data-messages]').data('messages');

                this.element
                    .on('click', '[data-cmd]', function(e) {

                        var ele = $(this), cmd = ele.data('cmd');

                        if (!ele.is(':input')) e.preventDefault();

                        if ($this.commands[cmd]) {
                            $this.commands[cmd].apply($this, [ele]);
                        }
                    })
                    .on('change', '.js-select', function() {
                        $this.showOnSelect();
                    })
                    .on('selected-rows', function() {
                        $this.showOnSelect();
                    })
                    .on('click', '[data-url]', function(e) {
                        if (!$(e.target).is('a')) {
                            $this.element.trigger('picked', [$(this).data(), $this]);
                        }
                    })
                    .on('keyup', this.filter, uikit.Utils.debounce(function() {
                        $this.applyFilter();
                    }, 50))
                    .on('drop', function(e) {

                        if (e.dataTransfer && e.dataTransfer.files) {

                            e.stopPropagation();
                            e.preventDefault();

                            uikit.Utils.xhrupload(e.dataTransfer.files, uploadsettings);
                        }

                        $this.main.removeClass('uk-dragover');

                    })
                    .on('dragenter', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                    })
                    .on('dragover', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        $this.main.addClass('uk-dragover');
                    })
                    .on('dragleave', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        $this.main.removeClass('uk-dragover');
                    });

                this.showOnSelect();

            }.bind(this));

            return this.deferred;
        },

        loadPath: function(path) {

            var $this = this;

            this.path = path || this.path;

            $.post(this.url + 'list', { path: this.path, root: this.options.root }, function(data) {

                if (data.error) {
                    uikit.notify(data.message, 'danger');
                } else {
                    $this.data = data;
                }

                $this.render();

            }, 'json').fail(function(jqXHR) {
                uikit.notify(jqXHR.status == 500 ? 'Unknown error.' : jqXHR.responseText, 'danger');
            });
        },

        switchView: function(view) {
            this.view = view ? view : (this.view == 'thumbnail' ? 'table' : 'thumbnail');
            this.render();
        },

        render: function() {

            if (!this.data) return;

            this._init().done(function(tmpls) {

                this.renderBreadcrumbs();

                this.main.html(tmpls[this.view]({ data: this.data }));

                this.element.find('.js-writable').toggle(this.data.mode == 'w');

                this.element.find('[data-view="thumbnail"],[data-view="table"]').removeClass('uk-active').filter('[data-view="' + this.view + '"]').addClass('uk-active');

                this.applyFilter();

                if (this.view == 'thumbnail') {
                    $(document).trigger('uk.domready');
                }

                this.element.data('rowselect').fetchRows();

            }.bind(this));

        },

        renderBreadcrumbs: function() {

            var path = '';
            this.crumbs.children().not(':first').remove();
            this.path.substr(1).split('/').forEach(function(part, i, parts) {
                if (!part) return;

                path += '/' + part;
                if (i == parts.length - 1) {
                    this.crumbs.append('<li class="uk-active"><span>' + part + '</span></li>');
                } else {
                    this.crumbs.append('<li><a href="" data-cmd="loadPath" data-path="' + path + '">' + part + '</a></li>');
                }
            }.bind(this));
        },

        applyFilter: function() {

            var query = this.filter.val();

            if (!query) {

                this.element.find('[data-name]').show();

            } else {

                this.element.find('[data-name]').each(function() {
                    var ele = $(this);

                    ele.toggle(ele.data('name').indexOf(query) != -1);
                });
            }

            $(document).trigger('uk-domready');
        },

        showOnSelect: function() {
            var count = this.element.find('.js-select:checked').length;
            this.element.find('.js-show-on-select').toggle(count > 0);
            this.element.find('.js-show-on-single-select').toggle(count == 1);
        },

        commands: {
            switchView: function(element) {
                this.switchView(element.data('view'));
            },

            loadPath: function(element) {
                this.loadPath(element.data('path'));
            },

            createFolder: function() {
                var name = prompt(this.options.messages.foldername, '');

                if (!name) return;

                this.executeCommand('createfolder', { name: name });
            },

            rename: function(element) {
                var oldname = element.data('name'), newname = prompt(this.options.messages.newname, oldname);

                if (!newname || !oldname) return;

                this.executeCommand('rename', { oldname: oldname, newname: newname });
            },

            renameSelected: function() {
                var element = this.element.find('.js-select:checked').first();

                if (element) this.commands.rename.apply(this, [element]);
            },

            remove: function(element) {
                var names = element.data('name');

                if (!names || !confirm(this.options.messages.confirm)) return;

                this.executeCommand('removefiles', { names: [names] });
            },

            removeSelected: function() {
                var selected = this.element.find('.js-select:checked');

                if (!selected.length || !confirm(this.options.messages.confirm)) return;

                var names = [];

                selected.each(function() {
                    names.push($(this).data('name'));
                });

                this.executeCommand('removefiles', { names: names });

                this.element.find('.js-select').prop('checked', false).trigger('change');
            },

            selectAll: function(element) {
                this.element.find('.js-select').prop('checked', element.prop('checked')).trigger('change');
                this.element.data('rowselect').handleSelected();
            }
        },

        executeCommand: function(name, params) {

            var $this = this;

            return $.getJSON(this.url + name, $.extend({ path: this.path, root: this.options.root }, params),function(data) {

                uikit.notify(data.message, data.error ? 'danger' : 'success');

                $this.loadPath();

            }).fail(function(jqXHR) {
                uikit.notify(jqXHR.status == 500 ? 'Unknown error.' : jqXHR.responseText, 'danger');
            });
        }
    });

    Finder.defaults = {
        url: system.config.finder,
        view: 'table',
        root: '/',
        path: '/',
        messages: {
            confirm: 'Are you sure?',
            newname: 'New Name',
            foldername: 'Folder Name'
        }
    };

    system.finder = function(element, options) {
        return new Finder(element, options);
    };

    return system;
});
