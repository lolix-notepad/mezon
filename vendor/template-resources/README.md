# Template resources class

##Intro##

This class allows you to include css and js files to the 'head' tag of your DOM from any place os your source code.

##Usage##

To use this class just create it's object:

```PHP
$Resources = new TemplateResources();
```

Then add CSS and JS files:

```PHP
$Resources->add_js_file( './include/js/file1.js' ); // one file
$Resources->add_js_files( array( './include/js/file1.js' , './include/js/file2.js' ) ); // or many files at one call
// and note that duplicate file file1.js will bi included into 'head' only once.

$Resources->add_css_file( './res/css/file1.css' ); // one file
$Resources->add_css_files( array( './res/css/file2.css' , './res/css/file3.css' ) ); // or many files at one call
```

Quite simple.

If you are using [Basic template](https://github.com/alexdodonov/mezon/tree/master/vendor/basic-template#basic-template-class) class or any other class derived from it, then you may operate with TemplateResources class and include resources anywere in your code.