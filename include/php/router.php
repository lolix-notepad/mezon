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
        *   Mapping of routes to their execution functions.
        */
        private                  $Routes;

        /**
        *   Singleton ˝onstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }

            $this->Routes = array();
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
        *   Method fetches actions from the objects and creates routes for them.
        */
        public function         fetch_actions( $Object )
        {
            $Methods = get_class_methods( $Object );

            foreach( $Methods as $i => $Method )
            {
                if( strpos( $Method , 'action_' ) === 0 )
                {
                    $Route = str_replace( array( 'action_' , '_' ) , array( '' , '-' ) , $Method );
                    $this->Routes[ "/$Route/" ] = array( $Object , $Method );
                }
            }
        }

        /**
        *   Method adds route and it's handler.
        */
        public function         add_route( $Route , $Callback )
        {
            $Route = '/'.trim( $Route , '/' ).'/';

            $this->Routes[ $Route ] = $Callback;
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
        *   Method tries to process static routes without any parameters.
        */
        private function        try_static_toutes( $Route )
        {
            foreach( $this->Routes as $i => $Processor )
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
						throw( new Exception( "'$Processor' must be callable entity" ) );
					}
                }
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
                    if( ctype_alnum( $Component ) )
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
        *   Method tries to process dynamic routes with parameters.
        */
        private function        try_dynamic_toutes( $Route )
        {
            $CleanRoute = explode( '/' , trim( $Route , '/' ) );

            foreach( $this->Routes as $i => $Processor )
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
        *   Processing specified router.
        */
        public function        call_route( $Route )
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

            throw( new Exception( 'The processor was not found for the route '.$Route ) );
        }
    }

?>