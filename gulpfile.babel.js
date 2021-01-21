// Dependencies
import gulp from 'gulp'
import path from 'path'
import inquirer from 'inquirer'
import del from 'del'
import shell from 'gulp-shell'
import rename from 'gulp-rename'
import connectPHP from 'gulp-connect-php'
import plumber from 'gulp-plumber'
import notify from 'gulp-notify'
// import colors from 'colors/safe';
import sass from 'gulp-sass'
import postcss from 'gulp-postcss'
// import postcssScss from 'postcss-scss';
// import postcssBemLinter from 'postcss-bem-linter';
import autoprefixer from 'autoprefixer'
import sourcemaps from 'gulp-sourcemaps'
import webpack from 'webpack'
import webpackStream from 'webpack-stream'
import webpackConfigDEV from './webpack.dev'
import webpackConfigPROD from './webpack.prod'
import { create as browserSyncCreate } from 'browser-sync'

// Settings
const basePath = __dirname
const nodePath = path.resolve(__dirname, 'node_modules').replace(/\\/g, '/') // Fix Windows 10 issue;
const destPath = `${basePath}/dist`.replace(/\\/g, '/') // Fix Windows 10 issue;

const baseName = path.basename(basePath)

const browserSync = browserSyncCreate()
const browserSyncProxy = `${baseName}.test`

// const bemUtilitySelectors = /^\.u-/
// const bemIgnoreSelectors = [
//   /^\.c-/,
//   /^\.grid-/,
//   /^\.cell/,
//   /^\.label/,
//   /^\.button/,
//   /^\.svg-icon-/,
//   /^\.flickity/
// ]

// DEV TASKS
// =======================================================================

// Plumber
const plumberHandler = {
  errorHandler: notify.onError({
    title: 'Gulp Error',
    message: '<%= error.message %>'
  })
}

// Reload
function reload(done) {
  browserSync.reload()
  done()
}

// SCSS
function scss() {
  return (
    gulp
      .src(`${basePath}/scss/**/*.scss`)
      .pipe(plumber(plumberHandler))
      // .pipe(
      //   postcss(
      //     [
      //       postcssBemLinter({
      //         preset: 'bem',
      //         implicitComponents: `scss/components/**/*.scss`,
      //         implicitUtilities: `scss/utils/**/*.scss`,
      //         utilitySelectors: bemUtilitySelectors,
      //         ignoreSelectors: bemIgnoreSelectors
      //       })
      //     ],
      //     {
      //       syntax: postcssScss
      //     }
      //   )
      // )
      .pipe(sourcemaps.init())
      .pipe(
        sass({
          precision: 10,
          includePaths: [nodePath]
        })
      )
      .pipe(postcss([autoprefixer({ cascade: false })]))
      .pipe(sourcemaps.write())
      .pipe(gulp.dest(destPath))
      .pipe(browserSync.stream())
  )
}

function scss_prod() {
  return (
    gulp
      .src(`${basePath}/scss/**/*.scss`)
      // .pipe(
      //   postcss(
      //     [
      //       postcssBemLinter({
      //         preset: 'bem',
      //         implicitComponents: `scss/components/**/*.scss`,
      //         implicitUtilities: `scss/utils/**/*.scss`,
      //         utilitySelectors: bemUtilitySelectors,
      //         ignoreSelectors: bemIgnoreSelectors
      //       })
      //     ],
      //     {
      //       syntax: postcssScss
      //     }
      //   )
      // )
      .pipe(
        sass({
          precision: 10,
          outputStyle: 'compressed',
          includePaths: [nodePath]
        })
      )
      .pipe(postcss([autoprefixer({ cascade: false })]))
      .pipe(gulp.dest(destPath))
  )
}

// JS
function js() {
  return gulp
    .src(`${basePath}/js/app.js`)
    .pipe(plumber(plumberHandler))
    .pipe(webpackStream(webpackConfigDEV, webpack))
    .pipe(gulp.dest(destPath))
}

function js_prod() {
  return gulp
    .src(`${basePath}/js/app.js`)
    .pipe(plumber(plumberHandler))
    .pipe(webpackStream(webpackConfigPROD, webpack))
    .pipe(gulp.dest(destPath))
}

// Watch
function watch_files() {
  gulp.watch(`${basePath}/scss/**/*.scss`.replace(/\\/g, '/'), scss)
  gulp.watch(`${basePath}/js/**/*.js`.replace(/\\/g, '/'), gulp.series(js, reload))
  gulp.watch(`${basePath}/**/*.twig`, reload)
  gulp.watch(`${basePath}/**/*.php`, reload)
  gulp.watch(`${basePath}/**/*.html`, reload)
}

// PHP
function php_fn() {
  connectPHP.server(
    {
      port: 8000,
      open: false,
      hostname: '127.0.0.1',
      base: basePath,
      stdio: 'ignore'
    },
    () => {
      browserSync.init({
        ghostMode: false,
        ui: false,
        notify: false,
        proxy: '127.0.0.1:8000'
      })
    }
  )
}

// Proxy
function proxy_fn() {
  browserSync.init({
    ghostMode: false,
    ui: false,
    notify: false,
    proxy: browserSyncProxy
  })
}

// Compile
let cHtmlUrl, cHtmlOutputDir, cHtmlPageInSubfolder

function cHtmlPrompt() {
  return inquirer
    .prompt([
      {
        type: 'input',
        name: 'url',
        message: 'What is the url or path of the website?',
        default: ``
      },
      {
        type: 'input',
        name: 'output-dir',
        message: 'Output directory?',
        default: 'public'
      },
      {
        type: 'confirm',
        name: 'page-in-subfolder',
        message: "Output every page in it's own subfolder?",
        default: false
      }
    ])
    .then((answers) => {
      cHtmlUrl = answers['url']
      cHtmlOutputDir = `${basePath}/${answers['output-dir']}`
      cHtmlPageInSubfolder = answers['page-in-subfolder']
    })
}

function cHtmlClean() {
  return del([`${basePath}/dist/**/*`, `${cHtmlOutputDir}/**/*`])
}

function cHtmlShell(done) {
  const cmd = [
    `php html-compiler.php --url=${cHtmlUrl} --output-dir=${cHtmlOutputDir} --page-in-subfolder=${cHtmlPageInSubfolder}`
  ]

  console.log(cmd)

  shell.task([cmd])()

  done()
}

function cHtmlStage(done) {
  cHtmlUrl = process.env.STAGING_URL || ''
  cHtmlOutputDir = `${basePath}/public`
  cHtmlPageInSubfolder = true
  done()
}

function cHtmlFiles() {
  return gulp
    .src(
      [
        `${basePath}/dist/**/*`,
        `${basePath}/fonts/**/*`,
        `${basePath}/img/**/*`,
        `${basePath}/svgs/**/*`,
        `!${basePath}/svgs/fa/**/*`
      ],
      {
        base: basePath
      }
    )
    .pipe(gulp.dest(cHtmlOutputDir))
}

const php = gulp.parallel(php_fn, watch_files)
const proxy = gulp.parallel(proxy_fn, watch_files)
const build = gulp.parallel(scss_prod, js_prod)
const compile = gulp.series(cHtmlPrompt, cHtmlClean, build, cHtmlFiles, cHtmlShell)
const stage = gulp.series(cHtmlStage, cHtmlClean, build, cHtmlFiles, cHtmlShell)

export { scss_prod, js_prod, php, proxy, build, compile, stage }

export default php
