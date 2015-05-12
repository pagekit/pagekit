require('./components/directives.js');
require('./components/filters.js');
require('./components/pagination.js');

function install (Vue) {

    var config = window.$pagekit;

    /**
     * Config
     */

    Vue.options.url.root = config.url;
    Vue.options.http.emulateHTTP = true;
    Vue.options.http.headers = {'X-XSRF-TOKEN': config.csrf, 'X-Requested-With': 'XMLHttpRequest'};

    /**
     * Methods
     */

    Vue.url.static = function(url, params) {

        var options = url;

        if (!_.isPlainObject(options)) {
            options = {url: url, params: params};
        }

        Vue.util.extend(options, {
            root: Vue.options.url.root.replace(/\/index.php$/i, '')
        });

        return this(options);
    };

    var formats = ['full', 'long', 'medium', 'short'];

    Vue.prototype.$date = function(date, format) {

        var options = format;

        if (typeof date == 'string') {
            date = new Date(date);
        }

        if (typeof options == 'string') {
            if (formats.indexOf(format) != -1) {
                options = {date: format};
            } else {
                options = {skeleton: format};
            }
        }

        return Globalize.formatDate(date, options);
    };

    Vue.prototype.$trans = Globalize.trans;
    Vue.prototype.$transChoice = Globalize.transChoice;

    var partial = Vue.directive('partial'), insert = partial.insert;

    partial.insert = function(id) {

        var partial = this.vm.$options.partials[id];

        if (undefined === id || partial) {
            return insert.call(this, id);
        }

        var frag = Vue.parsers.template.parse(id);

        if (frag) {
            this.vm.$options.partials[id] = frag;
            return insert.call(this, id);
        }
    };

}

if (window.Vue) {
    Vue.use(install);
}

/**
 * Copyright (c) 2013 Kevin van Zonneveld (http://kvz.io) and Contributors (http://phpjs.org/authors)
 */

String.prototype.parse = function (array) {

    var strArr = this.replace(/^&/, '').replace(/&$/, '').split('&'),
        sal = strArr.length,
        i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
        postLeftBracketPos, keys, keysLen,
        fixStr = function(str) {
            return decodeURIComponent(str.replace(/\+/g, '%20'));
        };

    if (!array) {
        array = {};
    }

    for (i = 0; i < sal; i++) {
        tmp = strArr[i].split('=');
        key = fixStr(tmp[0]);
        value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

        while (key.charAt(0) === ' ') {
            key = key.slice(1);
        }
        if (key.indexOf('\x00') > -1) {
            key = key.slice(0, key.indexOf('\x00'));
        }
        if (key && key.charAt(0) !== '[') {
            keys = [];
            postLeftBracketPos = 0;
            for (j = 0; j < key.length; j++) {
                if (key.charAt(j) === '[' && !postLeftBracketPos) {
                    postLeftBracketPos = j + 1;
                }
                else if (key.charAt(j) === ']') {
                    if (postLeftBracketPos) {
                        if (!keys.length) {
                            keys.push(key.slice(0, postLeftBracketPos - 1));
                        }
                        keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
                        postLeftBracketPos = 0;
                        if (key.charAt(j + 1) !== '[') {
                            break;
                        }
                    }
                }
            }
            if (!keys.length) {
                keys = [key];
            }
            for (j = 0; j < keys[0].length; j++) {
                chr = keys[0].charAt(j);
                if (chr === ' ' || chr === '.' || chr === '[') {
                    keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
                }
                if (chr === '[') {
                    break;
                }
            }

            obj = array;
            for (j = 0, keysLen = keys.length; j < keysLen; j++) {
                key = keys[j].replace(/^['"]/, '').replace(/['"]$/, '');
                lastIter = j !== keys.length - 1;
                lastObj = obj;
                if ((key !== '' && key !== ' ') || j === 0) {
                    if (obj[key] === undef) {
                        obj[key] = {};
                    }
                    obj = obj[key];
                }
                else { // To insert new dimension
                    ct = -1;
                    for (p in obj) {
                        if (obj.hasOwnProperty(p)) {
                            if (+p > ct && p.match(/^\d+$/g)) {
                                ct = +p;
                            }
                        }
                    }
                    key = ct + 1;
                }
            }
            lastObj[key] = value;
        }
    }

    return array;
};
