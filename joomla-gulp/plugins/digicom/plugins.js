var gulp = require('gulp');
var config = require('../../../gulp-config.json');

// Dependencies
var browserSync = require('browser-sync');
var rm          = require('gulp-rimraf');
var gutil 			= require('gulp-util');
var baseTask  = 'plugins.digicom.plugins';
var extPath   = './src';

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':content',
		'clean:' + baseTask + ':editor',
		'clean:' + baseTask + ':finder',
		'clean:' + baseTask + ':system',
		'clean:' + baseTask + ':offline',
		'clean:' + baseTask + ':paypal',
		'clean:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('clean:' +  baseTask + ':content', function() {
	// gutil.log('Lets start cleaning content plugin');
	return gulp.src(config.wwwDir + '/plugins/content/digicom/', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' +  baseTask + ':editor', function() {
	return gulp.src(config.wwwDir + '/plugins/editors-xtd/digicom', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':finder', function() {
	return gulp.src(config.wwwDir + '/plugins/finder/digicom/', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':system', function() {
	return gulp.src(config.wwwDir + '/plugins/system/digicom/', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':offline', function() {
	return gulp.src(config.wwwDir + '/plugins/digicom_pay/offline/', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':paypal', function() {
	return gulp.src(config.wwwDir + '/plugins/digicom_pay/paypal/', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' + baseTask + ':language', function() {
	return gulp.src(
    [
      config.wwwDir + '/administrator/language/en-GB/en-GB.plg_content_digicom.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.plg_digicom_pay_offline.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.plg_digicom_pay_paypal.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.plg_editors-xtd_digicom.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.plg_finder_digicom.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.plg_finder_digicom.sys.ini',
      config.wwwDir + '/administrator/language/en-GB/en-GB.plg_system_digicom.ini'
    ],
    { read: false }
  ).pipe(rm({ force: true }));
});


// Copy
gulp.task('copy:' + baseTask,
	[
    'copy:' + baseTask + ':content',
		'copy:' + baseTask + ':editor',
		'copy:' + baseTask + ':finder',
		'copy:' + baseTask + ':system',
		'copy:' + baseTask + ':offline',
		'copy:' + baseTask + ':paypal',
		'copy:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('copy:' +  baseTask + ':content', ['clean:' + baseTask + ':content'], function() {
	return gulp.src(extPath + '/plg_content_digicom/**').pipe(gulp.dest(config.wwwDir + '/plugins/content/digicom'));
});

gulp.task('copy:' +  baseTask + ':editor', ['clean:' + baseTask + ':editor'], function() {
	return gulp.src(extPath + '/plg_editors-xtd_digicom/**').pipe(gulp.dest(config.wwwDir + '/plugins/editors-xtd/digicom'));
});

gulp.task('copy:' +  baseTask + ':finder', ['clean:' + baseTask + ':finder'], function() {
	return gulp.src(extPath + '/plg_finder_digicom/**').pipe(gulp.dest(config.wwwDir + '/plugins/finder/digicom'));
});

gulp.task('copy:' +  baseTask + ':system', ['clean:' + baseTask + ':system'], function() {
	return gulp.src(extPath + '/plg_system_digicom/**').pipe(gulp.dest(config.wwwDir + '/plugins/system/digicom'));
});

gulp.task('copy:' +  baseTask + ':offline', ['clean:' + baseTask + ':offline'], function() {
	return gulp.src(extPath + '/plg_digicom_pay_offline/**').pipe(gulp.dest(config.wwwDir + '/plugins/digicom_pay/offline'));
});

gulp.task('copy:' +  baseTask + ':paypal', ['clean:' + baseTask + ':paypal'], function() {
	return gulp.src(extPath + '/plg_digicom_pay_paypal/**').pipe(gulp.dest(config.wwwDir + '/plugins/digicom_pay/paypal'));
});

gulp.task('copy:' +  baseTask + ':language', ['clean:' + baseTask + ':language'], function() {
	return gulp.src([
    extPath + '/plg_content_digicom/language/en-GB/**',
    extPath + '/plg_digicom_pay_offline/language/en-GB/**',
    extPath + '/plg_digicom_pay_paypal/language/en-GB/**',
    extPath + '/plg_editors-xtd_digicom/language/en-GB/**',
    extPath + '/plg_finder_digicom/language/en-GB/**',
    extPath + '/plg_system_digicom/language/en-GB/**',
  ]).pipe(gulp.dest(config.wwwDir + '/administrator/language/en-GB'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
    'watch:' + baseTask + ':content',
		'watch:' + baseTask + ':editor',
		'watch:' + baseTask + ':finder',
		'watch:' + baseTask + ':system',
		'watch:' + baseTask + ':offline',
		'watch:' + baseTask + ':paypal',
		'watch:' + baseTask + ':language'
	],
	function() {
		return true;
});

gulp.task('watch:' +  baseTask + ':content', function() {
	gulp.watch(extPath + '/plg_content_digicom/**', ['copy:' + baseTask + ':content', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':editor', function() {
	gulp.watch(extPath + '/plg_editors-xtd_digicom/**', ['copy:' + baseTask + ':editor', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':finder', function() {
	gulp.watch(extPath + '/plg_finder_digicom/**', ['copy:' + baseTask + ':finder', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':system', function() {
	gulp.watch(extPath + '/plg_system_digicom/**', ['copy:' + baseTask + ':system', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':offline', function() {
	gulp.watch(extPath + '/plg_digicom_pay_offline/**', ['copy:' + baseTask + ':offline', browserSync.reload]);
});

gulp.task('watch:' +  baseTask + ':paypal', function() {
	gulp.watch(extPath + '/plg_digicom_pay_paypal/**', ['copy:' + baseTask + ':paypal', browserSync.reload]);
});
gulp.task('watch:' +  baseTask + ':language', function() {
	gulp.watch([
		extPath + '/plg_content_digicom/language/en-GB/**',
    extPath + '/plg_digicom_pay_offline/language/en-GB/**',
    extPath + '/plg_digicom_pay_paypal/language/en-GB/**',
    extPath + '/plg_editors-xtd_digicom/language/en-GB/**',
    extPath + '/plg_finder_digicom/language/en-GB/**',
    extPath + '/plg_system_digicom/language/en-GB/**',
	],
		['copy:' + baseTask + ':language', browserSync.reload]
	);
});
