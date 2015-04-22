/**
 * Validate functions.
 */

function required (value) {
    if (typeof value == 'boolean') return value;
    return !((value === null) || (value.length === 0));
}

function numeric (value) {
    return (/^\s*(\-|\+)?(\d+|(\d*(\.\d*)))\s*$/).test(value);
}

function integer (value) {
    return (/^(-?[1-9]\d*|0)$/).test(value);
}

function digits (value) {
    return (/^[\d() \.\:\-\+#]+$/).test(value);
}

function alpha (value) {
    return (/^[a-zA-Z]+$/).test(value);
}

function alphaNum (value) {
    return (/\w/).test(value);
}

function email (value) {
    return (/^[a-z0-9!#$%&'*+\/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/i).test(value);
}

function url (value) {
    return (/^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/).test(value);
}

function minLength (value, arg) {
    return value && value.length && value.length >= +arg;
}

function maxLength (value, arg) {
    return value && value.length && value.length <= +arg;
}

function length (value) {
    return value && value.length == +arg;
}

function min (value, arg) {
    return value >= +arg;
}

function max (value, arg) {
    return value <= +arg;
}

function pattern (value, arg) {
    var match = arg.match(new RegExp('^/(.*?)/([gimy]*)$'));
    var regex = new RegExp(match[1], match[2]);
    return regex.test(value);
}

module.exports = {
    required: required,
    numeric: numeric,
    integer: integer,
    digits: digits,
    alpha: alpha,
    alphaNum: alphaNum,
    email: email,
    url: url,
    minLength: minLength,
    maxLength: maxLength,
    min: min,
    max: max,
    pattern: pattern
};
