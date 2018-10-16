const gulp = require("gulp");
const sass = require("gulp-sass");
const preprocess = require("gulp-preprocess");
const runSequence = require("run-sequence");
const del = require('del');
const createTask = require('./gulp/create-task');

const src = "src/";
const dst = "www/";

const PREPROCESS_VARS = {
	SECURE:true,
	HIDDEN_COMMENTS:true,
	USE_CAPTCHA:true
};

const config = {
	php:{
		name:"php",
		files:{
			input:src + "**/*.php",
			output:dst
		}
	},
	captcha:{
		name:"captcha",
		files:{
			input:"recaptcha-master/src/**/*.php",
			output:dst + "include/recaptcha"
		}
	},
	random_compat:{
		name:"random_compat",
		files:{
			input:"random_compat-2.0.17/lib/**/*.php",
			output:dst + "include/random_compat"
		}
	},
	js:{
		name:"js",
		files:{
			input:src + "js/**/*.js",
			output:dst + "js/"
		}
	},
	sass:{
		name:"sass",
		files:{
			input:src + "scss/*.scss",
			output:dst + "css/"
		}
	}
};

//php
function php(config){
	gulp
		.src(config.files.input)
		.pipe(preprocess({
			context:PREPROCESS_VARS
		}))
		.pipe(gulp.dest(config.files.output));
}
createTask("php", php, [config.php]);

//copy
function copy(config){
	gulp.src(config.files.input).pipe(gulp.dest(config.files.output));
}
createTask("copy", copy, [config.captcha, config.random_compat, config.js]);

//sass
function compileSass(config){
	gulp.src(config.files.input)
		.pipe(sass({
			outputStyle:"compressed"
		}).on("error", function(ex){
		   console.log(ex);
		}))
		.pipe(gulp.dest(config.files.output));
}
createTask("sass", compileSass, [config.sass]);

//clean
//clean:php
//clean:captcha
//clean:js
//clean:sass
function clean(config){
	return del([config.files.output]);
}
createTask("clean", clean, [config.php, config.captcha, config.random_compat, config.js, config.sass]);

//build
//build:php
//build:captcha
//build:js
//build:sass
function build(buildConfig){
	switch (buildConfig.name){
		case config.php.name:
			php(buildConfig);
			break;
		case config.captcha.name:
		case config.random_compat.name:
		case config.js.name:
			copy(buildConfig);
			break;
		case config.sass.name:
			compileSass(buildConfig);
			break;
	}
}
createTask("build", build, [config.php, config.captcha, config.random_compat, config.js, config.sass]);

//watch
//watch:php
//watch:js
//watch:sass
function watch(config){
	return gulp.watch(config.files.input, ["build:" + config.name]);
}
createTask("watch", watch, [config.php, config.js, config.sass]);

gulp.task("default", ["build"]);