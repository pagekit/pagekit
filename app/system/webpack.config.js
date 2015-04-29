var assets = __dirname + "/../../vendor/assets";

module.exports = [

  {
    context: __dirname,
    entry: {
      "app/bundle/system": "./app/system",
      "modules/editor/app/bundle/editor": "./modules/editor/app/editor",
      "modules/finder/app/bundle/finder": "./modules/finder/app/components/finder.vue",
      "modules/package/app/bundle/extensions": "./modules/package/app/extensions",
      "modules/package/app/bundle/themes": "./modules/package/app/themes",
      "modules/package/app/bundle/marketplace": "./modules/package/app/components/marketplace.vue",
      "modules/package/app/bundle/upload": "./modules/package/app/components/upload.vue"
    },
    output: {
      path: __dirname,
      filename: "./[name].js"
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
  },

  {
    context: __dirname,
    entry: {
      "globalize": "./app/globalize"
    },
    output: {
      path: __dirname,
      filename: "./app/bundle/[name].js",
      library: "Globalize"
    },
    resolve: {
      alias: {
        "cldr$": assets + "/cldrjs/dist/cldr.js",
        "cldr/event$": assets + "/cldrjs/dist/cldr/event.js",
        "cldr/supplemental$": assets + "/cldrjs/dist/cldr/supplemental.js",
        "globalize$": assets + "/globalize/dist/globalize.js",
        "globalize/number$": assets + "/globalize/dist/globalize/number.js",
        "globalize/date$": assets + "/globalize/dist/globalize/date.js"
      }
    }
  }

];
