import gulp from 'gulp'
import gutil from 'gulp-util'
import sass from 'gulp-sass'
import concat from 'gulp-concat'
import uglify from 'gulp-uglify'
import streamify from 'gulp-streamify'
import gulpif from 'gulp-if'
import cleancss from 'gulp-clean-css'
import browserify from 'browserify'
import babelify from 'babelify'
import source from 'vinyl-source-stream'

var is_production = !!gutil.env.production;
if(is_production) {
    process.env.NODE_ENV = 'production';
}

var path = {
    app: './resources/frontend/',
    build: './public/build/'
}

var app = {
    path: {
        src: path.app + 'js/',
        dst: path.build
    },
    extension: '.js',
    entry: 'app.js',
    lib: 'libs.js',
    external: [
        'jschannel',
        'promise-polyfill',
        'whatwg-fetch',
        'history',
        'react',
        'react-dom',
        'react-router',
        'js-cookie'
    ]
}

var styles = {
    src: [
        path.app + 'css/**/*.css'
    ],
    dst: 'app.css'
}


gulp.task('build-app', () =>
    browserify({
        entries: app.path.src + app.entry,
        extensions: [ app.extension ],
        debug: true,
        paths: [ app.path.src ],
        cache: {},
        packageCache: {}
    })
    .external(app.external)
    .transform('babelify', {
        presets: ['es2015', 'react'],
        plugins: ['transform-class-properties']
    })
    .bundle()
    .on('error', function(err){
        gutil.log(gutil.colors.red.bold('[browserify error]'));
        gutil.log(err.message);
        this.emit('end');
    })
    .pipe(source(app.entry))
    .pipe(gulpif(is_production, streamify(uglify())))
    .pipe(gulp.dest(app.path.dst))
)


gulp.task('build-lib', () =>
    browserify({
        debug: true,
    })
    .require(app.external)
    .bundle()
    .pipe(source(app.lib))
    .pipe(gulpif(is_production, streamify(uglify())))
    .pipe(gulp.dest(app.path.dst))
)


gulp.task('build-styles', () =>
    gulp
        .src(styles.src)
        .pipe(sass())
        .pipe(gulpif(is_production, cleancss()))
        .pipe(concat(styles.dst))
        .pipe(gulp.dest(path.build))
)


gulp.task('watch', ['build-app', 'build-lib'], () => {
    gulp.watch(app.path.src + '**/*' + app.extension, ['build-app'])
})
gulp.task('build', ['build-app', 'build-lib', 'build-styles'])
gulp.task('default', ['watch'])