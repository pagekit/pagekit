var md5 = require('md5');

module.exports = function (Vue) {

    Vue.http.interceptors.unshift(function () {

            var hit, key, lifetime;

            return {
                request: function (request) {

                    if (request.cache !== undefined && /^(GET|JSONP)$/i.test(request.method)) {

                        if (_.isObject(request.cache)) {
                            lifetime = request.cache.lifetime;
                            key = '_resource.' + request.cache.key;
                        } else {
                            lifetime = request.cache;
                            key = '_resource.' + md5(JSON.stringify(request));
                        }

                        hit = Vue.cache.get(key);
                        if (hit) {
                            request.client = function () {
                                return hit;
                            };
                        }
                    }

                    return request;
                },

                response: function (response) {

                    if (key && !hit && response.ok) {
                        Vue.cache.set(key, response, lifetime);
                    }

                    return response;
                }
            };

        }
    );

};
