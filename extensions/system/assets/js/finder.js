define(['jquery', 'module', 'require', 'tmpl!finder.main,finder.table,finder.thumbnail', 'uikit', 'ajaxupload'], function($, mod, req, tmpl, UI) {

    var Finder = function(element, options) {

        var $this = this;

        this.options  = $.extend({}, Finder.defaults, mod.config(), options);

        this.url  = this.options.url.replace(/\/$/, '');
        this.path = this.options.path;
        this.view = this.options.view;

        this.element  = $(element).html(tmpl.render('finder.main', { writable: this.options.mode == 'write' }), this.options);

        this.options['messages'] = this.element.find('[data-messages]').data('messages');

        this.main       = this.element.find('.js-finder-files');
        this.filter     = this.element.find('.js-search:first');
        this.crumbs     = this.element.find('.js-breadcrumbs:first');

        var progress    = this.element.find('.uk-progress'),
            progressbar = progress.find('.uk-progress-bar');

        this.progress = {
            show:   function() { progress.removeClass('uk-hidden'); },
            hide:   function() { progress.addClass('uk-hidden'); },
            update: function(percent) { progressbar.css('width', percent+'%').text(percent+'%'); }
        };

        var uploadsettings = {
            action: this.url + '/upload',
            before: function(options) {
                options = $.extend(options.params, $this.getParams($this.path));
            },
            loadstart: function() {
                $this.progress.update(0);
                $this.progress.show();
            },
            progress: function(percent) {
                $this.progress.update(Math.ceil(percent));
            },
            allcomplete: function(response) {

                var data = $.parseJSON(response);

                $this.loadPath();

                UI.notify(data.message, data.error ? 'danger' : 'success');

                $this.progress.update(100);
                setTimeout($this.progress.hide, 500);
            }
        };

        this.element.uploadOnDrag(uploadsettings);
        this.element.find('.js-upload').ajaxform(uploadsettings);

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
            .on('click', '[data-url]', function(e) {
                if (!$(e.target).is('a')) {
                    $this.element.trigger('picked', [$(this).data(), $this]);
                }
            })
            .on('keyup', this.filter, UI.Utils.debounce(function() {
                $this.applyFilter();
            }, 50));

        this.switchView(this.view);
        this.loadPath(this.path);
        this.showOnSelect();
    };

    $.extend(Finder.prototype, {

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
                var newname  = prompt(this.options.messages.newname, ''), oldname = element.data('name');

                if (!newname || !oldname) return;

                this.executeCommand('rename', { oldname: oldname, newname: newname });
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
            }
        },

        loadPath: function(path) {

            var $this = this, path = path || this.path, newPath = path;

            $.post(this.url + '/list', this.getParams(path), function(data) {

                $this.path = newPath;
                $this.data = data;

                // build breadcrumbs
                data.breadcrumbs = [];

                var parts = newPath.split('/');

                for (var i = 0; i < parts.length; i++) {
                    var name = parts[i], path = '';

                    if (!name) continue;

                    for (var j = 0; j <= i; j++) {
                        path += '/'+parts[j];
                    }

                    data.breadcrumbs.push({ name: name, path: path });
                }

                $this.render();

            }, 'json').fail(function (jqXHR) {

                UI.notify(jqXHR.status == 500 ? 'Unknown error.' : jqXHR.responseText, 'danger');
            });
        },

        switchView: function(view) {

            this.view = view ? view : (this.view == 'thumbnail' ? 'table':'thumbnail');

            this.element.attr('data-view', this.view);

            this.render();
        },

        render: function()
        {

            if (this.data && this.data.breadcrumbs) {

                this.crumbs.children().not(':first').remove();

                var i, path, name, max = this.data.breadcrumbs.length;

                for(i= 0;i<max; i++) {

                    path = this.data.breadcrumbs[i].path;
                    name = this.data.breadcrumbs[i].name;

                    if (i == max-1) {
                        this.crumbs.append('<li class="uk-active"><span>'+name+'</span></li>');
                    } else {
                        this.crumbs.append('<li><a href="" data-cmd="loadPath" data-path="'+path+'">'+name+'</a></li>');
                    }
                }
            }

            this.main.html(tmpl.render('finder.' + this.view, $.extend(this.data, { writable: this.options.mode == 'write' })));

            this.element.find('[data-view="thumbnail"],[data-view="table"]').removeClass('uk-active').filter('[data-view="'+this.view+'"]').addClass('uk-active');

            this.applyFilter();
            this.applyPreview();

            if (this.view == 'thumbnail') {
                $(document).trigger('uk-domready');
            }

            this.main.find('.js-show-when-empty')[this.main.find('[data-name]').length ? 'removeClass':'addClass']('uk-hidden');
        },

        applyFilter: function() {

            var query = this.filter.val();

            if (!query) {

                this.main.find('[data-name]').show();

            } else {

                this.main.find('[data-name]').each(function() {
                    var ele = $(this);

                    ele.toggle(ele.data('name').indexOf(query) != -1);
                });
            }

            $(document).trigger('uk-domready');
        },

        applyPreview: function() {

            var $this = this;

            setTimeout(function() {

                $this.main.find('[data-type="file"]').each(function() {
                    var ele = $(this);

                    if (ele.data('url').match(/\.(gif|jpg|jpeg|png|svg)/i)) {
                        if ($this.view == 'table') {
                            ele.find('.pk-finder-icon-file').removeClass('uk-icon-file-o').addClass('pk-finder-thumbnail-table').css('background-image','url("'+ele.data('url')+'")');
                        } else {
                            ele.find('.pk-finder-thumbnail-file').removeClass('pk-finder-thumbnail-file').css('background-image','url("'+ele.data('url')+'")');
                        }
                    }
                });

            }, 0);
        },

        getParams: function(path) {
            return { path: path, root: this.options.root, mode: this.options.mode, hash: this.options.hash };
        },

        executeCommand: function(name, params) {

            var $this = this;

            return $.getJSON(this.url + '/' + name, $.extend(this.getParams(this.path), params), function(data) {

                UI.notify(data.message, data.error ? 'danger' : 'success');

                $this.loadPath();

            }).fail(function (jqXHR) {
                UI.notify(jqXHR.status == 500 ? 'Unknown error.' : jqXHR.responseText, 'danger');
            });
        },

        showOnSelect: function() {
            this.element.find('.js-show-on-select').toggle(this.element.find('.js-select:checked').length > 0);
        }
    });

    Finder.defaults = {
        url : req.toUrl('system/finder'),
        view: 'table',
        mode: 'read',
        root: '/',
        path: '/',
        messages: {
            confirm: 'Are you sure?',
            newname: 'New Name',
            foldername: 'Folder Name'
        }
    };

    window.Finder = Finder;

    return Finder;

});
