var gulp = require('gulp');
var config = require('../../gulp-config.json');

// Dependencies
var browserSync = require('browser-sync');
var rm          = require('gulp-rimraf');
var gutil 			= require('gulp-util')
var less 				= require('gulp-less');
var minify 			= require('gulp-minify-css');
var uglify 			= require('gulp-uglify');
var rename 			= require('gulp-rename');
var runSequence = require('gulp-run-sequence');
var baseTask  	= 'media.media';
var extPath   	= './src';
var mediaPath = extPath + '/com_digicom/media';
var fs = require('fs');

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':css',
		'clean:' + baseTask + ':less',
		'clean:' + baseTask + ':js',
		'clean:' + baseTask + ':images'
	],
	function() {
		return gulp.src(config.wwwDir + '/media/com_digicom', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' +  baseTask + ':css', function() {
	// gutil.log('Lets start cleaning content plugin');
	return gulp.src(config.wwwDir + '/media/com_digicom/css', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' +  baseTask + ':less', function() {
	// gutil.log('Lets start cleaning content plugin');
	return gulp.src(config.wwwDir + '/media/com_digicom/less', { read: false }).pipe(rm({ force: true }));
});

gulp.task('clean:' +  baseTask + ':js', function() {
	// gutil.log('Lets start cleaning content plugin');
	return gulp.src(config.wwwDir + '/media/com_digicom/js', { read: false }).pipe(rm({ force: true }));
});
gulp.task('clean:' +  baseTask + ':images', function() {
	// gutil.log('Lets start cleaning content plugin');
	return gulp.src(config.wwwDir + '/media/com_digicom/images', { read: false }).pipe(rm({ force: true }));
});


// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':css',
		'copy:' + baseTask + ':less',
		'copy:' + baseTask + ':js',
		'copy:' + baseTask + ':images'
	],
	function() {
		return gulp.src([
			extPath + '/com_digicom/media/index.html'
		]).pipe(gulp.dest(config.wwwDir + '/media/com_digicom'));
		return true;bower.json
});

gulp.task('copy:' +  baseTask + ':css', ['clean:' + baseTask + ':css'], function() {
	return gulp.src(extPath + '/com_digicom/media/css/**').pipe(gulp.dest(config.wwwDir + '/media/com_digicom/css'));
});
gulp.task('copy:' +  baseTask + ':less', ['clean:' + baseTask + ':less'], function() {
	return gulp.src(extPath + '/com_digicom/media/less/**').pipe(gulp.dest(config.wwwDir + '/media/com_digicom/less'));
});
gulp.task('copy:' +  baseTask + ':js', ['clean:' + baseTask + ':js'], function() {
	return gulp.src(extPath + '/com_digicom/media/js/**').pipe(gulp.dest(config.wwwDir + '/media/com_digicom/js'));
});
gulp.task('copy:' +  baseTask + ':images', ['clean:' + baseTask + ':images'], function() {
	return gulp.src(extPath + '/com_digicom/media/images/**').pipe(gulp.dest(config.wwwDir + '/media/com_digicom/images'));
});

var dest = extPath + '/com_digicom/media';
var src = extPath + '/com_digicom/media';

var mediaconfig = {
  less: {
    src: src + '/less/*.less',
    dest: dest + '/css',
    settings: {
      compress: false ,
      indentedSyntax: false, // Enable .less syntax?
      imagePath: src + '/images' // Used by the image-url helper
    }
  }
};

gulp.task('less:' + baseTask, function () {

	return gulp.src(mediaconfig.less.src)
    .pipe(less(mediaconfig.less.settings))
		// .pipe(minify({compatibility: 'ie8'}))
		// .pipe(rename(function (path) {
		// 		path.basename += '.min';
		// }))
    .pipe(gulp.dest(mediaconfig.less.dest));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':css',
		'watch:' + baseTask + ':js',
		'watch:' + baseTask + ':less',
		'watch:' + baseTask + ':images'
	],
	function() {
		return true;
});

gulp.task('watch:' + baseTask + ':css', function() {
	gulp.watch(src + '/css/*.css', ['copy:' + baseTask + ':css', browserSync.reload]);
})
gulp.task('watch:' + baseTask + ':js', function() {
	gulp.watch(src + '/js/*.js', ['copy:' + baseTask + ':js', browserSync.reload]);
});
gulp.task('watch:' + baseTask + ':less', function(cb) {
	gulp.watch(src + '/less/*.less', ['less:' + baseTask, 'copy:' + baseTask + ':js', browserSync.reload]);
});
gulp.task('watch:' + baseTask + ':images', function() {
	gulp.watch(src + '/images/**',  ['copy:' + baseTask + ':images', browserSync.reload]);
});
