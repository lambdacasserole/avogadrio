// Promise polyfill.
var Promise = require('es6-promise').Promise;

// Gulp itself.
var gulp = require('gulp');

// Less and CSS stuff.
var less = require('gulp-less');
var prefix = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var coffee = require('gulp-coffee');

// Compile all the Less.
gulp.task('less', function () {
    gulp.src(['./src/less/*.less'])
        .pipe(less())
        .pipe(prefix(
            "last 1 version", "> 1%", "ie 8", "ie 7"
        ))
        .pipe(minifycss()) // Minify resulting CSS.
        .pipe(gulp.dest('./web/css'));
});

// Compile all the CoffeeScript.
gulp.task('coffee', function () {
    gulp.src(['./src/coffee/*.coffee'])
        .pipe(coffee())
        .pipe(gulp.dest('./web/js'));
});

gulp.task('watch', function() {
    // Compile Less.
    gulp.watch("./src/less/*.less", function(event) {
        gulp.run('less');
    });
    // Compile CoffeeScript.
    gulp.watch("./src/coffee/*.coffee", function(event) {
        gulp.run('coffee');
    });
});

gulp.task('default', ['less', 'coffee']);
