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
        *   Singleton ñonstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }
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
        *   Processing specified router.
        */
        private function        call_route( $Object , $Route )
        {
            switch( count( $Route ) )
            {
                case( 1 ): 
                    return( $this->process_one_component_route( $Object , $Route ) );
                break;
                case( 2 ): 
                    return( $this->process_two_component_route( $Route ) );
                break;
                default:
                    throw( new Exception( 'Illegal route : '.$_GET[ 'r' ] ) );
                break;
            }
        }

        /**
        *   Processing prepared route.
        */
        public function         parse_route( $Object , $Route )
        {
            //TODO: implement in conf.php view class in /vendor/mezon-resources/
            if( count( $Route ) && $Route[ 0 ] == 'conf' )
            {
                return( get_config_value( array_slice( $Route , 1 ) ) );
            }

            return( $this->call_route( $Object , $Route ) );
        }
    }

?>