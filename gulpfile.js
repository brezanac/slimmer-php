/**
 * Main build pipeline (gulp).
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */

/**
 * Project configuration from package.json
 */
const config = require('./package').config;

 /**
 * Main gulp instance.
 */
const gulp = require('gulp');

/**
 * Node.js module for path manipulation.
 */
const path = require('path');

/**
 * Dependency loader for gulp plugins.
 */
const plugins = require('gulp-load-plugins')({
    pattern: ["*"],
    scope: ["devDependencies"]
});

/**
 * Transpiles SCSS resources to CSS (each one separately).
 * Autoprefixes generated CSS.
 * Minifies generated CSS.
 * Creates source maps for generated CSS.
 */
function scss() {
    return gulp.src(config.scss.src)
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.cached('scss_cache'))
        .pipe(plugins.sassInheritance({
            dir: config.scss.path
        }))
        .pipe(plugins.sass({
            includePaths: config.scss.includePaths
        }))
        .pipe(plugins.autoprefixer())
        .pipe(plugins.cleanCss())
        .pipe(plugins.sourcemaps.write(config.scss.sourceMapsPath))
        .pipe(gulp.dest(config.scss.dest));
}

/*
 * Creates a bundle with all the CSS code (transpiled SCSS too).
 * Autoprefixes bundled CSS.
 * Minifies bundled CSS.
 * Creates source maps for bundled CSS.
 */
function css() {
    return gulp.src(config.css.src)
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.cached('css_cache'))
        .pipe(plugins.autoprefixer())
        .pipe(plugins.cleanCss())
        .pipe(plugins.concat(config.css.bundleName))
        .pipe(plugins.sourcemaps.write(config.css.sourceMapsPath))
        .pipe(gulp.dest(config.css.dest));
}

/*
 * Minifies JavaScript resources (each separately).
 * Creates JavaScript sourcemaps.
 */
function js() {
    return gulp.src(config.js.src)
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.cached('js_cache'))
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.uglify())
        .pipe(plugins.sourcemaps.write(config.js.sourceMapsPath))
        .pipe(gulp.dest(config.js.dest))
}

/*
 * Adds fonts to static assets.
 */
function fonts() {
    return gulp.src(config.fonts.src)
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.cached('fonts_cache'))
        .pipe(gulp.dest(config.fonts.dest));
}

/*
 * Optimizes images.
 * Adds images to static assets.
 */
function images() {
    return gulp.src(config.images.src)
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.cached('images_cache'))
        .pipe(plugins.imagemin(config.images.options))
        .pipe(gulp.dest(config.images.dest));
}

/*
 * Optimizes SVG resources (each separately).
 * Bundles SVG resources into one SVG file (sprite).
 */
function svg() {
    return gulp.src(config.svg.src)
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.svgmin(function getOptions(file) {
            var prefix = path.basename(file.relative, path.extname(file.relative));
            return {
                plugins: [{
                    cleanupIDs: {
                        prefix: prefix + '-',
                        minify: true
                    }
                }]
            }
        }))
        .pipe(plugins.svgstore())
        .pipe(plugins.rename(config.svg.bundleName))
        .pipe(gulp.dest(config.svg.dest));
}

/*
 * Generates unique revision hashes for static assets.
 * Appends generated revision hashes to the original asset names.
 * Stores generated revision hashes into a manifest file.
 */
function revision() {
    return gulp.src(config.rev.src, {base: config.rev.base})
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.rev())
        .pipe(plugins.revDeleteOriginal())
        .pipe(gulp.dest(config.rev.dest))
        .pipe(plugins.rev.manifest(config.rev.manifest.revPath, {
            merge: false
        }))
        .pipe(gulp.dest(config.rev.dest));
}

/*
 * Injects generated revision hashes into static assets.
 */
function inject() {
    const manifest = gulp.src(config.rev.manifest.rootPath);
    return gulp.src(config.rev.manifest.injectPaths)
        .pipe(plugins.plumber({errorHandler: onError}))
        .pipe(plugins.revRewrite({manifest: manifest}))
        .pipe(gulp.dest(config.rev.dest));
}

/*
 * Resets the build environment to a clean state.
 */
function reset(cb) {
    plugins.del(config.cleanup.before);
    cb();
}

/*
 * Cleans up after the build process has finished.
 */
function cleanup(cb) {
    plugins.del(config.cleanup.after);
    cb();
}

/*
 * Cleans all gulp caches.
 */
function caches(cb) {
    plugins.cached.caches = {};
    cb();
}

/*
 * Watches for resource changes and initiates appropriate tasks.
 */
function watch() {
    gulp.watch(config.scss.src, scss);
    gulp.watch(config.css.src, css);
    gulp.watch(config.js.src, js);
    gulp.watch(config.fonts.src, fonts);
    gulp.watch(config.images.src, images);
    gulp.watch(config.svg.src, svg);
}

/*
 * Error callback for Plumber.
 */
function onError(error) {
    plugins.notify.onError({
        title: "Gulp error in " + error.plugin,
        message: error.toString()
    })(error);
    this.emit('end');
}

/*
 * Default development build chain.
 */
const developmentChain = gulp.series(
    reset,
    caches,
    gulp.parallel(
        gulp.series(scss, css),
        js,
        fonts,
        images,
        svg
    ),
    cleanup,
    watch
);

/*
 * Production build chain.
 */
const productionChain = gulp.series(
    reset,
    caches,
    gulp.parallel(
        gulp.series(scss, css),
        js,
        fonts,
        images,
        svg
    ),
    revision,
    inject,
    cleanup
);

/*
 * Task exports.
 */
exports.reset = reset;
exports.cleanup = cleanup;
exports.caches = caches;
exports.watch = watch;
exports.scss = scss;
exports.css = css;
exports.js = js;
exports.fonts = fonts;
exports.images = images;
exports.svg = svg;
exports.revision = revision;
exports.inject = inject;

/*
 * Chain exports.
 */
exports.default = developmentChain;
exports.production = productionChain;
