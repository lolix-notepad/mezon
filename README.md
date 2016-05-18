# Mezon PHP Framework
##Intro##
Mezon is a simple php framework wich will help you to create administrative consoles for your projects.
##Installation##
Go to your web root directory and use:
```bash
mkdir mezon
cd mezon
git init
git remote add mezon <your fork of the mezon project>
```

##First "Hello world" application##
It's very simple to create your own "Hello world" application.

Open %mezon-dir%/index.php file and write this simple code (it can be found in %mezon-path%/doc/examples/hello-world/index.php):
```PHP
require_once( './mezon.php' );

class           HelloWorldApplication extends Application
{
    /**
    *   Main page.
    */
    public function action_index()
    {
        return( 'Hello world!' );
    }
}

$App = new HelloWorldApplication();
$App->run();
```

That's all!

But lets look at this code once again. 

Here we can see Mezon php files.

```PHP
require_once( './mezon.php' );
```

We also can see HelloWorldApplication wich is derived from the Application class.

```PHP
class           HelloWorldApplication extends Application
{
    // ...
}
```
Application class is the most important class of the Mezon framework. In your every application you will override a part of it's methods.

In this example we override only one method - action_index wich draws main page of your application.

```PHP
public function action_index()
{
    return( 'Hello world!' );
}
```

What else can we see? Each page drawing method (or 'view') must return generated content wich will be used for page renderring functions of the Application class and actual template engine.

Then we create the instance of our application and running it.

```PHP
$App = new HelloWorldApplication();
$App->run();
```

##More complex example of the 'Hello world' application##

Lets imagine that we are creating a simple web site. So here is our application (it can be found in %mezon-path%/doc/examples/simple-site/index.php):

```PHP
class           SiteApplication extends Application
{
    /**
    *   Main page.
    */
    public function action_index()
    {
        return( 'This the main page of our simple site!' );
    }

    /**
    *   Contacts page.
    */
    public function action_contacts()
    {
        return( 'This the "Contacts" page' );
    }
}

$App = new SiteApplication();
$App->run();
```

In this example we can see the main page, wich is rendered by public function action_index(). It can be accessed by example.com/ URL or example.com/index/ URL.

And the contacts page, wich is rendered by public function action_contacts(). It can be accessed by example.com/contacts/ URL.

Quite simple, yeah? )

##Routing##

Now it is time to go deeper and find out how routes are working?

###Simple routes###

Example fot this paragraph can be found in %mezon-path%/doc/examples/router/index.php

To be hounest you have already used it. But it was called implicitly.

Router allows you to map URLs on your php code and call when ever it needs to be calld.

Router supports simple routes like in the example above - example.com/contacts/

Each Application object implicity creates routes for it's 'action_[action-name]' methods, where 'action-name' will be stored as a route. Here is small (au usual)) ) example:

```PHP
class           MySite
{
    /**
    *   Main page.
    */
    public function action_index()
    {
        return( 'This is the main page of our simple site' );
    }

    /**
    *   Contacts page.
    */
    public function action_contacts()
    {
        return( 'This is the "Contacts" page' );
    }

    /**
    *   Some custom action handler.
    */
    public function some_other_page()
    {
        return( 'Some other page of our site' );
    }
}
```

And this code

```PHP
$Router = new Router();
$Router->fetch_actions( $MySite = new MySite() );
```

will create router object and loads information about it's actions and create routes. Strictly it will create two routes, because the class MySite has only two methods wich start wth 'action_prefix'. Method 'some_other_page' will not be converted into route automatically.

But we can still use this method as a route handler:

```PHP
$Router->add_route( 'some_any_other_route' , array( $MySite , 'some_other_page' ) );
```

We just need to create it explicitly.

We can also use simple functions for route creation:

```PHP
function        sitemap()
{
    return( 'Some fake sitemap' );
}

$Router->add_route( 'sitemap' , 'sitemap' );
```

###One handler for all routes###

You can specify one processor for all routes like this:

```PHP
$Router->add_route( '*' , function(){} );
```

Note that routing search will stops if the '*' handler will be found. For example:

```PHP
$Router->add_route( '*' , function(){} );
$Router->add_route( '/index/' , function(){} );
```

In this example route /index/ will never be reached. All request will be passed to the '*' handler. But in this example:

```PHP
$Router->add_route( '/contacts/' , function(){} );
$Router->add_route( '*' , function(){} );
$Router->add_route( '/index/' , function(){} );
```

route /contacts/ will be processed by it's own handler, and all other routes (even /index/) will be processed by the '*' handler.

###Request types and first steps to the REST API###

You can bind handlers to different request types as shown bellow:

```PHP
$Router->add_route( '/contacts/' , function(){} , 'POST' ); // this handler will be called for POST requests
$Router->add_route( '/contacts/' , function(){} , 'GET' );  // this handler will be called for GET requests
$Router->add_route( '/contacts/' , function(){} , 'PUT' );  // this handler will be called for PUT requests
$Router->add_route( '/contacts/' , function(){} , 'DELETE' );  // this handler will be called for DELETE requests
```

##Configuration##

Mezon has it's own config. It can be accesed with a set of functions, wich are described below.

Getting access to the key in config can be done with get_config_value( $Route , $DefaultValue = false ) function. It returns config value with route $Route and return $DefaultValue if this key was not found. For example:

```PHP
$Value = get_config_value( 'res/images/favicon' , 'http://yoursite.com/res/images/favicon.ico' );

// or the same
$Value = get_config_value( array( 'res' , 'images' , 'favicon' ) , 'http://yoursite.com/res/images/favicon.ico' );
```

Setting values for the config key can be done by calling set_config_value( $Route , $Value ) or add_config_value( $Route , $Value ) function. The main difference between these two functions is that the first one sets scalar key, and the second one adds element to the array in config. Here is small example:

```PHP
set_config_value( 'res/value' , 'Value!' );
var_dump( get_config_value( 'res/value' ) ); // displays 'Value!' string

add_config_value( 'res/value' , 'Value 1!' );
add_config_value( 'res/value' , 'Value 2!' );
var_dump( get_config_value( 'res/value' ) ); // displays array( [0] => 'Value 1!' , [1] => 'Value 2!' ) array
```

That's all you need to know about config read/write.

##Database support##

Mezon is using PDO PHP extension, so the following databases are supported:

- CUBRID
- MS SQL Server
- Firebird
- IBM
- Informix
- MySQL
- MS SQL Server
- Oracle
- ODBC and DB2
- PostgreSQL
- SQLite
- 4D

PDO objects are wrapped with ProCrud class wich will help you to create simple CRUD routine.

For example:

```PHP
$DataConnection = array(
    'dns' => 'mysql:host=localhost;dbname=testdb' , 
    'user' => 'user' ,
    'password' => 'password'
);

$CRUD = new PdoCrud();
$CRUD->connect( $DataConnection );
// fetching fields id and title from table test_table where ids are greater than 12
$Records = $CRUD->select( 'id , title' , 'test_table' , 'id > 12' );
```

##Template engine##

###Loading resources###

Mezon has a simple storage wich stores CSS and JS files. When page is rendered, template engine accessing it and fetching files to put in the 'head' tag of the rendered page.

Storage is globally accessed. So any componen can add it's own resources to the page.

It can be done in this way:

```PHP
$TemplateResources = new TemplateResources(); // getting access to the global storage

$TemplateResources->add_css_file( './res/test.css' ); // additing CSS file
$TemplateResources->add_js_file( './include/js/test.js' ); // additing JS file

$TemplateResources->add_css_files( array( './res/test1.css' , './res/test2.css' ) ); // additing CSS files
$TemplateResources->add_js_files( array( './include/js/test1.js' , './include/js/test2.js' ) ); // additing JS files
```