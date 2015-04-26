/**
 * Popular Tasks
 * -------------
 *
 * compile: compiles the .less files of the specified packages
 * lint: runs jshint on all .js files
 */

var fs         = require('fs'),
    path       = require('path'),
    merge      = require('merge-stream'),
    gulp       = require('gulp'),
    header     = require('gulp-header'),
    less       = require('gulp-less'),
    rename     = require('gulp-rename'),
    eslint     = require('gulp-eslint');

// paths of the packages for the compile-task
var pkgs = [
    { path: 'app/installer/', data: '../../composer.json' },
    { path: 'app/system/modules/theme/', data: '../../../../composer.json' },
    { path: 'themes/alpha/', data: 'theme.json' }
];

// banner for the css files
var banner = "/*! <%= data.title %> <%= data.version %> | (c) 2014 Pagekit | MIT License */\n";

gulp.task('default', ['compile']);

/**
 * Compile all less files
 */
gulp.task('compile', function () {

    return merge.apply(null, pkgs.map(function (pkg) {
        return gulp.src(pkg.path + '**/less/*.less', {base: pkg.path})
            .pipe(less({ compress: true, relativeUrls: true }))
            .pipe(header(banner, { data: require('./' + pkg.path + pkg.data) }))
            .pipe(rename(function (file) {
                // the compiled less file should be stored in the css/ folder instead of the less/ folder
                file.dirname = file.dirname.replace('less', 'css');
            }))
            .pipe(gulp.dest(pkg.path));
    }));

});

/**
 * Watch for changes in files
 */
gulp.task('watch', function () {
    gulp.watch('**/*.less', ['compile']);
});

/**
 * Lint all script files
 */
gulp.task('lint', function () {
    return gulp.src(['app/modules/**/*.js', 'extensions/**/*.js', 'themes/**/*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
});
