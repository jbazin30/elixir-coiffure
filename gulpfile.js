/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var gulp=       require('gulp');
var gulpsync=   require('gulp-sync')(gulp);
var extender=   require('gulp-html-extend');
var Promise=    require('es6-promise').Promise;
var del=        require('del');
var critical=   require('critical').stream;

// Include plugins
var plugins= require('gulp-load-plugins')(); // tous les plugins de package.json
var base_uri= './public_html';

// Variables de chemins
var source= base_uri + '/src'; // dossier de travail
var prod=   base_uri + '/templates'; // dossier à livrer

// Tâche "clean" = supprime la release
gulp.task('clean', function() {
  del.sync(prod + '/assets');
});

// Tâche "css" = SASS + autoprefixer + minify
gulp.task('css', function () {
    return gulp.src(source + '/assets/sass/*.scss')
        .pipe(plugins.sass())
        .pipe(plugins.csscomb())
        .pipe(plugins.cssbeautify({indent: '  '}))
        .pipe(plugins.autoprefixer())
        .pipe(plugins.rename({
            suffix: '.min'
            }))
        .pipe(gulp.dest(prod + '/assets/css'));
});

// Tâche "minify" = minification CSS (destination -> destination)
gulp.task('minify', function () {
  return gulp.src(prod + '/assets/css/*.css')
    .pipe(plugins.csso())
    .pipe(gulp.dest(prod + '/assets/css/'));
});

// Tâche "critical" = critical inline CSS
gulp.task('critical', function() {
  return  gulp.src(prod + '/*.html')
    .pipe(critical({
      base: prod,
      inline: true,
      width: 320,
      height: 480,
      minify: true,
      css: [prod + '/assets/css/main.min.css', prod + '/assets/css/ie8.min.css']
    }))
    .pipe(gulp.dest(prod));
});

// Tâche "fonts" = Copie des fonts en prod
gulp.task('fonts', function () {
    return gulp.src(source + '/assets/fonts/**/*')
        .pipe(gulp.dest(prod + '/assets/fonts'));
});

// Tâche "js" = uglify + concat
gulp.task('js', function() {
    return gulp.src(source + '/assets/js/**/*.js')
        .pipe(plugins.uglify())
        .pipe(gulp.dest(prod + '/assets/js/'));
});

// Tâche "img" = Images optimisées
gulp.task('img', function () {
  return gulp.src(source + '/images/*.{png,jpg,jpeg,gif,svg}')
    .pipe(plugins.imagemin())
    .pipe(gulp.dest(prod + '/images'));
});

// Tâche "html" = includes HTML
gulp.task('include_html', function() {
    return  gulp.src(source + '/*.html')
        // Generates HTML includes
        .pipe(extender({
          annotations: false,
          verbose: false
        })) // default options
        .pipe(gulp.dest(prod));
});

gulp.task('assets', gulpsync.sync(['css', 'minify', 'js', 'img', 'fonts']));
gulp.task('html', gulpsync.sync(['include_html', 'critical']));

// Tâche "prod" = toutes les tâches ensemble
gulp.task('prod', gulpsync.sync(['clean', 'assets' /*, 'html'*/]));