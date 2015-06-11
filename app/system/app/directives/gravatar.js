var $ = window.jQuery;
var md5 = require('md5');

module.exports = {

    update: function (value) {

        var el = $(this.el), url = '//gravatar.com/avatar/', params = [];

        params.push('r=g');
        params.push('d=mm');
        params.push('s=' + (el.attr('height') || 50) * 2);

        el.attr('src', url + md5(value) + '?' + params.join('&'));
    }

};
