var lscache = require('lscache');

module.exports = function (bucket) {

    return {

        set: function () {
            return wrap(lscache.set, arguments);
        },

        get: function () {
            return wrap(lscache.get, arguments);
        },

        remove: function (key) {
            return wrap(lscache.remove, arguments);
        },

        flush: function () {
            return wrap(lscache.flush, arguments);
        },

        flushExpired: function () {
            return wrap(lscache.flushExpired, arguments);
        }

    };

    function wrap(func, args) {

        lscache.setBucket(_.isFunction(bucket) ? bucket() : bucket);
        var ret = func.apply(null, args);
        lscache.resetBucket();

        return ret;

    }

};