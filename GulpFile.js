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
})

gulp.task('default', ['js', 'scss']);