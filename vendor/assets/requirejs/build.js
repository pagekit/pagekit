({
    baseUrl: ".",
    paths: {
        "requireLib":     "require",
        "css":            "plugins/css",
        "text":           "plugins/text",
        "tmpl":           "plugins/tmpl",
        "font":           "plugins/font",
        "json":           "plugins/json",
        "goog":           "plugins/goog",
        "async":          "plugins/async",
        "propertyParser": "plugins/propertyParser",
        "domReady":       "plugins/domReady",
        "jquery":         "empty:",
        "handlebars":     "empty:",
    },
    include: ["requireLib", "css", "text", "tmpl", "font", "json", "goog", "domReady"],
    out: "require.min.js",
    optimize: "uglify2",
    preserveLicenseComments: false
})