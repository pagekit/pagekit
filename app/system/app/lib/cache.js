var storage = require('JSONStorage');

module.exports = function (bucket, adapter) {

    var db = storage.select(bucket, adapter || 'local');

    return {

        set: function (key, value, minutes) {
            if (minutes){
                return db.setex(key, minutes * 60, value);
            } else  {
                return db.set(key, value);
            }
        },

        get: function () {
            return db.get.apply(db, arguments);
        },

        remove: function (key) {
            return db.del.apply(db, arguments);
        },

        flush: function () {
            return db.flushdb.apply(db, arguments);
        }

    };

};