var gulp = require('gulp');
var less = require('gulp-less');
var mainBowerFiles = require('main-bower-files');
var gulpFilter = require('gulp-filter');
var concat = require('gulp-concat');
var minify = require('gulp-minify-css');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

var gulpShell = require('gulp-shell');
var runSequence = require('gulp-run-sequence');
var clean = require('gulp-clean');

var path = require('path');

var ROOT_PATH = path.resolve(__dirname);

var dest = '.';
var src = '.';

var config = {
  less: {
    src: src + '/less/*.less',
    dest: dest + '/css',
    settings: {
      indentedSyntax: false, // Enable .less syntax?
      imagePath: '/images' // Used by the image-url helper
    }
  },
  // js: {
  //   src: [src + '/*.js', src + '/*.js'],
  //   dest: dest + '/'
  // },
  // images: {
  //   src: src + '/images/**/*',
  //   dest: dest + '/images'
  // },
  // fonts: {
  //   src: src + '/fonts/**/*',
  //   dest: dest + '/fonts'
  // },
  // bower: {
  //   js: dest + '/js',
  //   css: dest + '/css',
  //   images: dest + '/images',
  //   fonts: dest + '/fonts'
  // },
  watch: {
    src: src + '/**/*.*',
    less: src + '/**/*.less',
    js: src + '/*.js',
    tasks: ['build']
  }
};


gulp.task('less', function () {
  return gulp.src(config.less.src)
    .pipe(less(config.less.settings))
    .pipe(gulp.dest(config.less.dest));
});

gulp.task('js', function () {
  return gulp.src(config.js.src)
    .pipe(uglify())
    .pipe(gulp.dest(config.js.dest));
});

gulp.task('fonts', function () {
  return gulp.src(config.fonts.src)
    .pipe(gulp.dest(config.fonts.dest));
});

gulp.task('images', function () {
  return gulp.src(config.images.src)
    .pipe(gulp.dest(config.images.dest));
});

/*** Filters for bower main files **/
// var jsFilter = gulpFilter('**/*.js'),
// cssFilter = gulpFilter('**/*.css'),
// lessFilter = gulpFilter('**/*.less'),
// fontFilter = gulpFilter(['**/*.svg', '**/*.eot', '**/*.woff', '**/*.ttf']),
// imgFilter = gulpFilter(['**/*.png', '**/*.gif', '**/*.jpg']);
//
// gulp.task('bower', function () {
//   return gulp.src(mainBowerFiles())
//
//     .pipe(jsFilter)
//     .pipe(uglify())
//     .pipe(gulp.dest(config.bower.js))
//     .pipe(jsFilter.restore())
//
//     .pipe(lessFilter)
//     .pipe(less())
//     .pipe(gulp.dest(config.bower.css))
//     .pipe(rename({extname: 'css'}))
//     .pipe(lessFilter.restore())
//
//     .pipe(cssFilter)
//     .pipe(minify())
//     .pipe(gulp.dest(config.bower.css))
//     .pipe(cssFilter.restore())
//
//     .pipe(imgFilter)
//     .pipe(gulp.dest(config.bower.images))
//     .pipe(imgFilter.restore())
//
//     .pipe(fontFilter)
//     .pipe(gulp.dest(config.bower.fonts))
//     .pipe(fontFilter.restore());
// });

gulp.task('watch', function () {
  gulp.watch(config.watch.less, ['less']);
  gulp.watch(config.watch.js, ['js']);
});


/**
 * Build section
 */
gulp.task('build-clean', function () {
  // return gulp.src(dest).pipe(clean());
});

gulp.task('build', function (cb) {
  return runSequence('build-clean', ['less'], cb);
});


/**
 * Default tasks
 */
gulp.task('default', function () {
  runSequence('build', ['watch']);
});
