
module.exports = function(grunt) {

    "use strict";

    var fs = require('fs'), pkginfo = grunt.file.readJSON("package.json");

    grunt.initConfig({

        less: {
            theme: {
                options: { cleancss: true },
                files: { "css/theme.css": "less/theme.less" }
            }
        },

        meta: {
          banner: "/*! "+pkginfo.title+" "+pkginfo.version+" | "+pkginfo.homepage+" | (c) 2014 Pagekit | MIT License */"
        },

        usebanner: {
            dist: {
              options: {
                position: 'top',
                banner: "<%= meta.banner %>\n"
              },
              files: {
                src: [ 'css/*.css' ]
              }
            }
        },

        watch: {
            src: {
                files: ["less/**/*.less"],
                tasks: ["build"]
            }
        }
    });

    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-banner");

    grunt.registerTask("build", ["less", "usebanner"]);
    grunt.registerTask("default", ["build"]);
};