/**
 * Popular Tasks
 * -------------
 *
 * compile-less: compiles the less files of the specified packages
 */

var fs = require('fs'),
    merge = require('merge-stream'),
    gulp = require('gulp'),
    header = require('gulp-header'),
    less = require('gulp-less'),
    rename = require('gulp-rename');

// paths of the packages for the compile-task
var pkgs = [
    'extensions/installer/',
    'extensions/system/theme/',
    'themes/alpha/'
];

// banner for the css files
var banner = "/*! <%= data.title %> <%= data.version %> | (c) 2014 Pagekit | MIT License */\n";


/**
 * Default gulp task
 */
gulp.task('default', ['compile-less']);


/**
 * Compile all main less files of the packages and banner them
 */
gulp.task('compile-less', function() {
    var tasks = null;

    pkgs.forEach(function(path) {
        var data = {};
        // search for the json file
        if (fs.existsSync(path + 'theme.json')) {
            data = require('./' + path + 'theme.json');
        }
        else if (fs.existsSync(path + 'extension.json')) {
            data = require('./' + path + 'extension.json');
        }
        else if (fs.existsSync(path + '../extension.json')) {
            data = require('./' + path + '../extension.json');
        }

        var task = gulp.src(path + '**/less/*.less', {base: path})
            .pipe(less({compress: true}))
            .pipe(header(banner, {data: data}))
            .pipe(rename(function(file) {
                // the compiled less file should be stored in the css/ folder instead of the less/ folder
                file.dirname = file.dirname.replace('less', 'css');
            }))
            .pipe(gulp.dest(path));

        (tasks == null) ? tasks = task : tasks = merge(tasks, task);
    });

    return tasks;
});


/**
 * Watch for changes in all less files
 */
gulp.task('watch', function() {
    gulp.watch('**/*.less', ['compile-less']);
});