(function($) {

    var config = $.extend({}, $pagekit), templates = {};

    window.System = {

        version: config.version,

        loadLanguage: function(locale) {
            return $.getJSON(this.url('admin/system/locale', { locale: locale }, function(data) {
                data.locale = locale;
                Locale.Translator.fromJSON(data);
            }));
        }

    };

    $(document).on('ajaxSend', function(e, xhr){
        xhr.setRequestHeader('X-XSRF-TOKEN', config.csrf);
    });

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

})(jQuery);
