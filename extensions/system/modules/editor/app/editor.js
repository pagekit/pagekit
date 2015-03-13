jQuery(function($) {

    $(document).on('htmleditor-save', function(e, editor) {
        if (editor.element[0].form) {
            $(editor.element[0].form).submit();
        }
    });

    $('textarea[data-editor]').each(function() {

        var options = $(this).data();

        options.markdown = ('markdown' in options) && (options.markdown === '' || options.markdown);
        UIkit.htmleditor(this, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, options));
    });

});


/**
 * Link plugin
 */

(function($) {

    return; // todo

    var modal  = $(templates['link.modal']).appendTo('body'),
        picker = UIkit.modal(modal),
        title  = modal.find('.js-title'),
        link, handler;

    modal.on('click', '.js-update', function() {
        handler();
    });

    function openLinkModal(data) {
        handler = data.handler;

        title.val(data.txt);
        picker.show();
        setTimeout(function() { title.focus(); }, 10);

        link = system.link(modal.find('.js-linkpicker'), { value: data.link });
    }

    UIkit.plugin('htmleditor', 'link', {

        init: function(editor) {

            var links = [];

            editor.element.on('render', function() {

                var regexp = editor.getMode() != 'gfm' ? /<a(?:.+?)>(?:[^<]*)<\/a>/gi : /<a(?:.+?)>(?:[^<]*)<\/a>|(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?/gi;

                links = editor.replaceInPreview(regexp, function(data) {

                    if (data.matches[0][0] == '<') {

                        var anchor = $(data.matches[0]);

                        data['link']    = anchor.attr('href');
                        data['txt']     = anchor.html();
                        data['class']   = anchor.attr('class') || '';
                        data['handler'] = function() {
                            picker.hide();

                            anchor.attr('href', link.get());
                            anchor.html(title.val());

                            data.replace(anchor[0].outerHTML);
                        };

                    } else {

                        if (data.matches[data.matches.length - 1][data.matches[data.matches.length - 2] - 1] == '!') return false;

                        data['link']    = data.matches[2];
                        data['txt']     = data.matches[1];
                        data['class']   = '';
                        data['handler'] = function() {
                            picker.hide();

                            data.replace('[' + title.val() + '](' + link.get() + ')');
                        };
                    }

                    return Handlebars.compile(templates['link.replace'])({ link: data['link'], txt: data['txt'], class: data['class']  }).replace(/(\r\n|\n|\r)/gm, '');
                });
            });

            editor.preview.on('click', '.js-editor-link', function(e) {
                e.preventDefault();
                openLinkModal(links[editor.preview.find('.js-editor-link').index(this)]);
            });

            editor.element.off('action.link');
            editor.element.on('action.link', function() {

                var cursor = editor.editor.getCursor(), data;

                links.every(function(link) {
                    if (link.inRange(cursor)) {
                        data = link;
                        return false;
                    }
                    return true;
                });

                if (!data) {

                    data = {
                        txt: editor.editor.getSelection(),
                        link: 'http://',
                        'class': '',
                        handler: function() {

                            var repl;

                            picker.hide();

                            if (editor.getCursorMode() == 'html') {
                                repl = '<a href="' + link.get() + '">' + title.val() + '</a>';
                            } else {
                                repl = '[' + title.val() + '](' + link.get() + ')';
                            }

                            editor.editor.replaceSelection(repl, 'end');
                        },
                        replace: function(value) { editor.editor.replaceRange(value, cursor); }
                    };
                }

                openLinkModal(data);
            });

            return editor;
        }
    });

})(jQuery);


/**
 * Image plugin
 */

(function($) {

    var ImageVm = {

        el: '#editor-image',

        data: {
            view: 'settings',
            style: '',
            image: {src: '', alt: ''},
            finder: {root: '', select: ''}
        },

        ready: function () {

            var vm = this;

            this.$on('select.finder', function(selected) {
                if (selected.length == 1 && selected[0].match(/\.(png|jpg|jpeg|gif|svg)$/i)) {
                    vm.finder.select = selected[0];
                } else {
                    vm.finder.select = '';
                }
            });

            this.$watch('image.src', this.preview);
            this.preview();
        },

        methods: {

            update: function () {

                var img = this.image;

                img.replace(img.tag.template({src: img.src, alt: img.alt}));
            },

            preview: function () {

                var vm = this, img = new Image(), src = '';

                if (this.image.src) {
                    src = this.$url(this.image.src, true);
                }

                img.onerror = function() {
                    vm.style = '';
                };

                img.onload  = function() {
                    vm.style = 'background-image: url("' + src + '"); background-size: contain';
                };

                img.src = src;
            },

            openFinder: function () {
                this.view = 'finder';
                this.finder.select = '';
            },

            closeFinder: function (select) {
                this.view = 'settings';
                if (select) this.image.src = select;
            }

        }

    };

    UIkit.plugin('htmleditor', 'image', {

        init: function(editor) {

            var self = this;

            this.editor = editor;
            this.images = [];

            editor.element.off('action.image');
            editor.element.on('action.image', function() {

                var cursor = editor.editor.getCursor(), image;

                self.images.every(function(img) {

                    if (img.inRange(cursor)) {
                        image = img;
                        return false;
                    }

                    return true;
                });

                self.openModal(image);
            });

            editor.element.on('render', function() {
                var regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?)/gi;
                self.images = editor.replaceInPreview(regexp, self.replaceInPreview);
            });

            editor.preview.on('click', '.js-editor-image .js-config', function() {
                var index = editor.preview.find('.js-editor-image .js-config').index(this);
                self.openModal(self.images[index]);
            });

            editor.preview.on('click', '.js-editor-image .js-remove', function() {
                var index = editor.preview.find('.js-editor-image .js-remove').index(this);
                self.images[index].replace('');
            });

            return editor;
        },

        openModal: function(image) {

            var editor = this.editor, cursor = editor.editor.getCursor(), vm = $.extend(true, {}, ImageVm), modal;
            var options = editor.element.data('finder-options'), root = options.root.replace(/^\/+|\/+$/g, '')+'/';

            if (!image) {
                image = {
                    tag: editor.getCursorMode() == 'html' ? '<img src="${src}" alt="${alt}">' : '![${alt}](${src})',
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            modal = $(templates['image.modal']).appendTo('body');
            modal.on('hide.uk.modal', function() {
                $(this).remove();
            });

            UIkit.modal(modal).show();

            $.extend(vm.data.image, image);
            vm.data.finder.root = root;
            vm = new Vue(vm);
        },

        replaceInPreview: function(data) {

            if (data.matches[0][0] == '<') {

                if (data.matches[0].match(/js\-no\-parse/)) {
                    return false;
                }

                var src = data.matches[0].match(/src="(.*?)"/), alt = data.matches[0].match(/alt="(.*?)"/);

                data.src = src ? src[1] : '';
                data.alt = alt ? alt[1] : '';
                data.tag = data.matches[0].replace(/src="(.*?)"/, 'src="${src}"').replace(/alt="(.*?)"/, 'alt="${alt}"');

            } else {
                data.src = data.matches[3].trim();
                data.alt = data.matches[2];
                data.tag = '![${alt}](${src})';
            }

            return templates['image.replace'].template({src: data.src, alt: data.alt}).replace(/(\r\n|\n|\r)/gm, '');
        }

    });

})(jQuery);


/**
 * Video plugin
 */

(function($) {

    var VideoVm = {

        el: '#editor-video',

        data: {
            view: 'settings',
            video: {src: ''},
            finder: {root: '', select: ''}
        },

        ready: function () {

            var vm = this;

            this.$on('select.finder', function(selected) {
                if (selected.length == 1 && selected[0].match(/\.(mpeg|ogv|mp4|webm|wmv)$/i)) {
                    vm.finder.select = selected[0];
                } else {
                    vm.finder.select = '';
                }
            });

        },

        methods: {

            update: function () {

                var vid = this.video;

                vid.replace('(video)' + JSON.stringify({src: vid.src}));
            },

            preview: function (url) {

                var youtubeRegExp = /(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/,
                    youtubeRegExpShort = /youtu\.be\/(.*)/,
                    vimeoRegExp = /(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/,
                    code, matches, session = sessionStorage || {};

                if (matches = url.match(youtubeRegExp)) {

                    code = '<img src="//img.youtube.com/vi/' + matches[2] + '/hqdefault.jpg" class="uk-width-1-1">';

                } else if (matches = url.match(youtubeRegExpShort)) {

                    code = '<img src="//img.youtube.com/vi/' + matches[1] + '/hqdefault.jpg" class="uk-width-1-1">';

                } else if (url.match(vimeoRegExp)) {

                    var imgid = btoa(url);

                    if (session[imgid]) {
                        code = '<img src="' + session[imgid] + '" class="uk-width-1-1">';
                    } else {
                        code = '<img data-imgid="' + imgid + '" src="" class="uk-width-1-1">';

                        $.ajax({
                            type: 'GET',
                            url: 'http://vimeo.com/api/oembed.json?url=' + encodeURI(url),
                            jsonp: 'callback',
                            dataType: 'jsonp',
                            success: function(data) {
                                session[imgid] = data.thumbnail_url;
                                $('img[data-id="' + imgid + '"]').replaceWith('<img src="' + session[imgid] + '" class="uk-width-1-1">');
                            }
                        });
                    }
                }

                return code ? code : '<video class="uk-width-1-1" src="' + url + '"></video>';
            },

            openFinder: function () {
                this.view = 'finder';
                this.finder.select = '';
            },

            closeFinder: function (select) {
                this.view = 'settings';
                if (select) this.video.src = select;
            }

        }

    };

    UIkit.plugin('htmleditor', 'video', {

        init: function(editor) {

            var self = this;

            this.editor = editor;
            this.videos = [];

            editor.addButton('video', {
                title: 'Video',
                label: '<i class="uk-icon-video-camera"></i>'
            });

            editor.element.on('action.video', function(e, editor) {

                var cursor = editor.getCursor(), video;

                self.videos.every(function(vid) {

                    if (vid.inRange(cursor)) {
                        video = vid;
                        return false;
                    }

                    return true;
                });

                self.openModal(video);
            });

            editor.options.toolbar.push('video');

            editor.element.on('render', function() {
                self.videos = editor.replaceInPreview(/\(video\)(\{.+?\})/gi, self.replaceInPreview);
            });

            editor.preview.on('click', '.js-editor-video .js-config', function() {
                var index = editor.preview.find('.js-editor-video .js-config').index(this);
                self.openModal(self.videos[index]);
            });

            editor.preview.on('click', '.js-editor-video .js-remove', function() {
                var index = editor.preview.find('.js-editor-video .js-remove').index(this);
                self.videos[index].replace('');
            });

            return editor;
        },

        openModal: function(video) {

            var editor = this.editor, cursor = editor.editor.getCursor(), vm = $.extend(true, {}, VideoVm), modal;
            var options = editor.element.data('finder-options'), root = options.root.replace(/^\/+|\/+$/g, '')+'/';

            if (!video) {
                video = {
                    src: '',
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            modal = $(templates['video.modal']).appendTo('body');
            modal.on('hide.uk.modal', function() {
                $(this).remove();
            });

            UIkit.modal(modal).show();

            $.extend(vm.data.video, video);
            vm.data.finder.root = root;
            vm = new Vue(vm);
        },

        replaceInPreview: function(data) {

            var settings;

            try {

                settings = JSON.parse(data.matches[1]);

            } catch (e) {}

            $.extend(data, settings || { src: '' });

            return templates['video.replace'].template({src: data.src, preview: VideoVm.methods.preview(data.src)}).replace(/(\r\n|\n|\r)/gm, '');
        }

    });

})(jQuery);


/**
 * URL resolver plugin
 */

UIkit.plugin('htmleditor', 'urlresolver', {

    init: function(editor) {

        editor.element.on('renderLate', function() {

            editor.replaceInPreview(/src=["'](.+?)["']/gi, function(data) {

                var replacement = data.matches[0];

                if (!data.matches[1].match(/^(\/|http:|https:|ftp:)/i)) {
                    replacement = replacement.replace(data.matches[1], System.url(data.matches[1], true));
                }

                return replacement;
            });

        });

        return editor;
    }

});
