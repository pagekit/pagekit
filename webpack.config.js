var glob = require("glob");



var exports = [
  // Vue Resource, Validator
  {
    entry : {
      "vue-resource" : "./vendor/assets/vue-resource/index",
      "vue-validator": "./vendor/assets/vue-validator/index"
    },
    output: {
      filename: "./vendor/assets/[name]/dist/[name].js"
    }
  }
];

glob.sync('{app/modules/**,app/system/**,extensions/**,themes/**}/webpack.config.js').forEach(function(config) {
  exports = exports.concat(require('./' + config));
});

module.exports = exports;
