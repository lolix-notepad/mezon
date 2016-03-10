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

##First "Hello world" application
It's very simple to create your own "Hello world" application.

Open %mezon-dir%/index.php file and write this simple code:
```PHP
require_once( './mezon.php' );

class           HelloWorldoApplication extends Application
{
    /**
    *   Main page.
    */
    public function action_index()
    {
        return( 'Hello world!' );
    }
}

$App = new HelloWorldoApplication();
$App->run();
```

That's all!

But lets look at this code once again. 

Here we can see Mezon php files.

```PHP
require_once( './mezon.php' );
```

We also can see HelloWorldoApplication wich is derived from the Application class.

```PHP
class           HelloWorldoApplication extends Application
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
$App = new HelloWorldoApplication();
$App->run();
```