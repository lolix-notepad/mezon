# Template engine
##Intro##
Template engine provides template compilation routine with variables, loops and other programming abstractions.

##Loading resources##

Mezon has a simple storage wich stores CSS and JS files. When page is rendered, template engine accessing it and fetching files to put in the 'head' tag of the rendered page.

Storage is globally accessed. So any componen can add it's own resources to the page.

It can be done in this way:

```PHP
$TemplateResources = new TemplateResources(); // getting access to the global storage

$TemplateResources->add_css_file( './res/test.css' ); // additing CSS file
$TemplateResources->add_js_file( './include/js/test.js' ); // additing JS file

$TemplateResources->add_css_files( 
    array( './res/test1.css' , './res/test2.css' )
); // additing CSS files
$TemplateResources->add_js_files(
    array( './include/js/test1.js' , './include/js/test2.js' )
); // additing JS files
```

Resource storage is quite intilligent so you can't add many files with the same paths.

```PHP
$TemplateResources = new TemplateResources(); // getting access to the global storage

$TemplateResources->add_css_file( './res/test.css' ); // additing CSS file
$TemplateResources->add_css_file( './res/test.css' ); // no file will be added
```

But this way of additing resources is quite low level and it may be inconvinient for large number of resource files. So we have created assets. The documentation about it can be read [here](https://github.com/alexdodonov/mezon/tree/master/vendor/asset#assets-with-css-and-js-files)