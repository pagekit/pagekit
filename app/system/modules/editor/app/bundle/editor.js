/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	__webpack_require__(7);
	__webpack_require__(8);
	__webpack_require__(9);
	__webpack_require__(10);
	__webpack_require__(11);

	var $ = __webpack_require__(1);
	var UIkit = __webpack_require__(2);

	$(function () {

	    $(document).on('htmleditor-save', function (e, editor) {
	        if (editor.element[0].form) {
	            $(editor.element[0].form).submit();
	        }
	    });

	    $('textarea[data-editor]').each(function () {

	        var options = $(this).data();

	        options.markdown = ('markdown' in options) && (options.markdown === '' || options.markdown);
	        UIkit.htmleditor(this, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, options));
	    });

	});


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = UIkit;

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue;

/***/ },
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Editor Image plugin.
	 */

	var $ = __webpack_require__(1);
	var Vue = __webpack_require__(3);
	var UIkit = __webpack_require__(2);

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
	                src = this.$url.static(this.image.src);
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

	        modal = $(__webpack_require__(17)).appendTo('body');
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

	        return $('#editor-image-replace').text().template({src: data.src, alt: data.alt}).replace(/(\r\n|\n|\r)/gm, '');
	    }

	});


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Editor Link plugin.
	 */

	var $ = __webpack_require__(1);
	var Vue = __webpack_require__(3);
	var UIkit = __webpack_require__(2);

	/* TODO

	var modal  = $(require('./modal.html')).appendTo('body'),
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

	*/

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Editor Video plugin.
	 */

	var $ = __webpack_require__(1);
	var Vue = __webpack_require__(3);
	var UIkit = __webpack_require__(2);

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

	        modal = $(__webpack_require__(18)).appendTo('body');
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

	        return $('#editor-video-replace').text().template({src: data.src, preview: VideoVm.methods.preview(data.src)}).replace(/(\r\n|\n|\r)/gm, '');
	    }

	});


/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * URL resolver plugin
	 */

	var UIkit = __webpack_require__(2);

	UIkit.plugin('htmleditor', 'urlresolver', {

	    init: function(editor) {

	        editor.element.on('renderLate', function() {

	            editor.replaceInPreview(/src=["'](.+?)["']/gi, function(data) {

	                var replacement = data.matches[0];

	                if (!data.matches[1].match(/^(\/|http:|https:|ftp:)/i)) {
	                    replacement = replacement.replace(data.matches[1], Vue.url.static(data.matches[1], true));
	                }

	                return replacement;
	            });

	        });

	        return editor;
	    }

	});


/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * ES6 templating (Andrea Giammarchi - WTFPL License)
	 */

	String.prototype.template = function (fn, object) {

	    var hasTransformer = typeof fn === 'function',
	        prefix = hasTransformer ? '__tpl' + (+new Date) : '',
	        stringify = JSON.stringify,
	        regex = /\$\{([\S\s]*?)\}/g,
	        evaluate = [],
	        i = 0,
	        m;

	    while (m = regex.exec(this)) {
	        evaluate.push(stringify(this.slice(i, regex.lastIndex - m[0].length)), prefix + '(' + m[1] + ')');
	        i = regex.lastIndex;
	    }

	    evaluate.push(stringify(this.slice(i)));

	    // Function is needed to opt out from possible "use strict" directive
	    return Function(prefix, 'with(this)return' + evaluate.join('+')).call(
	        hasTransformer ? object : fn, // the object to use inside the with
	        hasTransformer && fn          // the optional transformer function to use
	    );
	};


/***/ },
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = "<div id=\"editor-image\" class=\"uk-modal\">\n    <div class=\"uk-modal-dialog uk-form uk-form-stacked\" v-class=\"uk-modal-dialog-large: view == 'finder'\">\n\n        <div v-show=\"view == 'settings'\">\n            <h1 class=\"uk-h3\">{{ 'Image' | trans }}</h1>\n            <div class=\"uk-form-row\">\n                <div class=\"uk-form-controls\">\n                    <div class=\"pk-thumbnail pk-thumbnail-image\" v-attr=\"style: style\"></div>\n                    <p class=\"uk-margin-small-top\"><a v-on=\"click: openFinder\">{{ 'Select image' | trans }}</a></p>\n                </div>\n            </div>\n            <div class=\"uk-form-row\">\n                <label for=\"form-src\" class=\"uk-form-label\">{{ 'URL' | trans }}</label>\n                <div class=\"uk-form-controls\">\n                    <input id=\"form-src\" type=\"text\" class=\"uk-width-1-1\" v-model=\"image.src\">\n                </div>\n            </div>\n            <div class=\"uk-form-row\">\n                <label for=\"form-alt\" class=\"uk-form-label\">{{ 'Alt' | trans }}</label>\n                <div class=\"uk-form-controls\">\n                    <input id=\"form-alt\" type=\"text\" class=\"uk-width-1-1\" v-model=\"image.alt\">\n                </div>\n            </div>\n            <div class=\"uk-form-row uk-margin-top\">\n                <button class=\"uk-button uk-button-primary uk-modal-close\" type=\"button\" v-on=\"click: update\">{{ 'Update' | trans }}</button>\n                <button class=\"uk-button uk-modal-close\" type=\"button\">{{ 'Cancel' | trans }}</button>\n            </div>\n        </div>\n\n        <div v-if=\"view == 'finder'\">\n            <h1 class=\"uk-h3\">{{ 'Select Image' | trans }}</h1>\n            <div v-component=\"v-finder\" v-ref=\"finder\" v-with=\"root: finder.root\"></div>\n            <div class=\"uk-margin-top\">\n                <button class=\"uk-button uk-button-primary\" type=\"button\" v-attr=\"disabled: !finder.select\" v-on=\"click: closeFinder(finder.select)\">{{ 'Select' | trans }}</button>\n                <button class=\"uk-button\" type=\"button\" v-on=\"click: closeFinder(false)\">{{ 'Cancel' | trans }}</button>\n            </div>\n        </div>\n\n    </div>\n</div>";

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = "<div id=\"editor-video\" class=\"uk-modal\">\n    <div class=\"uk-modal-dialog uk-modal-dialog-large uk-form\" v-class=\"uk-modal-dialog-large: view == 'finder'\">\n\n        <div v-show=\"view == 'settings'\">\n            <h1 class=\"uk-h3\">{{ 'Video' | trans }}</h1>\n            <div class=\"uk-grid\">\n                <div class=\"uk-width-1-3 uk-text-center\">\n                    <div>{{{ preview(video.src) }}}</div>\n                </div>\n\n                <div class=\"uk-width-2-3\">\n\n                    <div class=\"uk-form-row\">\n                        <input type=\"text\" class=\"uk-width-4-5\" placeholder=\"{{ 'URL' | trans }}\" v-model=\"video.src\">\n                        <button type=\"button\" class=\"uk-button uk-float-right uk-width-1-6\" v-on=\"click: openFinder\">{{ 'Select video' | trans }}</button>\n                    </div>\n\n                </div>\n            </div>\n            <div class=\"uk-form-row uk-margin-top\">\n                <button class=\"uk-button uk-button-primary uk-modal-close\" type=\"button\" v-on=\"click: update\">{{ 'Update' | trans }}</button>\n                <button class=\"uk-button uk-modal-close\" type=\"button\">{{ 'Cancel' | trans }}</button>\n            </div>\n        </div>\n\n        <div v-if=\"view == 'finder'\">\n            <h1 class=\"uk-h3\">{{ 'Select Video' | trans }}</h1>\n            <div v-component=\"v-finder\" v-ref=\"finder\" v-with=\"root: finder.root\"></div>\n            <div class=\"uk-margin-top\">\n                <button class=\"uk-button uk-button-primary\" type=\"button\" v-attr=\"disabled: !finder.select\" v-on=\"click: closeFinder(finder.select)\">{{ 'Select' | trans }}</button>\n                <button class=\"uk-button\" type=\"button\" v-on=\"click: closeFinder(false)\">{{ 'Cancel' | trans }}</button>\n            </div>\n        </div>\n\n    </div>\n</div>";

/***/ }
/******/ ]);