function install (Vue) {

    var config = window.$pagekit;

    Vue.config.debug = false;

    /**
     * Libraries
     */

    require('vue-form');
    require('vue-intl');
    require('vue-resource');
    require('./lib/asset')(Vue);
    require('./lib/notify')(Vue);
    require('./lib/trans')(Vue);
    require('./lib/filters')(Vue);

    /**
     * Components
     */

    Vue.component('v-loader', require('./components/loader.vue'));
    Vue.component('v-modal', require('./components/modal.vue'));
    Vue.component('v-pagination', require('./components/pagination'));
    Vue.component('input-filter', require('./components/input-filter.vue'));

    require('./components/input-date.vue');
    require('./components/input-image.vue');
    require('./components/input-image-meta.vue');
    require('./components/input-video.vue');

    /**
     * Directives
     */

    Vue.directive('check-all', require('./directives/check-all'));
    Vue.directive('confirm', require('./directives/confirm'));
    Vue.directive('gravatar', require('./directives/gravatar'));
    Vue.directive('order', require('./directives/order'));
    Vue.directive('lazy-background', require('./directives/lazy-background'));
    Vue.directive('stack-margin', require('./directives/stack-margin'));
    Vue.directive('var', require('./directives/var'));

    /**
     * Resource
     */

    Vue.url.options.root = config.url.replace(/\/index.php$/i, '');
    Vue.http.options.root = config.url;
    Vue.http.options.emulateHTTP = true;
    Vue.http.headers.custom = {'X-XSRF-TOKEN': config.csrf};

    Vue.url.route = function (url, params) {

        var options = url;

        if (!_.isPlainObject(options)) {
            options = {url: url, params: params};
        }

        Vue.util.extend(options, {
            root: Vue.http.options.root
        });

        return this(options);
    };

    Vue.url.current = Vue.url.parse(window.location.href);

    Vue.prototype.$session = window.sessionStorage || {};
    Vue.prototype.$cache = require('lscache');

    Vue.ready = function (fn) {

        if (Vue.util.isObject(fn)) {

            var options = fn;

            fn = function () {
                new Vue(options);
            };

        }

        var handle = function () {
            document.removeEventListener('DOMContentLoaded', handle);
            window.removeEventListener('load', handle);
            fn();
        };

        if (document.readyState === 'complete') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', handle);
            window.addEventListener('load', handle);
        }

    };
}

if (window.Vue) {
    Vue.use(install);
}

window.history.pushState = window.history.pushState || function() {};
window.history.replaceState = window.history.replaceState || function() {};
