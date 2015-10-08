/**
 * Validator functions.
 */

exports.required = function (value, arg) {

    if (!(typeof arg == 'boolean')) {
        arg = true;
    }

    if (typeof value == 'boolean') {
        return !arg || value;
    }

    return !arg || !((value === null) || (value.length === 0));
};

exports.numeric = function (value) {
    return /^[-+]?[0-9]+$/.test(value);
};

exports.integer = function (value) {
    return /^(?:[-+]?(?:0|[1-9][0-9]*))$/.test(value);
};

exports.float = function (value) {
    return /^(?:[-+]?(?:[0-9]+))?(?:\.[0-9]*)?(?:[eE][\+\-]?(?:[0-9]+))?$/.test(value);
};

exports.alpha = function (value) {
    return /^([A-Z]+)?$/i.test(value);
};

exports.alphaNum = function (value) {
    return /^([0-9A-Z]+)?$/i.test(value);
};

exports.email = function (value) {
    return /^([a-z0-9!#$%&'*+\/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*)?$/i.test(value || 'a@a.aa');
};

exports.url = function (value) {
    return /^((ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?)?$/.test(value);
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
