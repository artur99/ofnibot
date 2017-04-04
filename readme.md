# A9-Silex-Gen
## About
This is a base for an apache/ngnix server with PHP and a MySQL database. It uses Silex as a backend framework(loaded with PHP Composer) and tools like Bower and Grunt for the frontend.

## Fist configuration
For this app to run, you will need to have PHP composer installed. It is a tool just like npm, but for PHP libs. (Download: getcomposer.org)
```
$ npm install -g bower grunt-cli    // Global Bower and Grunt
$ npm update                        // Local package requirements
$ composer install                  // Silex frameworks and other tools
```
This will prepare the project. Then you will need to edit & set some things like the title, database, timezone, etc in the config file(`app/conf.yaml`).
Also, you will need to set apache to boot on `public_html/`, but the PHP should have access in the root (`./`) directory
## Usage
You can start adding routers, controllers and models to your website from the `app/` folder. There are already 2 demos set: for the `/` and `/demo/` routes, connected to the controllers and a `DemoModel`.

The HTML can be added from the `app/Templates/` folder. The system is using twig as a template engine. You can find that there are already 2 demo templates.

The custom CSS and JS (the one written by you) must be in the `src/css/` and `src/js/` folders. The file names are not a matter (excepting the `init.js` that will be the last one concated). 

For adding new JS/CSS libs (like jquery, materializecss, bootstrap, etc) you can install them with Bower:
```
$ bower install <package_name> --save
```
These packages will be downloaded in a temporary folder(ignored by gitignore), and then, when `grunt` command is runned, they will be taken, concated with the css & js from the `src/` directory, compressed, then moved to a public directory: `public_html/assets/components.js` & `public_html/assets/components.css`. They will be, then, availabel to the link:
`/assets/components.css` and `/assets/components.js`. So in the HTML, you should point to them like this:
`<link rel="stylesheet" href="/assets/components.css">`, and
`<script src="/assets/components.js"></script>`.
Grunt must be used after editing files from `src/` like this:
```
$ grunt
```
Or, you can also set a watcher like this:
```
$ grunt prep && grunt watch
```
but this won't compress the files, so you'll need to run `grunt` again before a deployment.

## Other
You can freely use it for your project. :)
It's not yet fully documented (there are some custom twig functions added), and not yet in the best stage of all, so I'll still need to work to make it look better.
