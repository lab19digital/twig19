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
