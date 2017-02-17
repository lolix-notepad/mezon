<?php

    /**
    *   Router class.
    */
    class           Router
    {
        /**
        *   Single instance of the class.
        */
        private static           $Instance = false;

        /**
        *   Mapping of routes to their execution functions for GET requests.
        */
        private                  $GetRoutes;

        /**
        *   Mapping of routes to their execution functions for GET requests.
        */
        private                  $PostRoutes;

        /**
        *   Mapping of routes to their execution functions for PUT requests.
        */
        private                  $PutRoutes;

        /**
        *   Mapping of routes to their execution functions for DELETE requests.
        */
        private                  $DeleteRoutes;

        /**
        *   Singleton ñonstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }

            $this->GetRoutes = array();

            $this->PostRoutes = array();

            $this->PutRoutes = array();

            $this->DeleteRoutes = array();

            $_SERVER[ 'REQUEST_METHOD' ] = isset( $_SERVER[ 'REQUEST_METHOD' ] ) ? $_SERVER[ 'REQUEST_METHOD' ] : 'GET';
        }

        /**
        *   Transfoming the path of the route in the class name.
        */
        private function        get_class_name( $RoutePart )
        {
            $ClassName = strtoupper( substr( $RoutePart , 0 , 1 ) ).substr( $RoutePart , 1 );

            while( strpos( $ClassName , '-' ) !== false )
            {
                $Position = strpos( $ClassName , '-' );
                $ClassName = substr( $ClassName , 0 , $Position ).
                         strtoupper( substr( $ClassName , $Position + 1 , 1 ) ).
                         substr( $ClassName , $Position + 2 );
            }

            return( $ClassName );
        }

        /**
        *   Processing one component routes.
        *
        *   @example http://example.com/action-name/
        */
        public function        process_one_component_route( $Object , $Route )
        {
            if( method_exists( $Object , 'action_'.$Route[ 0 ] ) )
            {
                $MethodName = 'action_'.$Route[ 0 ];
                return( $Object->$MethodName() );
            }
            else
            {
                throw( new Exception( 'Illegal route : '.$_GET[ 'r' ] ) );
            }
        }

        /**
        *   Processing one component routes.
        *
        *   @example http://example.com/class-name/action-name/
        */
        public function        process_two_component_route( $Route )
        {
            if( file_exists( $MEZON_PATH.'/vendor/'.$Route[ 0 ].'.php' ) )
            {
                require_once( $MEZON_PATH.'/vendor/'.$Route[ 0 ].'.php' );
            }
            else
            {
                throw( new Exception( 'File : '.$Route[ 0 ].' not found' ) );
            }

            $ClassName = $this->get_class_name( $Route[ 0 ] );
            $Object = new $ClassName;

            if( method_exists( $Object , 'action_'.$Route[ 1 ] ) )
            {
                $MethodName = 'action_'.$Route[ 1 ];
                return( $Object->$MethodName() );
            }
            else
            {
                throw( new Exception( 'Method : '.$MethodName.' was not found in the class '.$ClassName ) );
            }
        }

        /**
        *   Method fetches actions from the objects and creates GetRoutes for them.
        */
        public function         fetch_actions( $Object )
        {
            $Methods = get_class_methods( $Object );

            foreach( $Methods as $i => $Method )
            {
                if( strpos( $Method , 'action_' ) === 0 )
                {
                    $Route = str_replace( array( 'action_' , '_' ) , array( '' , '-' ) , $Method );
                    $this->GetRoutes[ "/$Route/" ] = array( $Object , $Method );
                }
            }
        }

        /**
        *   Method adds route and it's handler.
        *
        *   $Callback function may have two parameters - $Route and $Parameters. Where $Route is a called route,
        *   and $Parameters is associative array (parameter name => parameter value) with URL parameters.
        */
        public function         add_route( $Route , $Callback , $Request = 'GET' )
        {
            $Route = '/'.trim( $Route , '/' ).'/';

            switch( $Request )
            {
                case( 'GET' ) : $this->GetRoutes[ $Route ] = $Callback; break;

                case( 'POST' ) : $this->PostRoutes[ $Route ] = $Callback; break;

                case( 'PUT' ) : $this->PutRoutes[ $Route ] = $Callback; break;

                case( 'DELETE' ) : $this->DeleteRoutes[ $Route ] = $Callback; break;

                default : throw( new Exception( 'Invalid request type '.$Request ) ); break;
            }
        }

        /**
        *   Method prepares route for the next processing.
        */
        private function        prepare_route( $Route )
        {
            if( is_array( $Route ) )
            {
                $Route = implode( '/' , $Route );
            }

            return( '/'.trim( $Route , '/' ).'/' );
        }

        /**
        *   Method searches route processor.
        */
        private function        find_static_route_processor( &$Processors , $Route )
        {
            foreach( $Processors as $i => $Processor )
            {
                // exact router or 'all router'
                if( $i == $Route || $i == '/*/' )
                {
					if( is_callable( $Processor ) )
					{
						// passing route path and parameters
						return( call_user_func( $Processor , $Route , array() ) );
					}
					else
					{
						throw( 
                            new Exception( 
                                "'".( get_class( $Processor[ 0 ] ) !== false ? get_class( $Processor[ 0 ] ).'::'.
                                    $Processor[ 1 ] : $Processor )."' must be callable entity"
                            )
                        );
					}
                }
            }

            return( false );
        }
        
        /**
        *   Method tries to process static routes without any parameters.
        */
        private function        try_static_toutes( $Route )
        {
            switch( $_SERVER[ 'REQUEST_METHOD' ] )
            {
                case( 'GET' ) : return( $this->find_static_route_processor( $this->GetRoutes , $Route ) );

                case( 'POST' ) : return( $this->find_static_route_processor( $this->PostRoutes , $Route ) );

                case( 'PUT' ) : return( $this->find_static_route_processor( $this->PutRoutes , $Route ) );

                case( 'DELETE' ) : return( $this->find_static_route_processor( $this->DeleteRoutes , $Route ) );
				
				default : throw( new Exception( 'Unsupported request method' ) );
            }

            return( false );
        }

        /**
        *   Method detects if the $String is a parameter or a static component of the route.
        */
        private function        is_parameter( $String )
        {
            return( $String[ 0 ] == '[' && $String[ strlen( $String ) - 1 ] == ']' );
        }

        /**
        *   Matching parameter and component.
        */
        private function        match_parameter_and_component( $Component , $Parameter )
        {
            // [i:some_id]
            $ParameterData = explode( ':' , trim( $Parameter , '[]' ) );

            switch( $ParameterData[ 0 ] )
            {
                case( 'i' ):
                    if( ctype_digit( $Component ) )
                    {
                        return( $ParameterData[ 1 ] );
                    }
                break;
                case( 'a' ):
                    //if( ctype_alnum( $Component ) )
                    if( preg_match( '/^([a-z0-9A-Z_\/-]+)$/' , $Component ) )
                    {
                        return( $ParameterData[ 1 ] );
                    }
                break;
                default : throw( new Exception( 'Illegal parameter type : '.$ParameterData[ 0 ] ) ); break;
            }

            return( false );
        }

        /**
        *   Method matches route and pattern.
        */
        private function        match_route_and_pattern( $CleanRoute , $CleanPattern )
        {
            if( count( $CleanRoute ) !== count( $CleanPattern) )
            {
                return( false );
            }

            $Paremeters = array();

            $Trace = '';

            for( $i = 0 ; $i < count( $CleanPattern ) ; $i++ )
            {
                $Trace .= $CleanPattern[ $i ].'/';

                if( $this->is_parameter( $CleanPattern[ $i ] ) )
                {
                    $ParameterName = $this->match_parameter_and_component( $CleanRoute[ $i ] , $CleanPattern[ $i ] );

                    // it's a parameter
                    if( $ParameterName !== false )
                    {
                        // parameter was matched, store it!
                        $Paremeters[ $ParameterName ] = $CleanRoute[ $i ];
                    }
                    else
                    {
                        return( false );
                    }
                }
                else
                {
                    // it's a static part of the route
                    if( $CleanRoute[ $i ] !== $CleanPattern[ $i ] )
                    {
                        return( false );
                    }
                }
            }

            return( $Paremeters );
        }

        /**
        *   Method searches dynamic route processor.
        */
        private function        find_dynamic_route_processor( &$Processors , $Route )
        {
            $CleanRoute = explode( '/' , trim( $Route , '/' ) );

            foreach( $Processors as $i => $Processor )
            {
                $CleanPattern = explode( '/' , trim( $i , '/' ) );

                if( ( $Parameters = $this->match_route_and_pattern( $CleanRoute , $CleanPattern ) ) !== false )
                {
                    return( call_user_func( $Processor , $Route , $Parameters ) ); // return result of the router
                }
            }

            return( false );
        }

        /**
        *   Method tries to process dynamic routes with parameters.
        */
        private function        try_dynamic_toutes( $Route )
        {
            switch( $_SERVER[ 'REQUEST_METHOD' ] )
            {
                case( 'GET' ) : return( $this->find_dynamic_route_processor( $this->GetRoutes , $Route ) );

                case( 'POST' ) : return( $this->find_dynamic_route_processor( $this->PostRoutes , $Route ) );

                case( 'PUT' ) : return( $this->find_dynamic_route_processor( $this->PutRoutes , $Route ) );

                case( 'DELETE' ) : return( $this->find_dynamic_route_processor( $this->DeleteRoutes , $Route ) );

				default : throw( new Exception( 'Unsupported request method' ) );
            }

            return( false );
        }

		/**
		*	Method rturns all available routes.
		*/
		private function		get_all_routes_trace()
		{
			return(
				( count( $this->GetRoutes ) ? 'GET:'.implode( ', ' , array_keys( $this->GetRoutes ) ).'; ' : '' ).
				( count( $this->PostRoutes ) ? 'POST:'.implode( ', ' , array_keys( $this->PostRoutes ) ).'; ' : '' ).
				( count( $this->PutRoutes ) ? 'PUT:'.implode( ', ' , array_keys( $this->PutRoutes ) ).'; ' : '' ).
				( count( $this->DeleteRoutes ) ? 'DELETE:'.implode( ', ' , array_keys( $this->DeleteRoutes ) ) : '' )
			);
		}

        /**
        *   Processing specified router.
        */
        public function         call_route( $Route )
        {
            $Route = $this->prepare_route( $Route );

            if( ( $Result = $this->try_static_toutes( $Route ) ) !== false )
            {
                return( $Result );
            }

            if( ( $Result = $this->try_dynamic_toutes( $Route ) ) !== false )
            {
                return( $Result );
            }

            throw( 
				new Exception( 
					'The processor was not found for the route '.$Route.' in '.
						$this->get_all_routes_trace()
				)
			);
        }

        /**
        *   Method clears router data.
        */
        public function         clear()
        {
            $this->GetRoutes = array();

            $this->PostRoutes = array();

            $this->PutRoutes = array();

            $this->DeleteRoutes = array();
        }
    }

?>