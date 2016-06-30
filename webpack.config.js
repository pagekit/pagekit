var _ = require('lodash');
var glob = require('glob');
var path = require('path');
var exports = [];

glob.sync('{app/modules/**,app/installer/**,app/system/**,packages/**}/webpack.config.js', {ignore: 'packages/**/node_modules/**'}).forEach(function (file) {
    var dir = path.join(__dirname, path.dirname(file));
    exports = exports.concat(require('./' + file).map(function (config) {
        return _.merge({context: dir, output: {path: dir}}, config);
    }));
});

module.exports = exports;
