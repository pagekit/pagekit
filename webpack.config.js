var _ = require("lodash");
var glob = require("glob");
var path = require("path");

var exports = [
    // Vue Resource, Validator
    {
        entry: {
            "vue-resource": "./vendor/assets/vue-resource/index",
            "vue-validator": "./vendor/assets/vue-validator/index"
        },
        output: {
            filename: "./vendor/assets/[name]/dist/[name].js"
        }
    }
];

glob.sync('{app/modules/**,app/system/**,extensions/**,themes/**}/webpack.config.js').forEach(function (file) {
    var dir = path.join(__dirname, path.dirname(file));
    exports = exports.concat(require('./' + file).map(function (config) {
        return _.merge({ context: dir, output: { path: dir } }, config);
    }));
});

module.exports = exports;
