function install (Vue) {

    var config = window.$pagekit;

    Vue.config.debug = false;
    Vue.cache = Vue.prototype.$cache = require('./lib/cache')(config.url);
    Vue.session = Vue.prototype.$session = require('./lib/cache')('session',
        {

            load: function (name) {

                if (Vue.cache.get('_session') !== Vue.cache.get('_csrf')) {
                    Vue.cache.remove(name);
                }
                Vue.cache.set('_session', Vue.cache.get('_csrf'));

                return Vue.cache.get(name, {});
            },

            store: function (name, data) {
                return Vue.cache.set(name, data);
            }

        });

    /**
     * Libraries
     */

    require('vue-form');
    require('vue-intl');
    require('vue-resource');
    require('./lib/asset')(Vue);
    require('./lib/state')(Vue);
    require('./lib/resourceCache')(Vue);
    require('./lib/csrf')(Vue);
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

    /**
     * Resource
     */

    Vue.url.options.root = config.url.replace(/\/index.php$/i, '');
    Vue.http.options.root = config.url;
    Vue.http.options.emulateHTTP = true;

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

        if (document.readyState === 'complete' || document.readyState !== 'loading' && !document.documentElement.doScroll) {
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
