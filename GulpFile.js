var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var watch = require('gulp-watch');

gulp.task( 'js', function() {
	return gulp.src('includes/assets/js/*.js')
		.pipe(uglify())
		.pipe(rename({
			extname: '.min.js'
		}))
		.pipe(gulp.dest('includes/assets/js/min'))
});

gulp.task('scss', function() {
	return gulp.src('includes/assets/scss/*.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('includes/assets/css'));
});

gulp.task('watch', function(){
	gulp.watch('includes/assets/scss/*.scss', ['scss']);
	gulp.watch('includes/assetsjs/*.js', ['js']);
});

gulp.task('plugin_build', function() {
	
	gulp.src('*.php')
		.pipe(gulp.dest('plugin_build/'));
	
	gulp.src('includes/**/*.php')
		.pipe(gulp.dest('plugin_build/includes'));
	
	gulp.src('includes/assets/css/**/*.css')
		.pipe(gulp.dest('plugin_build/includes/assets/css/'));
		
	gulp.src('includes/assets/js/min/*.js')
		.pipe(gulp.dest('plugin_build/includes/assets/js/min/'));
	
	gulp.src('languages/*')
		.pipe(gulp.dest('plugin_build/languages'));
	
});

gulp.task('default', ['js', 'scss']);