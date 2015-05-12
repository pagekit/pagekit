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
