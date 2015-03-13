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
            src: '',
            alt: '',
            img: '',
            root: '',
            select: ''
        },

        ready: function () {

            var vm = this;

            this.$on('select.finder', function(selected) {
                if (selected.length == 1 && selected[0].match(/\.(png|jpg|jpeg|gif|svg)$/i)) {
                    vm.select = selected[0];
                } else {
                    vm.select = '';
                }
            });

            this.$watch('src', this.preview);
            this.preview();
        },

        methods: {

            update: function () {

            },

            preview: function () {

                var vm = this, img = new Image(), src = '';

                if (this.src) {
                    src = this.$url(this.src, true);
                }

                img.onerror = function() {
                    vm.img = '';
                };

                img.onload  = function() {
                    vm.img = 'background-image: url("' + src + '"); background-size: contain';
                };

                img.src = src;
            },

            openFinder: function () {
                this.view = 'finder';
                this.select = '';
            },

            closeFinder: function (select) {
                this.view = 'settings';
                if (select) this.src = select;
            }

        }

    };

    UIkit.plugin('htmleditor', 'image', {

        init: function(editor) {

            var self = this, options = editor.element.data('finder-options'), root = options.root.replace(/^\/+|\/+$/g, '')+'/', images = [], regexp;

            editor.element.on('render', function() {
                regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?)/gi;
                images = editor.replaceInPreview(regexp, self.replaceInPreview);
            });

            // editor.preview.on('click', '.js-editor-image .js-config', function() {
            //     openImageModal(images[editor.preview.find('.js-editor-image .js-config').index(this)], root);
            // });

            // editor.preview.on('click', '.js-editor-image .js-remove', function() {
            //     images[editor.preview.find('.js-editor-image .js-remove').index(this)].replace('');
            // });

            editor.element.off('action.image');
            editor.element.on('action.image', function() {

                var modal = $(templates['image.modal']).appendTo('body'), cursor = editor.editor.getCursor(), vm = $.extend(true, {}, ImageVm);

                images.every(function(img) {

                    if (img.inRange(cursor)) {

                        vm.data.src  = img.src;
                        vm.data.alt  = img.alt;
                        vm.data.root = root;

                        return false;
                    }

                    return true;
                });

                UIkit.modal(modal).show();

                modal.on('hide.uk.modal', function() {
                    console.log(vm);
                    $(this).remove();
                });

                vm = new Vue(vm);

                return;

                if (!data) {
                    data = {
                        src: '',
                        alt: '',
                        handler: function() {

                            var repl;

                            ImagePopup.getPicker().hide();

                            if (editor.getCursorMode() == 'html') {
                                repl = '<img src="' + ImagePopup.image.val() + '" alt="' + ImagePopup.title.val() + '">';
                            } else {
                                repl = '![' + ImagePopup.title.val() + '](' + ImagePopup.image.val() + ')';
                            }

                            editor.editor.replaceSelection(repl, 'end');
                        },
                        replace: function(value) { editor.editor.replaceRange(value, cursor); }
                    };
                }

            });

            return editor;
        },

        replaceInPreview: function(data) {

            if (data.matches[0][0] == '<') {

                if (data.matches[0].match(/js\-no\-parse/)) return false;

                var matchesSrc = data.matches[0].match(/\ssrc="(.*?)"/),
                    matchesAlt = data.matches[0].match(/\salt="(.*?)"/);

                data['src'] = matchesSrc ? matchesSrc[1] : '';
                data['alt'] = matchesAlt ? matchesAlt[1] : '';
                data['handler'] = function() {
                    ImagePopup.getPicker().hide();

                    var src = ' src="' + ImagePopup.image.val()+'"', alt = ' alt="'+ImagePopup.title.val() + '"', output = data.matches[0];

                    output = matchesSrc ? output.replace(matchesSrc[0], src) : [output.slice(0, 4), src, output.slice(4)].join('');
                    output = matchesAlt ? output.replace(matchesAlt[0], alt) : [output.slice(0, 4), alt, output.slice(4)].join('');

                    data.replace(output);
                };

            } else {

                data['src'] = data.matches[3].trim();
                data['alt'] = data.matches[2];
                data['handler'] = function() {
                    ImagePopup.getPicker().hide();
                    data.replace('![' + ImagePopup.title.val() + '](' + ImagePopup.image.val() + ')');
                };

            }

            return '[img]';

            return Handlebars.compile(templates['image.replace'])({ src: ('http://' !== data['src'] ? data['src'] : ''), alt: data['alt']  }).replace(/(\r\n|\n|\r)/gm, '');


        }

    });

})(jQuery);


/**
 * Video plugin
 */

(function($) {

    function openVideoModal(data, rootpath) {

        VideoPopup.video.val(data.src);
        VideoPopup.updatePreview(VideoPopup.video.val());
        VideoPopup.goto('settings');
        VideoPopup.getPicker().show();

        setTimeout(function() {
            VideoPopup.video.focus();
        }, 10);

        VideoPopup.handler = function() {
            VideoPopup.getPicker().hide();

            data.replace('(video)' + JSON.stringify({ src: VideoPopup.video.val() }));
        };

        VideoPopup.finder.loadPath(data.src.trim() && data.src.indexOf(rootpath) === 0 ? data.src.replace(rootpath, '').split('/').slice(0, -1).join('/') : '');
    }

    var VideoVm = {

        el: '#editor-video',

        data: {
            url: '',
            view: 'settings'
        },

        ready: function () {

        },

        methods: {

            update: function () {

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
            },

            closeFinder: function () {
                this.url  = this.$.finder.selected[0];
                this.view = 'settings';
            }

        }

    };

    UIkit.plugin('htmleditor', 'video', {

        init: function(editor) {

            var options = editor.element.data('finder-options'), rootpath = options.root.replace(/^\/+|\/+$/g, '')+'/', videos = [];

            // videos
            editor.addButton('video', {
                title: 'Video',
                label: '<i class="uk-icon-video-camera"></i>'
            });

            editor.element.on('action.video', function(e, editor) {

                var modal = $(templates['video.modal']).appendTo('body'), vm = new Vue(VideoVm);

                modal.on('hide.uk.modal', function() {
                    console.log(vm);
                    $(this).remove();
                });

                UIkit.modal(modal).show();

                return;

                var cursor = editor.getCursor(), data;
                videos.every(function(video) {
                    if (video.inRange(cursor)) {
                        data = video;
                        return false;
                    }
                    return true;
                });

                if (!data) {
                    data = { src: '', replace: function(value) { editor.replaceRange(value, cursor); } };
                }

                openVideoModal(data, rootpath);
            });

            editor.options.toolbar.push('video');

            // editor.element.on('render', function() {

            //     videos = editor.replaceInPreview(/\(video\)(\{.+?\})/gi, function(data) {

            //         try {

            //             var settings = $.parseJSON(data.matches[1]);

            //         } catch (e) {}

            //         $.extend(data, (settings || { src: '' }));

            //         return Handlebars.compile(templates['video.replace'])({ preview: getVideoPreview(data.src), src: data.src }).replace(/(\r\n|\n|\r)/gm, '');
            //     });
            // });

            // editor.preview.on('click', '.js-editor-video .js-config', function() {
            //     openVideoModal(videos[editor.preview.find('.js-editor-video .js-config').index(this)], rootpath);
            // });

            // editor.preview.on('click', '.js-editor-video .js-remove', function() {
            //     videos[editor.preview.find('.js-editor-video .js-remove').index(this)].replace('');
            // });

            return editor;
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
