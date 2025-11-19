/**
 * Gulp file for Backup Copilot.
 *
 * @format
 * @author Krasen Slavov (@krasenslavov)
 * @version 1.0
 */

/**
 * Issue: Deprecation Warning: The legacy JS API is deprecated and will be removed in Dart Sass 2.0.0.
 *
 * npm uninstall node-sass gulp-sass
 * npm install --save-dev sass gulp-sass
 *
 * Issue: Disable Windows notifications temporary.
 *
 * $env:DISABLE_NOTIFIER = "true"
 * Remove-Item Env:DISABLE_NOTIFIER
 */

// Importing required modules
const browserSync = require("browser-sync").create();
const gulp = require("gulp");
const del = require("del");
const autoprefixer = require("gulp-autoprefixer");
const cleanCSS = require("gulp-clean-css");
const filter = require("gulp-filter");
const ignore = require("gulp-ignore");
const imageMin = require("gulp-imagemin");
const lineEndingCorrector = require("gulp-line-ending-corrector");
const MMQ = require("gulp-merge-media-queries");
const notify = require("gulp-notify");
const rename = require("gulp-rename");
const sass = require("gulp-sass")(require("sass"));
const sourceMaps = require("gulp-sourcemaps");
const uglifyJS = require("gulp-uglify");

const { dest, parallel, series, src, watch } = gulp;

// Importing webpack configuration for component and block scripts
const webpackComponents = require("./webpack.config");

// Exclude and filter out all scripts and folders with block/component, they are handled separately
const excludeScriptsCondition = [
	"**/bkpc-admin.js",
	"**/admin/**/*",
	"**/cloud-storage/**/*",
	"**/backup-scheduler/**/*",
	"**/manage-backups/**/*",
	"**/common/**/*"
];

// Project configuration
const project = "bkpc";
const projectUrl = "backupcopilot.local";
const projectPath = "./";
const certPath = "C:\\Users\\krasenslavov\\AppData\\Roaming\\Local\\run\\router\\nginx\\certs\\";

/**
 * Browsers list for autoprefixer.
 * @type {string[]}
 */
const AUTOPREFIXER_BROWSERS = [
	"last 2 version",
	"> 1%",
	"ie >= 9",
	"ie_mob >= 10",
	"ff >= 30",
	"chrome >= 34",
	"safari >= 7",
	"opera >= 23",
	"ios >= 7",
	"android >= 4",
	"bb >= 10"
];

/**
 * File paths for various sources and destinations.
 * @type {Object}
 */
const paths = {
	php: "./**/*.php",
	dev: {
		scss: "./assets/dev/scss/**/*.scss",
		scripts: "./assets/dev/scripts/**/*.js",
		styles: "./assets/dev/styles",
		images: "./assets/dev/images/*.{jpg,jpeg,png,gif,svg}",
		fonts: "./assets/dev/fonts/*.{woff,woff2,ttf,eot,svg}"
	},
	dist: {
		scripts: "./assets/dist/js",
		styles: "./assets/dist/css",
		images: "./assets/dist/img",
		fonts: "./assets/dist/fonts"
	}
};

/**
 * BrowserSync task for live reloading.
 *
 * @param {Function} done - Callback to signal task completion.
 */
function browserSyncro(done) {
	browserSync.init(
		{
			proxy: "http://" + projectUrl,
			host: projectUrl,
			open: "external",
			port: 3000,
			// https: {
			//   key: `${certPath}${projectUrl}.key`,
			//   cert: `${certPath}${projectUrl}.crt`
			// },
			injectChanges: true
		},
		done
	);
}

/**
 * Task to move font files to the distribution folder.
 */
function fonts() {
	return src(paths.dev.fonts)
		.pipe(dest(paths.dist.fonts))
		.pipe(
			notify({
				message: "Fonts task completed successfully.",
				onLast: true,
				enabled: process.env.NOTIFY !== "false"
			})
		);
}

/**
 * Task to optimize and move image files to the distribution folder.
 */
function images() {
	return src(paths.dev.images)
		.pipe(
			imageMin([
				imageMin.mozjpeg({ progressive: true }),
				imageMin.optipng({ optimizationLevel: 3 }),
				imageMin.svgo({ plugins: [{ removeViewBox: false }] })
			])
		)
		.pipe(dest(paths.dist.images))
		.pipe(
			notify({
				message: "Images tasks completed successfully.",
				onLast: true,
				enabled: process.env.NOTIFY !== "false"
			})
		);
}

/**
 * Task to process and minify JavaScript files.
 */
function scripts() {
	const filterOut = filter(excludeScriptsCondition, { restore: true });

	return src(paths.dev.scripts)
		.pipe(filterOut)
		.pipe(ignore.exclude(excludeScriptsCondition))
		.pipe(filterOut.restore)
		.pipe(lineEndingCorrector())
		.pipe(rename({ suffix: ".min" }))
		.pipe(uglifyJS())
		.pipe(lineEndingCorrector())
		.pipe(dest(paths.dist.scripts))
		.pipe(
			notify({
				message: "Scripts task completed successfully.",
				onLast: true,
				enabled: process.env.NOTIFY !== "false"
			})
		);
}

/**
 * Task to process and minify styles (SCSS).
 */
function styles() {
	return (
		src(paths.dev.scss)
			// Styles `dev` tasks
			.pipe(sass().on("error", sass.logError))
			.pipe(autoprefixer(AUTOPREFIXER_BROWSERS))
			.pipe(MMQ({ log: true }))
			.pipe(dest(paths.dev.styles))
			.pipe(
				notify({
					message: "Styles `dev` task completed successfully.",
					onLast: true,
					enabled: process.env.NOTIFY !== "false"
				})
			)
			// Styles `dist` tasks
			.pipe(sourceMaps.init())
			.pipe(sass().on("error", sass.logError))
			.pipe(autoprefixer(AUTOPREFIXER_BROWSERS))
			.pipe(MMQ({ log: true }))
			.pipe(cleanCSS())
			.pipe(rename({ suffix: ".min" }))
			.pipe(sourceMaps.write("."))
			.pipe(dest(paths.dist.styles))
			.pipe(
				notify({
					message: "Styles `dist` task completed successfully.",
					onLast: true,
					enabled: process.env.NOTIFY !== "false"
				})
			)
	);
}

/**
 * Task to clean the distribution folder.
 */
function cleanDist() {
	return del([paths.dist.styles, paths.dist.scripts, paths.dist.images, paths.dist.fonts]);
}

/**
 * Task to watch files and run corresponding tasks on changes.
 */
function watchFiles() {
	watch(paths.dev.fonts, fonts);
	watch(paths.dev.images, images);
	watch(paths.dev.scripts, scripts);
	watch(paths.dev.scss, styles);

	if (typeof webpackComponents === "function") {
		watch(paths.dev.scripts, series(scripts, webpackComponents));
	}

	// if (typeof browserSyncro === 'function' ) {
	//   watch(paths.php, browserSyncro);
	// }
}

/**
 * Exported tasks.
 */
exports.cleanDist = cleanDist;
exports.fonts = fonts;
exports.images = images;
exports.scripts = scripts;
exports.styles = styles;

if (typeof webpackComponents === "function") {
	exports.webpackComponents = webpackComponents;
}

exports.build = series(cleanDist, parallel(fonts, images, scripts, styles));

if (typeof webpackComponents === "function") {
	exports.build = series(cleanDist, parallel(fonts, images, scripts, webpackComponents, styles));
}

// Task to watch files during development
exports.watch = parallel(watchFiles, browserSyncro);
