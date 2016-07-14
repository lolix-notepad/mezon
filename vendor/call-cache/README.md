# Function calls caching
##Intro##
You can cache results of any slow function into cache. To do this use CallCache class.

##Examples##
This class is very simple to use. Just put data in cache:

```PHP
CallCache::put( 'function_name' , 'data to store' , $FunctionParam1 , $FunctionParam2 );
```

Here we can see that all cache data is grouped into scopes wich depends on function name and it's parameters.

```PHP
function        foo( $Param1 , $Param2 )
{
    // ...

    CallCache::put( 'foo' , 'data to store' , $Param1 , $Param2 );
    
    // ...
}

// data will be cached in differrent storages
foo( 1 , 2 );
foo( 1 , 3 );
```

CallCache put method supports methods up to 5 parameters.

After you have stored data, you can access it by 'get' method.

```PHP
CallCache::put( 'function_name' , 'data to store' , $FunctionParam1 , $FunctionParam2 );

// 'data to store' will be printed
var_dump( CallCache::get( 'function_name' , $FunctionParam1 , $FunctionParam2 ) );

// no data was stored for this function. false will be printed
var_dump( CallCache::get( 'another_function_name' , $FunctionParam1 , $FunctionParam2 ) );
```

##Full example##

```PHP
function        foo( $Param1 , $Param2 )
{
    if( ( $Result = CallCache::get( 'foo' , $Param1 , $Param2 ) ) !== false )
    {
        return( $Result );
    }

    $Result = 'data to store';

    CallCache::put( 'foo' , $Result , $Param1 , $Param2 );

    return( $Result );
}
```

[Back to framework's system level description](https://github.com/alexdodonov/mezon#system-level)