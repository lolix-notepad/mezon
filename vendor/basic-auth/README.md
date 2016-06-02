# HTTP Basic authentication
##Intro##
You can use HTTP Basic Authentication in your applications out of the box.

##Preparations##

First create an object of class BasicAuth

```PHP
// here we use only only one user in your application
$Auth = new BasicAuthPublic( 'Admin console' , array( 'login' =>'admin' , 'password' => '1234567' ) );
```

But we want to add more than one user. And we can do it!

```PHP
$Auth = new BasicAuthPublic( 
    'Admin console' , 
    array( 
        array( 'login' =>'admin' , 'password' => '1234567' ) , 
        array( 'login' => 'manager' , 'password' => '7654321' )
    )
);
```

##Login form##

To display login form just call the method login_form as shown below:

```PHP
$Auth->login_form();
```

And it will displays standart browser login form with the string 'Admin console' in its title.

Now put everythong together:

```PHP
$Auth = new BasicAuthPublic( 'Admin console' , array( 'login' =>'admin' , 'password' => '1234567' ) );

$Auth->login_form();
```

##Logout##

Logging out is also very easy:

```PHP
$Auth->logout();
```