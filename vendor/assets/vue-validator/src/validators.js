/**
 * Validate functions.
 */

exports.required = function (value) {
    if (typeof value == 'boolean') return value;
    return !((value === null) || (value.length === 0));
};

exports.numeric = function (value) {
    return (/^\s*(\-|\+)?(\d+|(\d*(\.\d*)))\s*$/).test(value);
};

exports.integer = function (value) {
    return (/^(-?[1-9]\d*|0)$/).test(value);
};

exports.digits = function (value) {
    return (/^[\d() \.\:\-\+#]+$/).test(value);
};

exports.alpha = function (value) {
    return (/^[a-zA-Z]+$/).test(value);
};

exports.alphaNum = function (value) {
    return (/\w/).test(value);
};

exports.email = function (value) {
    return (/^[a-z0-9!#$%&'*+\/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/i).test(value);
};

exports.url = function (value) {
    return (/^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/).test(value);
};

exports.minLength = function (value, arg) {
    return value && value.length && value.length >= +arg;
};

exports.maxLength = function (value, arg) {
    return value && value.length && value.length <= +arg;
};

exports.length = function (value) {
    return value && value.length == +arg;
};

exports.min = function (value, arg) {
    return value >= +arg;
};

exports.max = function (value, arg) {
    return value <= +arg;
};

exports.pattern = function (value, arg) {
    var match = arg.match(new RegExp('^/(.*?)/([gimy]*)$'));
    var regex = new RegExp(match[1], match[2]);
    return regex.test(value);
};
