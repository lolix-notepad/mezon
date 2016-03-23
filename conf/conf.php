<?php

    @session_start();

    $MEZON_PATH = dirname( dirname( __FILE__ ) );

    function            _expand_string( $String )
    {
        global          $AppConfig;

        $String = str_replace( 
            array( '@app-http-path' , '@mezon-http-path' ) , 
            array( @$AppConfig[ '@app-http-path' ] , @$AppConfig[ '@mezon-http-path' ] ) , 
            $String
        );

        return( $String );
    }

    /**
    *   Function returns specified config key. If the key does not exists then $DefaultValue will be returned.
    */
    function            get_config_value( $Route , $DefaultValue = false )
    {
        global          $AppConfig;

        if( isset( $AppConfig[ $Route[ 0 ] ] ) === false )
        {
            return( $DefaultValue );
        }

        $Value = $AppConfig[ $Route[ 0 ] ];

        for( $i = 1 ; $i < count( $Route ) ; $i++ )
        {
            $Value = $Value[ $Route[ $i ] ];
        }

        if( is_array( $Value ) === false )
        {
            return( _expand_string( $Value ) );
        }
        else
        {
            return( false );
        }
    }

    function            _set_config_value_rec( &$Config , $Route , $Value )
    {
        if( isset( $Config[ $Route[ 0 ] ] ) === false )
        {
            $Config[ $Route[ 0 ] ] = array();
        }

        if( count( $Route ) > 1 )
        {
            _set_config_value_rec( $Config[ $Route[ 0 ] ] , array_slice( $Route , 1 ) , $Value );
        }
        elseif( count( $Route ) == 1 )
        {
            $Config[ $Route[ 0 ] ] = $Value;
        }
    }

    /**
    *   Function sets specified config key with value $Value.
    */
    function            set_config_value( $Route , $Value )
    {
        global          $AppConfig;

        $Route = explode( '/' , $Route );

        if( count( $Route ) > 1 )
        {
            _set_config_value_rec( @$AppConfig[ $Route[ 0 ] ] , array_slice( $Route , 1 ) , $Value );
        }
        else
        {
            $AppConfig[ $Route[ 0 ] ] = $Value;
        }
    }

    function            _add_config_value_rec( &$Config , $Route , $Value )
    {
        if( count( $Route ) )
        {
            _add_config_value_rec( @$Config[ $Route[ 0 ] ] , array_slice( $Route , 1 ) , $Value );
        }
        else
        {
            if( isset( $Config ) )
            {
                $Config = array();
            }
            if( is_array( $Config ) )
            {
                $Config [] = $Value;
            }
        }
    }

    /**
    *   Function adds specified value $Value into array with path $Route in the config.
    */
    function            add_config_value( $Route , $Value )
    {
        global          $AppConfig;

        $Route = explode( '/' , $Route );

        if( count( $Route ) > 1 )
        {
            _add_config_value_rec( @$AppConfig[ $Route[ 0 ] ] , array_slice( $Route , 1 ) , $Value );
        }
        else
        {
            $AppConfig[ $Route[ 0 ] ] = array( $Value );
        }
    }

    set_config_value( '@app-http-path' , 'http://'.@$_SERVER[ 'HTTP_HOST' ].'/'.trim( @$_SERVER[ 'REQUEST_URI' ] , '/' ) );
    set_config_value( '@mezon-http-path' , 'http://'.@$_SERVER[ 'HTTP_HOST' ].'/'.trim( @$_SERVER[ 'REQUEST_URI' ] , '/' ) );

    add_config_value( 'res/images/favicon' , '@mezon-http-path/res/images/favicon.ico' );
    add_config_value( 'res/css' , '@mezon-http-path/res/css/application.css' );

?>