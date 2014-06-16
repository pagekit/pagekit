({
    baseUrl: ".",
    paths: {
        "requireLib":     "../../../../vendor/assets/requirejs/require",
        "css":            "../../../../vendor/assets/requirejs/plugins/css",
        "text":           "../../../../vendor/assets/requirejs/plugins/text",
        "tmpl":           "../../../../vendor/assets/requirejs/plugins/tmpl",
        "font":           "../../../../vendor/assets/requirejs/plugins/font",
        "json":           "../../../../vendor/assets/requirejs/plugins/json",
        "goog":           "../../../../vendor/assets/requirejs/plugins/goog",
        "async":          "../../../../vendor/assets/requirejs/plugins/async",
        "propertyParser": "../../../../vendor/assets/requirejs/plugins/propertyParser",
        "domReady":       "../../../../vendor/assets/requirejs/plugins/domReady",
        "system":         "system/system",
        "jquery":         "empty:",
        "handlebars":     "empty:"
    },
    include: ["requireLib", "css", "text", "tmpl", "font", "json", "goog", "domReady", "system"],
    out: "require.min.js",
    optimize: "uglify2",
    preserveLicenseComments: false
})