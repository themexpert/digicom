var gulp = require('gulp');
var config = require('../../../gulp-config.json');

// Dependencies
var browserSync = require('browser-sync');
var rm          = require('gulp-rimraf');
var gutil 			= require('gulp-util');
var baseTask  = 'modules.frontend.digicom';
var extPath   = './src';

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':categories',
		'clean:' + baseTask + ':cart',
		'clean:' + baseTask + ':menu',
		'clean:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('clean:' +  baseTask + ':categories', function() {
	// gutil.log('Lets start cleaning content plugin');
	return gulp.src(config.wwwDir + '/modules/mod_digicom_categories/', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':cart', function() {
	return gulp.src(config.wwwDir + '/modules/mod_digicom_cart', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':menu', function() {
	return gulp.src(config.wwwDir + '/administrator/modules/mod_digicom_menu', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':language', function() {
	return gulp.src(
    [
      config.wwwDir + '/administrator/language/en-GB/en-GB.mod_digicom_menu.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.mod_digicom_menu.sys.ini',
      config.wwwDir + '/language/en-GB/en-GB.mod_digicom_cart.ini',
      config.wwwDir + '/language/en-GB/en-GB.mod_digicom_categories.ini'
    ],
    { read: false }
  ).pipe(rm({ force: true }));
});


// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':categories',
		'copy:' + baseTask + ':cart',
		'copy:' + baseTask + ':menu',
		'copy:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('copy:' +  baseTask + ':categories', ['clean:' + baseTask + ':categories'], function() {
	return gulp.src(extPath + '/mod_digicom_categories/**').pipe(gulp.dest(config.wwwDir + '/modules/mod_digicom_categories'));
});

gulp.task('copy:' +  baseTask + ':cart', ['clean:' + baseTask + ':cart'], function() {
	return gulp.src(extPath + '/mod_digicom_cart/**').pipe(gulp.dest(config.wwwDir + '/modules/mod_digicom_cart'));
});

gulp.task('copy:' +  baseTask + ':menu', ['clean:' + baseTask + ':menu'], function() {
	return gulp.src(extPath + '/mod_digicom_menu/**').pipe(gulp.dest(config.wwwDir + '/administrator/modules/mod_digicom_menu'));
});

gulp.task('copy:' +  baseTask + ':language', ['clean:' + baseTask + ':language'], function() {
	gulp.src([
    extPath + '/mod_digicom_categories/language/en-GB/**',
    extPath + '/mod_digicom_cart/language/en-GB/**'
  ]).pipe(gulp.dest(config.wwwDir + '/language/en-GB'));

	return gulp.src(extPath + '/mod_digicom_menu/language/en-GB/**').pipe(gulp.dest(config.wwwDir + '/administrator/language/en-GB'));

});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':categories',
		'watch:' + baseTask + ':cart',
		'watch:' + baseTask + ':menu',
		'watch:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('watch:' +  baseTask + ':categories', function() {
	gulp.watch(extPath + '/mod_digicom_categories/**', ['copy:' + baseTask + ':categories', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':cart', function() {
	gulp.watch(extPath + '/mod_digicom_cart/**', ['copy:' + baseTask + ':cart', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':menu', function() {
	gulp.watch(extPath + '/mod_digicom_menu/**', ['copy:' + baseTask + ':menu', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':language', function() {
	gulp.watch([
		extPath + '/mod_digicom_categories/language/en-GB/**',
    extPath + '/mod_digicom_cart/language/en-GB/**',
    extPath + '/mod_digicom_menu/language/en-GB/**'
	],
		['copy:' + baseTask + ':language', browserSync.reload]
	);
});
