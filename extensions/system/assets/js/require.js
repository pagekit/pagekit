var require = (function(win, doc) {

    var i, base, vers, script = '', config = {}, metas = doc.getElementsByTagName('meta');

    if (metas) {
        for (i = 0; i < metas.length; i += 1) {

            base = metas[i].getAttribute('data-base');
            vers = metas[i].getAttribute('data-version');

            if (base && vers) {

                if (win.location.pathname.indexOf('index.php') > 0) {
                    script = 'index.php/';
                }

                config = {
                    baseUrl: base,
                    paths: {
                        'jquery':          'vendor/assets/jquery/jquery.js?ver=2.1.0',
                        'jsonsource':      'vendor/assets/requirejs/plugins/jsonsource',
                        'codemirror':      'vendor/assets/codemirror/codemirror.js?ver=3.22',
                        'marked':          'vendor/assets/marked/marked.js?ver=0.3.1',
                        'mustache':        'vendor/assets/mustache/mustache.js?ver=0.8.1',
                        'uikit':           'vendor/assets/uikit/js/uikit.min',
                        'uikit/addons':    'vendor/assets/uikit/addons',
                        'markdownarea':    'vendor/assets/uikit/addons/markdownarea/markdownarea',
                        'ajaxupload':      'vendor/assets/ajaxupload/ajaxupload',
                        'link':            'extensions/system/assets/js/link.js?ver=' + vers,
                        'finder':          'extensions/system/assets/js/finder.js?ver=' + vers,
                        'editor.markdown': 'extensions/system/assets/js/editor.markdown?ver=' + vers,
                        'linkpicker':      'extensions/system/assets/js/linkpicker.js?ver=' + vers,
                        'local':           'extensions/system/assets/js/local.js?ver=' + vers
                    },
                    shim: {
                        'uikit': {
                            deps: ['jquery']
                        },
                        'codemirror': {
                            deps: ['css!vendor/assets/codemirror/codemirror', 'css!vendor/assets/codemirror/hint']
                        },
                        'markdownarea': {
                            deps: ['uikit', 'marked', 'codemirror']
                        },
                        'editor.markdown': {
                            deps: ['markdownarea']
                        }
                    },
                    config: {
                        'uikit': {
                            base: 'vendor/assets/uikit/'
                        },
                        'tmpl': {
                            url: script + 'system/tmpl/'
                        },
                        'jsonsource': {
                            url: script + 'system/json/'
                        },
                        'finder': {
                            url: script + 'system/finder/'
                        }
                    }
                };

                break;
            }
        }
    }

    return config;

})(window, document);
