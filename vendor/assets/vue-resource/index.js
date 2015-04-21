var Url = require('./src/url');
var Http = require('./src/http');
var Resource = require('./src/resource');

/**
 * Install plugin.
 */

function install (Vue) {

    Vue.url = Url;
    Vue.http = Http;
    Vue.resource = function (url, params, actions) {
        return new Resource(url, params, actions);
    };

    Vue.prototype.$url = Vue.url;
    Vue.prototype.$http = Vue.http;
    Vue.prototype.$resource = Vue.resource;

}

if (window.Vue) {
    Vue.use(install);
}

module.exports = install;
