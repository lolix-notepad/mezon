# Base application class

##Intro##

All your applications will be derived from this class or will be using classes wich are siblings of this Application class.

##Functionality##

This class provieds:

- routing
- transformation action_[action name] methods into static routes /[action name]/
- loading routes from config file

###Loading routes from config file###

With time your application will grow and number of routes will increase. So we have provided convinient way to store all routes in a standalone confi file. So it is not necessary to initialize all routes in an Application (or any derived class) object's constructor.

Let's find out how you can use it.

First of all create config file ./conf/routes.php in your projects directory. It must look like this:

```PHP
return(
    array(
        array(
            'route' => '/news/' , // your route
            'callback' => 'display_news_line' // this must be the method name of your 
                                              // Application derived class
        ) , 
        array(
            'route' => '/news/[i:news_id]/' , // your route
            'callback' => 'display_exact_news' // this must be the method name of your 
                                               // Application derived class
        )
    )
);
```

Then just call Application::load_routes_from_config() method and it will load your ./conf/routes.php config.

You can also specify your own config file.

```PHP
$App->load_routes_from_config( './conf/my-config.php' );
```

For more functionality look [BasicApplication](https://github.com/alexdodonov/mezon/tree/master/vendor/basic-application#basic-application-class) class.