const gulp = require("gulp");
const sass = require("gulp-sass");

const src = "src/";
const dst = "www/";

const sassSrc = src + "scss/*.scss";
const sassDst = dst + "css/";

const phpSrc = src + "**/*.php";
const captchaSrc = "recaptcha-master/src/**/*.php";

function copy(input, output){
	return gulp.src(input).pipe(gulp.dest(output));
};

gulp.task("sass", function(){
	return gulp
		.src(sassSrc)
		.pipe(sass({
			outputStyle:"compressed"
		})
		.on("error", function(ex){
		   console.log(ex);
		}))
		.pipe(gulp.dest(sassDst));
});

gulp.task("watch", function(){
	gulp.watch(sassSrc, ["sass"]);
});

gulp.task("build", function(){
	return copy(phpSrc, dst);
});