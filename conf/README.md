# Configuration
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
```