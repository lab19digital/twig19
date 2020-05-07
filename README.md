## Quick start

- `npm install -g gulp` _or_ `yarn global add gulp` _(Skip if you already have gulp installed)_

- `npm install` _or_ `yarn` _(Install dependencies)_

- `npm run hooks` _or_ `yarn run hooks` _(Install git hooks)_

- `npm run dev` _or_ `yarn run dev` _(Run dev environment)_

## Available gulp tasks
`gulp build` - compile production ready files (minified & autoprefixed)  
`gulp php` - (default task) run the server and watch for files changes  
`gulp proxy` - run BrowserSync proxying a local url, you need to modify `browserSyncProxy` setting
`gulp compile` - build the full website using the twig to html compiler.

## Site Data

The site data is in the `context` folder, you can add data globally or page specific.  

To add data globally follow the same structure as in `site.php` and require the file inside `context.php`.  

To add data just for a specific page create a new file with the same name as the `route` and follow the same structure as in `home.php`.

## BEM Linter

A BEM (Block Element Modifier) linter has been setup and will run when the `scss` code is compiled. Add your __blocks (components)__ inside the `scss/blocks` folder and __utilities__ inside `scss/utils` folder. You can also enable the linter on files outside of this default folders by adding the comment `/** @define my-component */` at the top of the file. For more info about how to define components and utilities check the [documentation](https://github.com/postcss/postcss-bem-linter#define-componentsutilities-with-a-comment).

## Versioning / Caching

A git hook is added when setting up the boilerplate for the first time that will create a file with the _build version_ every time a new commit is made.

If you are cloning this repository after the boilerplate was setup or you need to set the hook again, run the task `gulp copy_git_pre_commit_hook` to copy the hook to your git hooks folder, next time you make a new commit the build version will be updated.

## Compile Twig to HTML

`php html-compiler.php`  

#### Options
- `--url=` - the url or path of the website. Default is __'\'__.  
- `--template-url=` - the url or path of the template. Default is the same as the __url__.  
- `--output-dir=` - where to output the compiled html files. Deafult is `public`.  
- `--page-in-subfolder=` (bool) - set to __true__ if you would like each page to be placed in a subfolder, example _public/about/index.html_ instead of _public/about.html_.  

---

Created by [Lab19](https://lab19.dev/).
