var _ = require('./src/util');
var Url = require('./src/url');
var Http = require('./src/http');
var Resource = require('./src/resource');

/**
 * Install plugin.
 */

function install (Vue) {

    Vue.url = Url;
    Vue.http = Http;
    Vue.resource = Resource;
    Vue.options.url = {};
    Vue.options.http = {};

    Object.defineProperties(Vue.prototype, {

        $url: {
            get: function () {
                return _.extend(Url.bind(this), Url);
            }
        },

        $http: {
            get: function () {
                return _.extend(Http.bind(this), Http);
            }
        },

        $resource: {
            get: function () {
                return Resource.bind(this);
            }
        }

    });

}

if (window.Vue) {
    Vue.use(install);
}

module.exports = install;
