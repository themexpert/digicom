var gulp = require('gulp');
var config = require('../../gulp-config.json');

// Dependencies
var browserSync = require('browser-sync');
var rm          = require('gulp-rimraf');
var gutil 			= require('gulp-util');
var baseTask  = 'components.com_digicom';
var extPath   = './src';

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':admin',
		'clean:' + baseTask + ':site',
		'clean:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('clean:' +  baseTask + ':admin', function() {
	// gutil.log('Lets start cleaning content plugin');
	return gulp.src(config.wwwDir + '/administrator/components/com_digicom/', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':site', function() {
	return gulp.src(config.wwwDir + '/components/com_digicom', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':language', function() {
	return gulp.src(
    [
      config.wwwDir + '/administrator/language/en-GB/en-GB.com_digicom.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.com_digicom.sys.ini',
      config.wwwDir + '/language/en-GB/en-GB.com_digicom.ini'
    ],
    { read: false }
  ).pipe(rm({ force: true }));
});


// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':admin',
		'copy:' + baseTask + ':site',
		'copy:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('copy:' +  baseTask + ':admin', ['clean:' + baseTask + ':admin'], function() {
	return gulp.src(extPath + '/com_digicom/admin/**').pipe(gulp.dest(config.wwwDir + '/administrator/components/com_digicom'));
});

gulp.task('copy:' +  baseTask + ':site', ['clean:' + baseTask + ':site'], function() {
	return gulp.src(extPath + '/com_digicom/site/**').pipe(gulp.dest(config.wwwDir + '/components/com_digicom'));
});

gulp.task('copy:' +  baseTask + ':language', ['clean:' + baseTask + ':language'], function() {
	gulp.src(extPath + '/com_digicom/admin/language/en-GB/**').pipe(gulp.dest(config.wwwDir + '/administrator/language/en-GB'));
	return gulp.src(extPath + '/com_digicom/site/language/en-GB/**').pipe(gulp.dest(config.wwwDir + '/language/en-GB'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':admin',
		'watch:' + baseTask + ':site',
		'watch:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('watch:' +  baseTask + ':admin', function() {
	gulp.watch(extPath + '/com_digicom/admin/**', ['copy:' + baseTask + ':admin', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':site', function() {
	gulp.watch(extPath + '/com_digicom/site/**', ['copy:' + baseTask + ':site', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':language', function() {
	gulp.watch([
		extPath + '/com_digicom/admin/language/en-GB/**',
    extPath + '/com_digicom/site/language/en-GB/**',
	],
		['copy:' + baseTask + ':language', browserSync.reload]
	);
});
