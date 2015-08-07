module.exports = function (Vue) {

    var _ = Vue.util;
    var Promise = require('promise');
    var cache = {};

    /**
     * Asset provides a promise based assets manager.
     */
    function Asset(assets, success, error) {

        var self = this, promises = [], $url = (this.$url || Vue.url), _assets = [], promise;

        Object.keys(assets).forEach(function (type) {

            if (!Asset[type]) {
                return;
            }

            _assets = _.isArray(assets[type]) ? assets[type] : [assets[type]];

            for (var i = 0; i < _assets.length; i++) {

                if (!_assets[i]) {
                    continue;
                }

                if (!cache[_assets[i]]) {
                    cache[_assets[i]] = Asset[type]($url(_assets[i]));
                }

                promises.push(cache[_assets[i]]);
            }

        });

        promise = Promise.all(promises);

        promise.success = function (fn) {

            promise.then(function (response) {
                fn.call(self, response);
            });

            return promise;
        };

        promise.error = function (fn) {

            promise.then(undefined, function (response) {
                fn.call(self, response);
            });

            return promise;
        };

        promise.always = function (fn) {

            var cb = function (response) {
                fn.call(self, response);
            };

            promise.then(cb, cb);

            return promise;
        };

        if (success) {
            promise.success(success);
        }

        if (error) {
            promise.error(error);
        }

        return promise;
    }

    _.extend(Asset, {

        css: function (url) {

            return new Promise(function (resolve, reject) {

                var link = document.createElement('link');

                link.onload = function () {
                    resolve(url);
                };
                link.onerror = function () {
                    reject(url);
                };

                link.href = url;
                link.type = 'text/css';
                link.rel = 'stylesheet';

                document.getElementsByTagName('head')[0].appendChild(link);
            });

        },

        js: function (url) {

            return new Promise(function (resolve, reject) {

                var script = document.createElement('script');

                script.onload = function () {
                    resolve(url);
                };
                script.onerror = function () {
                    reject(url);
                };
                script.src = url;

                document.getElementsByTagName('head')[0].appendChild(script);
            });

        },

        image: function (url) {

            return new Promise(function (resolve, reject) {

                var img = document.createElement('img');

                img.onload = function () {
                    resolve(url);
                };
                img.onerror = function () {
                    reject(url);
                };

                img.src = url;
            });

        }

    });

    Object.defineProperty(Vue.prototype, '$asset', {

        get: function () {
            return _.extend(Asset.bind(this), Asset);
        }

    });

    Vue.asset = Asset;

    return Asset;

};
