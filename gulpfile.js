/**
 * Popular Tasks
 * -------------
 *
 * compile: compiles the .less files of the specified packages
 * lint: runs jshint on all .js files
 */

var fs         = require('fs'),
    merge      = require('merge-stream'),
    source     = require('vinyl-source-stream'),
    buffer     = require('vinyl-buffer'),
    browserify = require('browserify'),
    gulp       = require('gulp'),
    header     = require('gulp-header'),
    less       = require('gulp-less'),
    rename     = require('gulp-rename'),
    eslint     = require('gulp-eslint'),
    util       = require('gulp-util'),
    uglify     = require('gulp-uglify'),
    vueify     = require('vueify');

// paths of the packages for the compile-task
var pkgs = [
    { path: 'app/installer/', data: '../../composer.json' },
    { path: 'app/system/modules/theme/', data: '../../../../composer.json' },
    { path: 'themes/alpha/', data: 'theme.json' }
];

// banner for the css files
var banner = "/*! <%= data.title %> <%= data.version %> | (c) 2014 Pagekit | MIT License */\n";

/**
 * Default gulp task
 */
gulp.task('default', ['compile', 'lint']);

/**
 * Compile all main .less files of the packages and banner them
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
 * Watch for changes in all .less files
 */
gulp.task('watch', function () {
    gulp.watch('**/*.less', ['compile']);
});

/**
 * Runs eshint
 */
gulp.task('lint', function () {
    return gulp.src(['app/modules/**/*.js', 'extensions/**/*.js', 'themes/**/*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
});

/**
 * Runs browserify
 */
gulp.task('browserify', function () {

  var b = browserify({
    entries: './app/modules/debug/assets/app/debugbar.js',
    debug: true,
    transform: [vueify]
  });

  return b.bundle()
    .pipe(source('debugbar.min.js'))
    .pipe(buffer())
    .pipe(uglify())
    .on('error', util.log)
    .pipe(gulp.dest('./app/modules/debug/assets/app/'));
});
