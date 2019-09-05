# Quick start

For watching and compiling source files. 

`npm install -g gulp` or `yarn global add gulp` (You can skip this step if you already have gulp installed)

`npm install` or `yarn`

# Available gulp tasks
`gulp build` - compile production ready files (minified & autoprefixed)  
`gulp php` - (default task) run the server and watch for files changes  
`gulp proxy` - run BrowserSync proxying a local url, you need to modify `browserSyncProxy` setting

# Site Data

The site data is in the `context` folder, you can add data globally or page specific.  

To add data globally follow the same structure as in `site.php` and require the file inside `context.php`.  

To add data just for a specific page create a new file with the same name as the `route` and follow the same structure as in `home.php`.

# Compile Twig to HTML

`php html-compiler.php`  

### Options
- `--url=` - The url or path of the website. Default is __'\'__.  
- `--template-url=` - The url or path of the template. Default is the same as the __url__.  
- `--output-dir=` - Where to output the compiled html files. Deafult is the current directory.  
