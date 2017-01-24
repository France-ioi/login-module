import gulp from 'gulp'
import gutil from 'gulp-util'
import browserify from 'browserify'
import babelify from 'babelify'
import source from 'vinyl-source-stream'


var app = {
    path: {
        src: './resources/frontend/',
        dst: './public/frontend'
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
    .pipe(gulp.dest(app.path.dst))
)


gulp.task('build-lib', () =>
    browserify({
        debug: true,
    })
    .require(app.external)
    .bundle()
    .pipe(source(app.lib))
    .pipe(gulp.dest(app.path.dst))
)


gulp.task('watch', ['build-app', 'build-lib'], () =>
    gulp.watch(app.path.src + '**/*' + app.extension, ['build-app'])
)


gulp.task('build', ['build-app', 'build-lib'])


gulp.task('default', ['watch'])