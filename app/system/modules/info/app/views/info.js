module.exports = {

    data: {
        info: window.$info
    }

};

$(function () {

    new Vue(module.exports).$mount('#info');

});
