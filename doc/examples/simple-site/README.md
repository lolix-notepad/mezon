# Simple site example

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