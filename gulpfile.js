'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass')(require('sass'));
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');

function cssToSass(dir) {
    return gulp.src(dir + '/themes/default/css/sass/**/*.scss')
        .pipe(sass({
            outputStyle: 'compressed',
            includePaths: ['node_modules/susy/sass']
        }).on('error', sass.logError))
        .pipe(postcss([autoprefixer()]))
        .pipe(gulp.dest(dir + '/themes/default/css'));
}

function taskCss() {
    return cssToSass('./admin');
}
taskCss.description = 'Build css for the core';
gulp.task('css', taskCss);

function taskCssWatch() {
    gulp.watch('./admin/themes/default/css/sass/**/*.scss', gulp.parallel('css'));
}
taskCssWatch.description = 'Watch for core sass changes and auto-build css';
gulp.task('css:watch', taskCssWatch);
