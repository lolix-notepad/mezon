<?php

    @session_start();

    $MEZON_PATH = dirname( dirname( __FILE__ ) );

    function            expand_string( $String )
    {
        global          $AppConfig;

        $String = str_replace( 
            array( '@app-http-path' , '@mezon-http-path' ) , 
            array( $AppConfig[ '@app-http-path' ] , $AppConfig[ '@mezon-http-path' ] ) , 
            $String
        );

        return( $String );
    }

    function            get_compiled_config_value( $Route )
    {
        global          $AppConfig;

        $Value = $AppConfig[ $Route[ 0 ] ];

        for( $i = 1 ; $i < count( $Route ) ; $i++ )
        {
            $Value = $Value[ $Route[ $i ] ];
        }

        if( is_array( $Value ) === false )
        {
            return( expand_string( $Value ) );
        }
        else
        {
            return( false );
        }
    }

    function            set_config_value_rec( &$Config , $Route , $Value )
    {
        if( count( $Route ) )
        {
            set_config_value_rec( $Config[ $Route[ 0 ] ] , array_slice( $Route , 1 ) , $Value );
        }
        else
        {
            $Config = $Value;
        }
    }

    function            set_config_value( $Route , $Value )
    {
        global          $AppConfig;

        $Route = explode( '/' , $Route );

        set_config_value_rec( $AppConfig[ $Route[ 0 ] ] , array_slice( $Route , 1 ) , $Value );
    }

    set_config_value( '@app-http-path' , 'http://'.$_SERVER[ 'HTTP_HOST' ] );
    set_config_value( '@mezon-http-path' , 'http://'.$_SERVER[ 'HTTP_HOST' ] );

    $AppConfig = array(
        'res' => array(
            'css' => array(
                '@mezon-http-path/res/css/application.css'
            ) , 
            'images' => array(
                'favicon' => '@framework-http-path/res/images/favicon.ico'
            )
        )
    );

?>