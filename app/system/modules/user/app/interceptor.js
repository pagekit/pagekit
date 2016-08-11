var modal = require('./components/modal-login.vue');
var mutex;

Vue.http.interceptors.push(function () {

    var options;

    return {

        request: function (request) {
            options = _.clone(request);

            return request;
        },

        response: function (response) {

            if (response.request.crossOrigin || response.status !== 401 || options.headers['X-LOGIN']) {
               return response;
            }

            if (!mutex) {
                mutex = new Vue(modal).promise.finally(function () {
                    mutex = undefined;
                });
            }

            return mutex.then(function () {
                return Vue.http(options);
            });

        }

    };

});