// Dependencies
import { series, parallel, watch, src, dest } from 'gulp';
import connectPHP from 'gulp-connect-php';
import plumber from 'gulp-plumber';
import notify from 'gulp-notify';
import sass from 'gulp-sass';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import sourcemaps from 'gulp-sourcemaps';
import webpack from 'webpack';
import webpackStream from 'webpack-stream';
import webpackConfigDEV from './webpack.dev';
import webpackConfigPROD from './webpack.prod';
import { create as browserSyncCreate } from 'browser-sync';



// Settings
const browserSync = browserSyncCreate();
const browserSyncProxy = 'local-url.test';


const basePath = __dirname;
const nodePath = `${basePath}/node_modules`;
const destPath = `${basePath}/dist`;

console.log(`Base path: ${basePath}`);
console.log(`Build path: ${destPath}`);



// DEV TASKS
// =======================================================================

// Plumber
const plumberHandler = {
  errorHandler: notify.onError({
    title: 'Gulp Error',
    message: '<%= error.message %>'
  })
};

// Reload
function reload(done) {
  browserSync.reload();
  done();
}

// SCSS
function scss() {
  return src(`${basePath}/scss/**/*.scss`)
    .pipe(plumber(plumberHandler))
    .pipe(sourcemaps.init())
    .pipe(sass({
      precision: 10,
      includePaths: [nodePath]
    }))
    .pipe(postcss([
      autoprefixer({ cascade: false })
    ]))
    .pipe(sourcemaps.write())
    .pipe(dest(destPath))
    .pipe(browserSync.stream());
}

function scss_prod() {
  return src(`${basePath}/scss/**/*.scss`)
    .pipe(sass({
      precision: 10,
      outputStyle: 'compressed',
      includePaths: [nodePath]
    }))
    .pipe(postcss([
      autoprefixer({ cascade: false })
    ]))
    .pipe(dest(destPath));
}

// JS
function js() {
  return src(`${basePath}/js/main.js`)
    .pipe(plumber(plumberHandler))
    .pipe(webpackStream(webpackConfigDEV, webpack))
    .pipe(dest(destPath));
}

function js_prod() {
  return src(`${basePath}/js/main.js`)
    .pipe(webpackStream(webpackConfigPROD, webpack))
    .pipe(dest(destPath));
}

// Watch
function watch_files() {
  watch(`${basePath}/scss/**/*.scss`, scss);
  watch(`${basePath}/js/**/*.js`, series(js, reload));
  watch(`${basePath}/**/*.twig`, reload);
  watch(`${basePath}/**/*.php`, reload);
  watch(`${basePath}/**/*.html`, reload);
}

// PHP
function php_fn() {
  connectPHP.server({
    port: 8000,
    open: false,
    hostname: '127.0.0.1',
    base: basePath,
    stdio: 'ignore'
  }, () => {
    browserSync.init({
      ghostMode: false,
      ui: false,
      notify: false,
      proxy: '127.0.0.1:8000'
    });
  });
}

// Proxy
function proxy_fn() {
  browserSync.init({
    ghostMode: false,
    ui: false,
    notify: false,
    proxy: browserSyncProxy
  });
}

const proxy = parallel(proxy_fn, watch_files);
const php = parallel(php_fn, watch_files);
const build = parallel(scss_prod, js_prod);

export {
  scss_prod,
  js_prod,
  php,
  proxy,
  build
};

export default php;
