## Quick start

- `npm install -g gulp` _or_ `yarn global add gulp` _(Skip if you already have gulp installed)_

- `npm install` _or_ `yarn` _(Install dependencies)_

- `composer install` _(Install composer dependencies)_

- `git config core.hooksPath .githooks` _(Change git hooks path)_

- `gulp php` _or_ `npm run dev` _or_ `yarn dev` _(Run dev environment)_

## Available gulp tasks

`gulp build` - compile production ready files (minified & autoprefixed)  
`gulp php` - (default task) run the server and watch for files changes  
`gulp proxy` - run BrowserSync proxying a local url, you need to modify `browserSyncProxy` setting  
`gulp compile` - build the full website using the twig to html compiler.

## Compile Twig to HTML

`php html-compiler.php`

#### Options

- `--url=` - the url or path of the website. Default is **'\'**.
- `--template-url=` - the url or path of the template. Default is the same as the **url**.
- `--output-dir=` - where to output the compiled html files. Deafult is `public`.
- `--page-in-subfolder=` (bool) - set to **true** if you would like each page to be placed in a subfolder, example _public/about/index.html_ instead of _public/about.html_.

## DEV Environment

#### Page Preview

`http://localhost:3000/[page-file-name]/` _Ex: http://localhost:3000/example/_

#### Component Preview

`http://localhost:3000/component/[component-file-name]/` _Ex: http://localhost:3000/component/c-example/_

## Stage

`https://[branch-name]-[repository-name].01.dvsb1.com/`

A `demo-links.html` file is generated on each push https://[branch-name]-[repository-name].01.dvsb1.com/demo-links.html.

**Username**: \*\*\*\*  
**Password**: \*\*\*\*

## Code Formating

To keep the code style consistent between developers please run `git config core.hooksPath .githooks` to setup the pre-commit hook or `yarn format` before each commit.

---

Created by [Lab19](https://lab19.dev/).
