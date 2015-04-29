module.exports = [

  {
    entry: {
      "app/bundle/system": __dirname + "/app/system",
      "modules/editor/app/bundle/editor": __dirname + "/modules/editor/app/editor",
      "modules/finder/app/bundle/finder": __dirname + "/modules/finder/app/components/finder.vue",
      "modules/package/app/bundle/extensions": __dirname + "/modules/package/app/extensions",
      "modules/package/app/bundle/themes": __dirname + "/modules/package/app/themes",
      "modules/package/app/bundle/marketplace": __dirname + "/modules/package/app/components/marketplace.vue",
      "modules/package/app/bundle/upload": __dirname + "/modules/package/app/components/upload.vue"
    },
    output: {
      filename: __dirname + "/[name].js"
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
    entry: {
      "globalize": __dirname + "/app/globalize"
    },
    output: {
      filename: __dirname + "/app/bundle/[name].js",
      library: "Globalize"
    },
    resolve: {
      alias: {
        "cldr$": __dirname + "/../../vendor/assets/cldrjs/dist/cldr.js",
        "cldr/event$": __dirname + "/../../vendor/assets/cldrjs/dist/cldr/event.js",
        "cldr/supplemental$": __dirname + "/../../vendor/assets/cldrjs/dist/cldr/supplemental.js",
        "globalize$": __dirname + "/../../vendor/assets/globalize/dist/globalize.js",
        "globalize/number$": __dirname + "/../../vendor/assets/globalize/dist/globalize/number.js",
        "globalize/date$": __dirname + "/../../vendor/assets/globalize/dist/globalize/date.js"
      }
    }
  }

];
