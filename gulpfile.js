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
    source     = require('vinyl-source-stream'),
    buffer     = require('vinyl-buffer'),
    aliasify   = require('aliasify'),
    browserify = require('browserify'),
    partialify = require('partialify'),
    vueify     = require('vueify'),
    gulp       = require('gulp'),
    concat     = require('gulp-concat'),
    header     = require('gulp-header'),
    less       = require('gulp-less'),
    rename     = require('gulp-rename'),
    eslint     = require('gulp-eslint'),
    util       = require('gulp-util'),
    uglify     = require('gulp-uglify');

// paths of the packages for the compile-task
var pkgs = [
    { path: 'app/installer/', data: '../../composer.json' },
    { path: 'app/system/modules/theme/', data: '../../../../composer.json' },
    { path: 'themes/alpha/', data: 'theme.json' }
];

// banner for the css files
var banner = "/*! <%= data.title %> <%= data.version %> | (c) 2014 Pagekit | MIT License */\n";

gulp.task('default', ['compile', 'lint']);

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
    gulp.watch(['app/**/*.js', 'app/**/*.vue'], ['compile-js']);
});

/**
 * Lint all script files
 */
gulp.task('lint-js', function () {
    return gulp.src(['app/modules/**/*.js', 'extensions/**/*.js', 'themes/**/*.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
});

/**
 * Compile all script files
 */
gulp.task('compile-js', function(){

    var files = [
        { src: './app/modules/debug/app/app.js', dest: 'debugbar.js' },
        { src: './app/system/app/app.system.js', dest: 'system.js' },
        { src: './app/system/app/app.globalize.js', dest: 'globalize.js' },
        { src: './app/system/modules/editor/app/app.js', dest: 'editor.js' },
        { src: './app/system/modules/finder/app/components/finder.vue', dest: 'finder.js' },
        { src: './app/system/modules/package/app/components/marketplace.vue', dest: 'marketplace.js' },
        { src: './app/system/modules/package/app/components/upload.vue', dest: 'upload.js' },
        { src: './vendor/assets/vue-resource/index.js', dest: 'dist/vue-resource.js' },
        { src: './vendor/assets/vue-validator/index.js', dest: 'dist/vue-validator.js' }
    ];

    var aliases = {'aliases': {
        'cldrjs': './vendor/assets/cldrjs/dist/cldr.js',
        'cldrjs/event': './vendor/assets/cldrjs/dist/cldr/event.js',
        'cldrjs/supplemental': './vendor/assets/cldrjs/dist/cldr/supplemental.js',
        'globalize': './vendor/assets/globalize/dist/globalize.js',
        'globalize/number': './vendor/assets/globalize/dist/globalize/number.js',
        'globalize/date': './vendor/assets/globalize/dist/globalize/date.js'
    }};

    return merge.apply(null, files.map(function (file) {
        return compile(file.src, file.dest);
    }));

    function compile(src, dest) {
        return browserify(src)
            .transform(aliasify, aliases)
            .transform(partialify)
            .transform(vueify)
            .bundle()
            .pipe(source(path.join(path.dirname(src), dest)))
            .pipe(buffer())
            .on('error', util.log)
            .pipe(uglify())
            .pipe(gulp.dest('.'));
    }

});
