module.exports = [{
  entry: {
    "app/modules/debug/app/debugbar": "./app/modules/debug/app/app",
    "app/system/app/system": "./app/system/app/app.system",
    "app/system/app/globalize": "./app/system/app/app.globalize",
    "app/system/modules/editor/app/editor": "./app/system/modules/editor/app/app",
    "app/system/modules/finder/app/components/finder": "./app/system/modules/finder/app/components/finder.vue",
    "app/system/modules/package/app/components/marketplace": "./app/system/modules/package/app/components/marketplace.vue",
    "app/system/modules/package/app/components/upload": "./app/system/modules/package/app/components/upload.vue"
  },
  output: {
    filename: "./[name].js"
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
  },
  externals: {
      "lodash": "_",
      "jquery": "jQuery",
      "uikit": "UIkit",
      "vue": "Vue"
  },
  module: {
    loaders: [
      { test: /\.html$/, loader: "html" },
      { test: /\.vue$/, loader: "vue" }
    ]
  }
}, {
  entry: {
    "vue-resource": "./vendor/assets/vue-resource/index",
    "vue-validator": "./vendor/assets/vue-validator/index"
  },
  output: {
    filename: "./vendor/assets/[name]/dist/[name].js"
  }
}];