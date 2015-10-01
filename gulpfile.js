var requireDir = require('require-dir');

var dir = requireDir('./node_modules/joomla-gulp', {recurse: true});
var dir = requireDir('./joomla-gulp-extensions', {recurse: true});


// Load config
var extension = require('./package.json');
var config    = require('./gulp-config.json');

var gulp    = require('gulp');
var zip     = require('gulp-zip');
var rm      = require('gulp-rimraf');
var replace = require('gulp-replace');

//release
gulp.task('release', function () {
  gulp.src('./releases', { read: false }).pipe(rm({ force: true }));

  return gulp.src([
		'!*',
		'./src/**',
	])
  // run the replacement of version name from
  .pipe(replace(/##VERSION##/g, extension.version))
  .pipe(replace(/##CREATIONDATE##/g, extension.creationDate))
  // .pipe(gulp.dest('releases/file.xml'));
	.pipe(zip(extension.name + '_' + extension.version + '.zip'))
	.pipe(gulp.dest('releases'));
});
