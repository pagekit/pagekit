/**
 * Utility functions.
 */

module.exports = function (Vue) {

    var _ = Vue.util.extend({}, Vue.util), config = Vue.config;

    _.warn = function (msg) {
        if (window.console && (!config.silent || config.debug)) {
            console.warn('[VueForm warn]: ' + msg);
        }
    };

    _.each = function (obj, iterator, context) {

        var i, key;

        if (typeof obj.length == 'number') {
            for (i = 0; i < obj.length; i++) {
                iterator.call(context || obj[i], obj[i], i);
            }
        } else if (_.isObject(obj)) {
            for (key in obj) {
                if (obj.hasOwnProperty(key)) {
                    iterator.call(context || obj[key], obj[key], key);
                }
            }
        }

        return obj;
    };

    _.pull = function (arr, value) {
        arr.splice(arr.indexOf(value), 1);
    };

    _.attr = function (el, attr) {
        return el ? el.getAttribute(attr) : null;
    };

    _.trigger = function (el, event) {

        var e = document.createEvent('HTMLEvents');

        e.initEvent(event, true, false);
        el.dispatchEvent(e);
    };

    _.isString = function (value) {
        return typeof value === 'string';
    };

    return _;
};
