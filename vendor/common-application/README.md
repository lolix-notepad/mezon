# Basic application class

##Intro##

This class provides simple aplication routine. Using this class you can create veri simple applications with the [basic template](https://github.com/alexdodonov/mezon/tree/master/vendor/basic-template#basic-template-class) wich looks like black text on white background.

It can be simply used for prototyping.

##Extended routes processing##

In [Application](https://github.com/alexdodonov/mezon/tree/master/vendor/application#base-application-class) class routes may return only strings. But BasicApplication class allows you to return arrays of string wich will be placed in the template placeholders.

Simple example:

```PHP
class           ExampleApplication extends CommonApplication
{
	/**
	*	Constructor.
	*/
	function			__construct( $Template )
	{
		parent::__construct( $Template );
	}

    function            action_simple_page()
    {
        return( 
            array( 
                'title' => 'Route title' , 
                'main' => 'Route main'
            )
        );
    }
}
```

Here route's handler generates two parts of the page /simple-page/ - 'title' and 'main'. These two part will be inserted into {title} and {main} placeholders respectively.

More complex example:

```PHP
class           ExampleApplication extends CommonApplication
{
	/**
	*	Constructor.
	*/
	function			__construct( $Template )
	{
		parent::__construct( $Template );
	}

    function            action_simple_page()
    {
        return( 
            array( 
                'title' => 'Route title' , 
                'main' => new View( 'Generated main content' )
            )
        );
    }
}
```

Here we pass instance of the class View (or any class derived from View) to the application page compilator. It will call View::render method wich must return compiled html content. See [here](https://github.com/alexdodonov/mezon/tree/master/vendor/view#base-view-class) more detatils about View class.