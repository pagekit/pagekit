module.exports = function (Vue) {

    Vue.http.interceptors.unshift({

        request: function (request) {

            if (!request.crossOrigin) {
                request.headers['X-XSRF-TOKEN'] = Vue.cache.get('_csrf');
            }

            return request;
        }

    });

    Vue.cache.set('_csrf', window.$pagekit.csrf);

};
