function install (Vue) {

    var config = window.$pagekit;

    /**
     * Libraries
     */

    require('vue-resource');
    require('vue-validator');
    require('./lib/trans')(Vue);
    require('./lib/filters')(Vue);

    /**
     * Components
     */

    Vue.component('v-pagination', require('./components/pagination'));
    Vue.component('v-loader', require('./components/loader.vue'));

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
    Vue.http.options.headers = {'X-XSRF-TOKEN': config.csrf};

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

}

if (window.Vue) {
    Vue.use(install);
}
