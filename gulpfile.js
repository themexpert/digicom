var requireDir = require('require-dir');

var dir = requireDir('./node_modules/joomla-gulp', {recurse: true});
var dir = requireDir('./joomla-gulp-extensions', {recurse: true});


// Load config
var extension = require('./package.json');
var config    = require('./gulp-config.json');

var gulp = require('gulp');
var zip  = require('gulp-zip');
var rm   = require('gulp-rimraf');
//release
gulp.task('release', function () {
  gulp.src('./releases', { read: false }).pipe(rm({ force: true }));

  return gulp.src([
		'!*',
		'./src/**',
	])
	.pipe(zip(extension.name + '_' + extension.version + '.zip'))
	.pipe(gulp.dest('releases'));
});
