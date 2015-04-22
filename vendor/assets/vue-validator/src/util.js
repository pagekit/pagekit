var _ = Vue.util.extend({}, Vue.util);

/**
 * Utility functions.
 */

_.attr = function (el, attr) {
    return el ? el.getAttribute(attr) : null;
};

_.trigger = function(el, event) {
    var e = document.createEvent('HTMLEvents');
    e.initEvent(event, true, false);
    el.dispatchEvent(e);
};

module.exports = _;