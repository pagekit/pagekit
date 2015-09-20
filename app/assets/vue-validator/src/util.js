/**
 * Utility functions.
 */

module.exports = function (Vue) {

    var _ = Vue.util.extend({}, Vue.util);

    _.vm = function (el) {

        do {

            if (el.__vue__) {
                return el.__vue__;
            }

            el = el.parentElement;

        } while (el);

    };

    _.attr = function (el, attr) {
        return el ? el.getAttribute(attr) : null;
    };

    _.trigger = function (el, event) {

        var e = document.createEvent('HTMLEvents');

        e.initEvent(event, true, false);
        el.dispatchEvent(e);
    };

    return _;
};
