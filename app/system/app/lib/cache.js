module.exports = function (Vue) {

    Vue.http.interceptors.unshift({

            request: function (request) {

                if (request.cache !== undefined && /^(GET|JSONP)$/i.test(request.method)) {

                    var hit = Vue.cache.get(request.url);

                    if (hit) {
                        delete request.cache;
                        request.client = function () {
                            return hit;
                        };
                    }
                }

                return request;
            },

            response: function (response) {

                if (response.request.cache !== undefined && response.ok) {
                    Vue.cache.set(response.request.url, response, response.request.cache);
                }

                return response;
            }

        }
    );

};
