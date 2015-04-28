var externals = {
  "lodash": "_",
  "jquery": "jQuery",
  "uikit": "UIkit",
  "vue": "Vue"
};

var loaders = {loaders: [
  {test: /\.html$/, loader: "html"},
  {test: /\.vue$/, loader: "vue"}
]};

module.exports = [

// System
{
  entry: {
    "app/system/app/bundle/system": "./app/system/app/system",
    "app/system/modules/editor/app/bundle/editor": "./app/system/modules/editor/app/editor",
    "app/system/modules/finder/app/bundle/finder": "./app/system/modules/finder/app/components/finder.vue",
    "app/system/modules/package/app/bundle/extensions": "./app/system/modules/package/app/extensions",
    "app/system/modules/package/app/bundle/themes": "./app/system/modules/package/app/themes",
    "app/system/modules/package/app/bundle/marketplace": "./app/system/modules/package/app/components/marketplace.vue",
    "app/system/modules/package/app/bundle/upload": "./app/system/modules/package/app/components/upload.vue"
  },
  output: {
    filename: "./[name].js"
  },
  externals: externals,
  module: loaders
},

// Globalize
{
  entry: {
    "globalize": "./app/system/app/globalize"
  },
  output: {
    filename: "./app/system/app/bundle/[name].js",
    library: "Globalize"
  },
  resolve: {
    alias: {
      "cldr$": __dirname + "/vendor/assets/cldrjs/dist/cldr.js",
      "cldr/event$": __dirname + "/vendor/assets/cldrjs/dist/cldr/event.js",
      "cldr/supplemental$": __dirname + "/vendor/assets/cldrjs/dist/cldr/supplemental.js",
      "globalize$": __dirname + "/vendor/assets/globalize/dist/globalize.js",
      "globalize/number$": __dirname + "/vendor/assets/globalize/dist/globalize/number.js",
      "globalize/date$": __dirname + "/vendor/assets/globalize/dist/globalize/date.js"
    }
  }
},

// Debugbar
{
  entry: {
    "debugbar": "./app/modules/debug/app/debugbar",
  },
  output: {
    filename: "./app/modules/debug/app/bundle/[name].js"
  },
  externals: externals,
  module: loaders
},

// Vue Resource, Validator
{
  entry: {
    "vue-resource": "./vendor/assets/vue-resource/index",
    "vue-validator": "./vendor/assets/vue-validator/index"
  },
  output: {
    filename: "./vendor/assets/[name]/dist/[name].js"
  }
}];