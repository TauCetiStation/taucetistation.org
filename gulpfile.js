'use strict';

const gulp = require('gulp');
const plumber = require('gulp-plumber');

const gulpif = require('gulp-if');
const argv = require('yargs').argv;

//postcss and plugins
const postcss = require('gulp-postcss');
const atImport = require("postcss-import");
const cssvariables = require('postcss-css-variables');
const nested = require('postcss-nested');
const autoprefixer = require('autoprefixer');
const reporter = require('postcss-reporter');
const postcssurl = require('postcss-url');
const stylelint = require('stylelint');

//js
const browserify = require('browserify');
const babelify = require('babelify');
const envify = require('envify/custom');
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const watchify = require('watchify');

const sourcemaps = require('gulp-sourcemaps');

const notify = require('gulp-notify');
const stripAnsi = require('strip-ansi');

const options = {
	css: {
		srcWatch: 'public/src/css/**/*.css',
		src:      'public/src/css/styles.css',
		dest:     'public/build/',
	},
	js: {
		src:      'public/src/js/scripts.js',
		dest:     'public/build/',
	},
};

gulp.task('default', ['css', 'js']);

gulp.task('css', () => gulp.src(options.css.src)
	.pipe(gulpif(!argv.prod, plumber({errorHandler: err => notify.onError("Error:\n" + stripAnsi(err.message))(err)})))
	.pipe(sourcemaps.init())
	.pipe(postcss([
		atImport({plugins: [/*stylelint()*/]}),
		reporter({clearReportedMessages: true}),
		nested(),
		cssvariables(),
		autoprefixer({browsers: ['last 3 versions', 'ie>9', 'safari 8', 'ios 6']}),
		postcssurl({url: 'inline', basePath: '../..'}),
	]))
	.pipe(sourcemaps.write('./maps'))
	.pipe(gulp.dest(options.css.dest))
);

gulp.task('css-watch', () => gulp.watch(options.css.srcWatch,['css']));

gulp.task('js', () => bsBundle());

gulp.task('js-watch', () => bsBundle(true));

gulp.task('watch', ['css-watch', 'js-watch']);

function bsBundle(watch) {
	let bundler = browserify(options.js.src, {debug: true})
		.transform(babelify.configure({presets : ["es2015"]}));

	if(argv.prod) {
		bundler.transform({global: true}, envify({NODE_ENV: 'production'}));
	}

	function rebundle() {
		bundler.bundle()
			.on('error', err => {
				if(argv.prod)
					return console.log(err.message);
				return notify.onError("Error:\n" + stripAnsi(err.message))(err);
			})
			.pipe(source('scripts.js'))
			.pipe(buffer())
			.pipe(sourcemaps.init({loadMaps: true}))
			.pipe(sourcemaps.write('./maps'))
			.pipe(gulp.dest(options.js.dest));
	}

	if (watch) {
		watchify(bundler).on('update', function() {
			console.log('JS rebundle...');
			rebundle();
		});
	}

	rebundle();
}
