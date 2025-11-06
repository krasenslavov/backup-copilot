/**
 * Webpack config for Backup Copilot.
 *
 * @format
 * @author Krasen Slavov (@krasenslavov)
 * @version 1.0
 */

const gulp = require("gulp");
const mergeStream = require("merge-stream");
const webpackStream = require("webpack-stream");
const webpack = require("webpack");
const buffer = require("vinyl-buffer");
const uglifyJS = require("gulp-uglify");
const notify = require("gulp-notify");
const path = require("path");

/**
 * Configuration objects for Webpack.
 * @type {Object[]}
 */
const components = [
	{
		inputPath: "./assets/dev/scripts/bkpc-admin.js",
		outputFile: "bkpc-admin.min.js",
		distFolder: "./assets/dist/js",
		presetsArr: ["@babel/preset-env"]
	}
];

/**
 * Task to process and minify JavaScript files using Webpack.
 */
function webpackComponents() {
	const tasks = components.map((components) => {
		const { inputPath, distFolder, outputFile, presetsArr } = components;

		const webpackConfig = {
			mode: "production", // or 'development'
			entry: inputPath,
			output: {
				filename: outputFile,
				path: path.resolve(__dirname, distFolder)
			},
			module: {
				rules: [
					{
						test: /\.js$/,
						exclude: /node_modules/,
						use: {
							loader: "babel-loader",
							options: {
								presets: presetsArr
							}
						}
					}
				]
			}
		};

		return gulp
			.src(inputPath)
			.pipe(webpackStream(webpackConfig, webpack))
			.on("error", function (error) {
				console.error(error);
				this.emit("end");
			})
			.pipe(buffer())
			.pipe(uglifyJS())
			.pipe(gulp.dest(distFolder))
			.pipe(
				notify({
					message: `Webpack task for ${outputFile} completed successfully.`,
					onLast: true,
					enabled: process.env.NOTIFY !== "false"
				})
			);
	});

	// Merge all tasks into a single stream
	return mergeStream(...tasks);
}

// Exporting the task function for use in other modules.
module.exports = webpackComponents;
