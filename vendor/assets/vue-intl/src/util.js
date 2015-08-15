/**
 * Utility functions.
 */

module.exports = function (Vue) {

    var a = Array.prototype,
        o = Object.prototype,
        _ = Vue.util.extend({}, Vue.util);

    _.isString = function (value) {
        return typeof value === 'string';
    };

    _.isNumber = function (value) {
        return typeof value === 'number';
    };

    _.isUndefined = function (value) {
        return typeof value === 'undefined';
    };

    _.isDate = function (value) {
        return o.toString.call(value) === '[object Date]';
    };

    _.toInt = function (value) {
        return parseInt(value, 10);
    };

    _.concat = function (arr1, arr2, index) {
        return arr1.concat(a.slice.call(arr2, index));
    };

    _.uppercase = function (str) {
        return _.isString(str) ? str.toUpperCase() : str;
    };

    return _;
};
