var requireDir = require('require-dir');

var dir = requireDir('./node_modules/joomla-gulp', {recurse: true});
var dir = requireDir('./joomla-gulp', {recurse: true});


// Load config
var extension = require('./package.json');
var config    = require('./gulp-config.json');

var gulp    = require('gulp');
var zip     = require('gulp-zip');
var rm      = require('gulp-rimraf');
var replace = require('gulp-replace');
var es      = require('event-stream');

//release
gulp.task('cleanRelease', function () {
  return gulp.src('./releases', { read: false }).pipe(rm({ force: true }));
});

var modelFolders = [
    'com_digicom',
    'mod_digicom_cart',
    'mod_digicom_categories',
    'mod_digicom_menu',
    'plg_content_digicom',
    'plg_digicom_pay_offline',
    'plg_digicom_pay_paypal',
    'plg_editors-xtd_digicom',
    'plg_finder_digicom',
    'plg_system_digicom',
];

// identifies a dependent task must be complete before this one begins
gulp.task('release', ['cleanRelease'], function() {
    var zips = [], modelZip;

    for (var i = 0; i < modelFolders.length; i++) {
        var model = modelFolders[i];
        modelZip = gulp.src('./src/'+ model+'/**')
                       .pipe(replace(/##VERSION##/g, extension.version))
                       .pipe(replace(/##CREATIONDATE##/g, extension.creationDate))
                       .pipe(zip(model + '.zip'));
        // notice we removed the dest step and store the zip stream (still in memory)
        zips.push(modelZip);
    }

    var pkgfiles = gulp.src(['!*', './src/pkg_language/**', './src/pkg.script.php', './src/pkg_digicom.xml'])
                        // run the replacement of version name
                        .pipe(replace(/##VERSION##/g, extension.version))
                        // run the replacement of creation date
                        .pipe(replace(/##CREATIONDATE##/g, extension.creationDate));

    zips.push(pkgfiles);

    // we finally merge them (the zips), zip them again, and output.
    return es.merge.apply(null, zips)
        .pipe(zip(extension.name + '_' + extension.version + '.zip'))
        .pipe(gulp.dest('releases'));
});
