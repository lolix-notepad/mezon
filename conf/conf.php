<?php

    $MEZON_PATH = dirname( dirname( __FILE__ ) );
	define( 'MEZON_PATH' , dirname( dirname( __FILE__ ) ) );

    function            _expand_string( $Value )
    {
        global          $AppConfig;

        if( is_string( $Value ) )
        {
            $Value = str_replace( 
                array( '@app-http-path' , '@mezon-http-path' ) , 
                array( @$AppConfig[ '@app-http-path' ] , @$AppConfig[ '@mezon-http-path' ] ) , 
                $Value
            );
        }
        else
        {
            foreach( $Value as $FieldName => $FieldValue )
            {
                $Value[ $FieldName ] =  _expand_string( $FieldValue );
            }
        }

        return( $Value );
    }

    /**
    *   Function returns specified config key. If the key does not exists then $DefaultValue will be returned.
    */
    function            get_config_value( $Route , $DefaultValue = false )
    {
        global          $AppConfig;

        if( is_string( $Route ) )
        {
            $Route = explode( '/' , $Route );
        }

        if( isset( $AppConfig[ $Route[ 0 ] ] ) === false )
        {
            return( $DefaultValue );
        }

        $Value = $AppConfig[ $Route[ 0 ] ];

        for( $i = 1 ; $i < count( $Route ) ; $i++ )
        {
            if( isset( $Value[ $Route[ $i ] ] ) === false )
            {
                return( false );
            }

            $Value = $Value[ $Route[ $i ] ];
        }

        return( _expand_string( $Value ) );
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
            _set_config_value_rec( $AppConfig , $Route , $Value );
        }
        else
        {
            $AppConfig[ $Route[ 0 ] ] = $Value;
        }
    }

    function            _add_config_value_rec( &$Config , $Route , $Value )
    {
        if( isset( $Config[ $Route[ 0 ] ] ) === false )
        {
            $Config[ $Route[ 0 ] ] = array();
        }

        if( count( $Route ) > 1 )
        {
            _add_config_value_rec( $Config[ $Route[ 0 ] ] , array_slice( $Route , 1 ) , $Value );
        }
        elseif( count( $Route ) == 1 )
        {
            $Config[ $Route[ 0 ] ][] = $Value;
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
            _add_config_value_rec( $AppConfig , $Route , $Value );
        }
        else
        {
            $AppConfig[ $Route[ 0 ] ] = array( $Value );
        }
    }

	/**
	*	Validating key existance.
	*/
	function			config_key_exists( $Route )
	{
		global          $AppConfig;

        if( is_string( $Route ) )
        {
            $Route = explode( '/' , $Route );
        }

		// validating route
        $Value = $AppConfig[ $Route[ 0 ] ];

        for( $i = 1 ; $i < count( $Route ) ; $i++ )
        {
            if( isset( $Value[ $Route[ $i ] ] ) === false )
            {
                return( false );
            }

            $Value = $Value[ $Route[ $i ] ];
        }

		return( true );
	}

	/**
	*	Deleting config element.
	*/
	function			_delete_config( $RouteParts , &$ConfigPart )
	{
		if( count( $RouteParts ) == 1 )
		{
			// don't go deeper and delete the found staff
			unset( $ConfigPart[ $RouteParts[ 0 ] ] );
		}
		else
		{
			// go deeper
			_delete_config( array_splice( $RouteParts , 1 ) , $ConfigPart[ $RouteParts[ 0 ] ] );

			if( count( $ConfigPart[ $RouteParts[ 0 ] ] ) == 0 )
			{
				// remove empty parents
				unset( $ConfigPart[ $RouteParts[ 0 ] ] );
			}
		}
	}

	/**
	*	Deleting config value.
	*/
	function			delete_config_value( $Route )
	{
		global          $AppConfig;

		if( is_string( $Route ) )
        {
            $Route = explode( '/' , $Route );
        }

        if( config_key_exists( $Route ) === false )
		{
			return( false );
		}

		// route exists, so delete it
		_delete_config( $Route , $AppConfig );

		return( true );
	}

    set_config_value( '@app-http-path' , 'http://'.@$_SERVER[ 'HTTP_HOST' ].'/'.trim( @$_SERVER[ 'REQUEST_URI' ] , '/' ) );
    set_config_value( '@mezon-http-path' , 'http://'.@$_SERVER[ 'HTTP_HOST' ].'/'.trim( @$_SERVER[ 'REQUEST_URI' ] , '/' ) );

    set_config_value( 'res/images/favicon' , '@mezon-http-path/res/images/favicon.ico' );

    add_config_value( 'res/css' , '@mezon-http-path/res/css/application.css' );

?>