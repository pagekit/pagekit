function install (Vue) {

    var config = window.$pagekit;

    /**
     * Libraries
     */

    require('./lib/filters')(Vue);
    require('./lib/trans')(Vue);
    require('vue-validator');
    require('vue-resource');

    /**
     * Components
     */

    Vue.component('v-pagination', require('./components/pagination'));

    /**
     * Directives
     */

    Vue.directive('check-all', require('./directives/check-all'));
    Vue.directive('checkbox', require('./directives/checkbox'));
    Vue.directive('confirm', require('./directives/confirm'));
    Vue.directive('gravatar', require('./directives/gravatar'));
    Vue.directive('order', require('./directives/order'));

    /**
     * Resource
     */

    Vue.url.options.root = config.url;
    Vue.http.options.emulateHTTP = true;
    Vue.http.options.headers = {'X-XSRF-TOKEN': config.csrf, 'X-Requested-With': 'XMLHttpRequest'};

    Vue.url.static = function(url, params) {

        var options = url;

        if (!_.isPlainObject(options)) {
            options = {url: url, params: params};
        }

        Vue.util.extend(options, {
            root: Vue.url.options.root.replace(/\/index.php$/i, '')
        });

        return this(options);
    };

    /**
     * Partial
     */

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
